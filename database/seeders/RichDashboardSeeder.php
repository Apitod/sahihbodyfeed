<?php

namespace Database\Seeders;

use App\Enums\CommissionStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Agent;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CommissionDistributionService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RichDashboardSeeder extends Seeder
{
    public function run(CommissionDistributionService $commissionService): void
    {
        $admin = User::where('username', 'admin')->first();
        if (!$admin) {
            $admin = User::first();
        }

        // Get some agents
        $agents = Agent::inRandomOrder()->limit(50)->get();

        if ($agents->isEmpty()) {
            $this->command->error("No agents found. Please run DummyHierarchySeeder first.");
            return;
        }

        $this->command->info("Generating historical transactions and commissions...");

        DB::transaction(function () use ($agents, $admin, $commissionService) {
            // Generate data for the last 6 months
            $startDate = Carbon::now()->subMonths(5)->startOfMonth();
            $endDate = Carbon::now();

            // Total transactions to generate
            $totalTransactions = 150;

            for ($i = 0; $i < $totalTransactions; $i++) {
                $agent = $agents->random();
                
                // Random date between start and end
                $randomTimestamp = rand($startDate->timestamp, $endDate->timestamp);
                $createdAt = Carbon::createFromTimestamp($randomTimestamp);
                
                // 80% Repeat Order, 20% New Agent
                $type = (rand(1, 100) <= 80) ? TransactionType::RepeatOrder : TransactionType::NewAgent;
                
                // Most are verified, some are pending
                $status = (rand(1, 100) <= 85) ? TransactionStatus::Approved : TransactionStatus::Pending;
                
                $transaction = Transaction::create([
                    'agent_id'         => $agent->id,
                    'type'             => $type,
                    'amount'           => $type->amount(),
                    'status'           => $status,
                    'proof_of_payment' => 'dummy_proof_' . $i . '.jpg',
                    'verified_by_superadmin_id'      => ($status === TransactionStatus::Approved) ? $admin->id : null,
                    'verified_at'      => ($status === TransactionStatus::Approved) ? $createdAt->copy()->addHours(rand(1, 48)) : null,
                    'created_at'       => $createdAt,
                    'updated_at'       => $createdAt,
                ]);

                // Distribute commissions if verified
                if ($status === TransactionStatus::Approved) {
                    $commissionService->distribute($transaction);
                    
                    // Update created_at for the generated commissions to match transaction date
                    $commissions = Commission::where('transaction_id', $transaction->id)->get();
                    
                    foreach ($commissions as $comm) {
                        // Randomly set commission status based on how old it is
                        $commStatus = CommissionStatus::Paid;
                        $paidAt = $createdAt->copy()->addDays(rand(1, 5));
                        
                        // If it's very recent, it might be pending or menunggu
                        if ($createdAt->isCurrentMonth()) {
                            $randStat = rand(1, 100);
                            if ($randStat <= 30) {
                                $commStatus = CommissionStatus::Menunggu;
                                $paidAt = null;
                            } elseif ($randStat <= 60) {
                                $commStatus = CommissionStatus::Pending;
                                $paidAt = null;
                            }
                        }

                        $comm->update([
                            'status'     => $commStatus,
                            'paid_at'    => $paidAt,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                            'transfer_proof' => ($commStatus === CommissionStatus::Paid) ? 'dummy_transfer.jpg' : null,
                        ]);
                    }
                }
            }
        });

        $this->command->info("Rich dummy data generated successfully.");
    }
}
