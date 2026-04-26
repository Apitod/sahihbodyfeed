<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Maps to Flowchart 3 (20 Point -> Supervisor, dll)
     */
    public function run(): void
    {
        $rewards = [
            [
                'name'            => 'Supervisor',
                'required_points' => 20,
                'reward_value'    => 5_000_000,
            ],
            [
                'name'            => 'Ass. Manager',
                'required_points' => 100,
                'reward_value'    => 25_000_000, 
            ],
            [
                'name'            => 'Manager',
                'required_points' => 500,
                'reward_value'    => 100_000_000, 
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::firstOrCreate(
                ['required_points' => $reward['required_points']],
                [
                    'name'         => $reward['name'],
                    'reward_value' => $reward['reward_value'],
                ]
            );
        }
    }
}
