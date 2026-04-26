<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rewards are the configurable milestone tiers agents can claim.
     * Examples: 20pts → Rp5,000,000 (Supervisor), 100pts → Ass. Manager, 500pts → Manager.
     * This table must exist before `reward_claims` and `matching_reward_logs`.
     */
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->notNull();
            // Points threshold an agent must reach to be eligible to claim this reward.
            $table->unsignedInteger('required_points')->notNull();
            // Cash value disbursed to the agent upon a successful claim (e.g. Rp5,000,000).
            $table->decimal('reward_value', 15, 2)->notNull();
            $table->timestamps();

            // Ensure no two rewards share the same point threshold.
            $table->unique('required_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
