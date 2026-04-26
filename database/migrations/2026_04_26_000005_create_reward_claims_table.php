<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tracks an agent's formal claim against a specific reward milestone.
     * System validates `agent.total_points >= reward.required_points` before approval.
     *
     * status values (from Flowchart 3):
     *   - pending  : Claim submitted, awaiting admin confirmation of point sufficiency.
     *   - approved : Points validated, cash (reward_value) disbursed to agent.
     *               Agent level is also auto-updated at this point.
     *   - rejected : Insufficient points or admin override.
     *
     * approved_by: FK to users.id (admin who approved/rejected). Nullable at submission.
     *
     * IMPORTANT BUSINESS RULE: When a claim is approved, the system MUST immediately
     * check for any PENDING matching_reward_logs where this agent is the `sponsor_id`
     * and the `reward_id` matches — and disburse those pending matching rewards.
     * This is the "Kasus B trigger" from Flowchart 3.
     */
    public function up(): void
    {
        Schema::create('reward_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')
                  ->constrained('agents')
                  ->cascadeOnDelete();
            $table->foreignId('reward_id')
                  ->constrained('rewards')
                  ->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->notNull()
                  ->default('pending');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // An agent should generally not have multiple approved claims for the same reward.
            // Using a standard index (not unique) to allow re-claims after rejection.
            $table->index(['agent_id', 'reward_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_claims');
    }
};
