<?php

namespace Database\Seeders;

use App\Enums\AgentStatus;
use App\Enums\UserRole;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyHierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get agent01 (Root)
        $rootUser = User::where('username', 'agent01')->first();
        if (!$rootUser) {
            $this->command->error("User 'agent01' not found! Please run UserSeeder first.");
            return;
        }

        $rootAgent = $rootUser->agent;
        if (!$rootAgent) {
            $this->command->error("Agent profile for 'agent01' not found!");
            return;
        }

        $this->command->info("Starting to seed dummy hierarchy under agent01...");

        // Level 1: 5 agents
        $level1Agents = $this->createAgents(5, $rootAgent, 'l1');
        $this->command->info("Created 5 Level 1 Agents.");

        // Level 2: 25 agents (5 for each Level 1)
        $level2Agents = [];
        foreach ($level1Agents as $upline) {
            $created = $this->createAgents(5, $upline, 'l2');
            $level2Agents = array_merge($level2Agents, $created);
        }
        $this->command->info("Created 25 Level 2 Agents.");

        // Level 3: 125 agents (5 for each Level 2)
        $level3Agents = [];
        foreach ($level2Agents as $upline) {
            $created = $this->createAgents(5, $upline, 'l3');
            $level3Agents = array_merge($level3Agents, $created);
        }
        $this->command->info("Created 125 Level 3 Agents.");

        $this->command->info("Hierarchy seeded successfully.");
    }

    /**
     * Create multiple agents directly under a specified upline.
     */
    private function createAgents(int $count, Agent $upline, string $levelPrefix): array
    {
        $createdAgents = [];
        
        for ($i = 1; $i <= $count; $i++) {
            $uniqueStr = Str::lower(Str::random(5));
            $username = "agent_{$levelPrefix}_{$upline->id}_{$i}_{$uniqueStr}";
            
            $user = User::create([
                'username'  => $username,
                'password'  => Hash::make('password'),
                'role'      => UserRole::Agent,
                'is_active' => true,
            ]);
            
            if (!$user->hasRole('agent')) {
                $user->assignRole('agent');
            }

            $agent = Agent::create([
                'user_id'      => $user->id,
                'nama'         => "Dummy Agent " . strtoupper($levelPrefix) . " " . strtoupper($uniqueStr),
                'upline_id'    => $upline->id,
                'no_telp'        => '08' . mt_rand(100000000, 999999999),
                'total_points' => 0,
                'status'       => AgentStatus::Agent,
                'joined_at'    => now(),
            ]);

            $createdAgents[] = $agent;
        }

        return $createdAgents;
    }
}
