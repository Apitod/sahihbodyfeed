<?php

namespace Database\Seeders;

use App\Enums\AgentStatus;
use App\Enums\UserRole;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Admin
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password'  => Hash::make('password'),
                'role'      => UserRole::Admin,
                'is_active' => true,
            ]
        );
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // 2. Create Default Root Agent (For Referrals)
        $rootAgentUser = User::firstOrCreate(
            ['username' => 'agent01'],
            [
                'password'  => Hash::make('password'),
                'role'      => UserRole::Agent,
                'is_active' => true,
            ]
        );
        if (!$rootAgentUser->hasRole('agent')) {
            $rootAgentUser->assignRole('agent');
        }

        Agent::firstOrCreate(
            ['user_id' => $rootAgentUser->id],
            [
                'nama'          => 'Agen Utama Sahih',
                'referral_code' => 'SBFROOT01', // Fixed code for the root/seed agent.
                'upline_id'     => null,         // Root agent, no upline.
                'phone'         => '08123456789',
                'total_points'  => 0,
                'status'        => AgentStatus::Agent,
                'joined_at'     => now(),
            ]
        );
    }
}
