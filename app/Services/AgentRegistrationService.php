<?php

namespace App\Services;

use App\Enums\AgentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Exceptions\InvalidTransactionStateException;
use App\Models\Agent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * AgentRegistrationService
 *
 * Implements Flowchart 1 — "Registrasi Agen Baru".
 *
 * FLOW:
 *   1. submitRegistration() — Prospective agent submits personal data + payment proof.
 *      Creates an INACTIVE User + Agent record, plus a PENDING transaction.
 *      The agent cannot log in at this point (is_active = false).
 *
 *   2. approveRegistration() — Admin verifies payment is valid.
 *      Activates the user account, assigns Spatie roles, sets joined_at,
 *      then fires CommissionDistributionService to walk the upline chain.
 *
 *   3. rejectRegistration() — Admin finds payment invalid.
 *      Marks the transaction as rejected. The user stays inactive.
 *      The admin can request re-submission (handled at the controller layer).
 *
 * NOTE: submitRegistration() is called before the agent has an ID. Since the
 * ERD requires agent_id NOT NULL on transactions, we create the inactive
 * Agent record first, then immediately attach the pending transaction to it.
 */
class AgentRegistrationService
{
    public function __construct(
        private readonly CommissionDistributionService $commissionService,
    ) {}

    /**
     * Step 1 — Submit a new registration request.
     *
     * Creates:
     *   - User (is_active=false, role=agent)
     *   - Agent (upline_id = referring agent, status=agent, total_points=0)
     *   - Transaction (type=new_agent, status=pending, amount=Rp2,650,000)
     *
     * @param array $data {
     *   @type string   $username         Unique login name for the new user.
     *   @type string   $password         Plain-text password (will be hashed).
     *   @type string   $nama             Agent's full name.
     *   @type string   $proof_of_payment File path/name of payment proof upload.
     *   @type int|null $referral_agent_id ID of the sponsoring agent (upline_id). Null = no sponsor.
     * }
     * @return Transaction The newly created pending transaction.
     */
    public function submitRegistration(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            // 1. Create the user account (inactive until payment is verified).
            $user = User::create([
                'username'  => $data['username'],
                'password'  => Hash::make($data['password']),
                'role'      => UserRole::Agent,
                'is_active' => false,
            ]);

            // 2. Create the agent profile linked to this user.
            $agent = Agent::create([
                'user_id'      => $user->id,
                'nama'         => $data['nama'],
                'phone'        => $data['phone'] ?? null,
                'upline_id'    => $data['referral_agent_id'] ?? null,
                'total_points' => 0,
                'status'       => AgentStatus::Agent,
                'joined_at'    => null, // Set on approval, not submission.
            ]);

            // 3. Create the pending registration transaction.
            $transaction = Transaction::create([
                'agent_id'          => $agent->id,
                'type'              => TransactionType::NewAgent,
                'amount'            => TransactionType::NewAgent->amount(),
                'status'            => TransactionStatus::Pending,
                'proof_of_payment'  => $data['proof_of_payment'] ?? null,
            ]);

            Log::info("AgentRegistration: New registration submitted — User[{$user->id}] Agent[{$agent->id}] Transaction[{$transaction->id}].");

            return $transaction;
        });
    }

    /**
     * Step 2 — Admin approves the registration (Pembayaran Valid = Ya).
     *
     * FC1 path: Admin Verifikasi → Pembayaran Valid? → Ya →
     *   Create User Role: Agent → Background Task: Distribusi Komisi → END
     *
     * @param Transaction $transaction The pending new_agent transaction to approve.
     * @param User        $adminUser   The admin performing the verification.
     * @return Agent The now-active agent.
     * @throws InvalidTransactionStateException If the transaction is not in a pending new_agent state.
     */
    public function approveRegistration(Transaction $transaction, User $adminUser): Agent
    {
        // Guard: only pending new_agent transactions can be approved here.
        if ($transaction->type !== TransactionType::NewAgent) {
            throw new InvalidTransactionStateException(
                $transaction,
                "approveRegistration() hanya untuk transaksi bertipe 'new_agent'. Tipe diterima: {$transaction->type->value}."
            );
        }

        if ($transaction->status !== TransactionStatus::Pending) {
            throw new InvalidTransactionStateException($transaction);
        }

        return DB::transaction(function () use ($transaction, $adminUser) {
            // 1. Mark transaction as verified.
            $transaction->update([
                'status'      => TransactionStatus::Verified,
                'verified_by' => $adminUser->id,
                'verified_at' => now(),
            ]);

            // 2. Activate the agent's user account.
            $agent = $transaction->agent()->with('user')->firstOrFail();
            $agent->user->update(['is_active' => true]);

            // 3. Set agent's join timestamp.
            $agent->update(['joined_at' => now()]);

            // 4. Assign Spatie role 'agent' (keeps role cache column in sync).
            $agent->user->assignRole('agent');

            // 5. Fire commission distribution background task (FC1: Distribusi Komisi).
            // Walks up to 3 upline generations and creates Commission records.
            $commissions = $this->commissionService->distribute($transaction);

            Log::info("AgentRegistration: Approved — Agent[{$agent->id}] activated. " . count($commissions) . " commission(s) distributed.");

            return $agent->fresh();
        });
    }

    /**
     * Step 3 — Admin rejects the registration (Pembayaran Valid = Tidak).
     *
     * FC1 path: Admin Verifikasi → Pembayaran Valid? → Tidak → Reject / Minta Ulang Input
     *
     * The user account stays inactive. The agent can resubmit with a new proof
     * of payment — handled at the controller layer (reuse submitRegistration() or
     * allow re-upload and create a new transaction linked to the same agent).
     *
     * @param Transaction $transaction The pending new_agent transaction to reject.
     * @param User        $adminUser   The admin performing the rejection.
     */
    public function rejectRegistration(Transaction $transaction, User $adminUser): void
    {
        if ($transaction->type !== TransactionType::NewAgent) {
            throw new InvalidTransactionStateException(
                $transaction,
                "rejectRegistration() hanya untuk transaksi bertipe 'new_agent'."
            );
        }

        if ($transaction->status !== TransactionStatus::Pending) {
            throw new InvalidTransactionStateException($transaction);
        }

        DB::transaction(function () use ($transaction, $adminUser) {
            $transaction->update([
                'status'      => TransactionStatus::Rejected,
                'verified_by' => $adminUser->id,
                'verified_at' => now(),
            ]);

            Log::warning("AgentRegistration: Rejected — Transaction[{$transaction->id}] by Admin[{$adminUser->id}].");
        });
    }
}
