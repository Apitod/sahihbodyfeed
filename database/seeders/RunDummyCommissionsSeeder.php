<?php

namespace Database\Seeders;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Agent;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CommissionDistributionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RunDummyCommissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(CommissionDistributionService $commissionService): void
    {
        $admin = User::where('username', 'admin')->first();
        
        // Find all dummy agents
        $agents = Agent::where('nama', 'like', 'Dummy Agent%')->get();

        $this->command->info("Processing commissions for " . $agents->count() . " dummy agents...");

        DB::transaction(function () use ($agents, $admin, $commissionService) {
            foreach ($agents as $agent) {
                // Check if they already have a new_agent transaction
                $exists = Transaction::where('agent_id', $agent->id)
                    ->where('type', TransactionType::NewAgent)
                    ->exists();

                if (!$exists) {
                    $transaction = Transaction::create([
                        'agent_id'         => $agent->id,
                        'type'             => TransactionType::NewAgent,
                        'amount'           => TransactionType::NewAgent->amount(),
                        'status'           => TransactionStatus::Verified,
                        'proof_of_payment' => 'dummy.jpg',
                        'verified_by'      => $admin ? $admin->id : 1,
                        'verified_at'      => now(),
                    ]);

                    // This will generate Gen-1, Gen-2, and Gen-3 commissions for the uplines
                    $commissionService->distribute($transaction);
                }
            }
        });

        $this->command->info("Commissions distributed successfully.");
    }
}
