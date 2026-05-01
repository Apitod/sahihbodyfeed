<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AgentRegistrationService;
use App\Services\RepeatOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService,
        private readonly RepeatOrderService $repeatOrderService
    ) {}

    public function transactionsList()
    {
        // Simple query without data tables for MVP. Order pending first.
        $transactions = Transaction::with(['agent.user', 'verifier'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'verified' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(50);
            
        return view('admin.verifications.transactions', compact('transactions'));
    }

    /**
     * Show form for admin to create a Repeat Order on behalf of an agent.
     * Agent lookup is done by username.
     */
    public function createRepeatOrder(Request $request)
    {
        $amount = \App\Enums\TransactionType::RepeatOrder->amount();

        // If a username is submitted, resolve the agent for preview
        $agent = null;
        if ($username = $request->input('username')) {
            $agent = \App\Models\Agent::whereHas('user', fn ($q) => $q->where('username', $username))
                ->with('user')
                ->first();
        }

        return view('admin.verifications.create_ro', compact('amount', 'agent', 'username'));
    }

    /**
     * Admin submits a Repeat Order transaction on behalf of an agent (found by username).
     */
    public function storeRepeatOrder(Request $request)
    {
        $validated = $request->validate([
            'username'         => 'required|string|exists:users,username',
            'proof_of_payment' => 'required|image|max:2048',
        ]);

        $agent = \App\Models\Agent::whereHas('user', fn ($q) => $q->where('username', $validated['username']))
            ->with('user')
            ->first();

        if (! $agent) {
            return back()->withErrors(['username' => 'Agen dengan username tersebut tidak ditemukan.'])->withInput();
        }

        if (! $agent->user?->is_active) {
            return back()->withErrors(['username' => 'Akun agen ini tidak aktif.'])->withInput();
        }

        $path = $request->file('proof_of_payment')->store('payments', 'public');

        $this->repeatOrderService->submitOrder($agent, $path);

        return redirect()->route('admin.verifications.transactions')
            ->with('success', "Repeat Order untuk agen '{$agent->nama}' (@{$agent->user->username}) berhasil dibuat dan menunggu verifikasi.");
    }

    public function approveTransaction(Transaction $transaction, Request $request)
    {
        try {
            $adminUser = $request->user();
            
            if ($transaction->type === TransactionType::NewAgent) {
                // FC1 Branch
                $this->registrationService->approveRegistration($transaction, $adminUser);
                $message = "Registrasi Agen {$transaction->agent->nama} berhasil diverifikasi. Komisi telah dibagikan.";
            } elseif ($transaction->type === TransactionType::RepeatOrder) {
                // FC2 Branch
                $this->repeatOrderService->approveOrder($transaction, $adminUser);
                $message = "Repeat Order dari {$transaction->agent->nama} berhasil diverifikasi. Poin bertambah dan komisi dibagikan.";
            } else {
                throw new \Exception("Tipe transaksi tidak dikenali.");
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectTransaction(Transaction $transaction, Request $request)
    {
        try {
            $adminUser = $request->user();

            if ($transaction->type === TransactionType::NewAgent) {
                $this->registrationService->rejectRegistration($transaction, $adminUser);
            } elseif ($transaction->type === TransactionType::RepeatOrder) {
                $this->repeatOrderService->rejectOrder($transaction, $adminUser);
            }

            return back()->with('success', "Transaksi #{$transaction->id} berhasil ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
