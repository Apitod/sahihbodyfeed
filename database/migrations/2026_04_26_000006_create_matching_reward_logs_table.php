<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The most critical table in the system. Implements the deferred Matching Reward
     * logic from Flowchart 3 (Klaim Reward & Matching Reward).
     *
     * BUSINESS LOGIC RECAP — two possible cases when a downline claims a reward:
     *
     *   KASUS A (Sponsor HAS already claimed the same reward):
     *     → Immediately disburse 100% Matching Reward (reward_value) to the sponsor.
     *     → A row is inserted with status = 'paid' and paid_at = now().
     *
     *   KASUS B (Sponsor has NOT yet claimed the same reward):
     *     → Insert a row with status = 'PENDING'. Do NOT disburse yet.
     *     → TRIGGER: When the sponsor later gets THEIR OWN reward_claim approved
     *       for the same reward_id, the system queries this table:
     *           SELECT * FROM matching_reward_logs
     *           WHERE sponsor_id = ? AND reward_id = ? AND status = 'pending'
     *       ...and disburses all found rows, flipping status to 'paid'.
     *
     * Column notes:
     *   - sponsor_id  : The upline agent who RECEIVES the matching reward payout.
     *   - downline_id : The agent whose approved reward_claim TRIGGERED this log entry.
     *   - claim_id    : The specific reward_claims row that caused this log to be created.
     *   - amount      : Always equals reward.reward_value (100% match).
     *   - status      : enum('pending', 'paid') — replaces the ERD's `is_paid boolean`
     *                   to correctly model the PENDING state from Flowchart 3.
     */
    public function up(): void
    {
        Schema::create('matching_reward_logs', function (Blueprint $table) {
            $table->id();
            // The upline agent who will receive/has received the matching reward.
            $table->foreignId('sponsor_id')
                  ->constrained('agents')
                  ->cascadeOnDelete();
            // The downline agent whose approved claim triggered this log entry.
            $table->foreignId('downline_id')
                  ->constrained('agents')
                  ->cascadeOnDelete();
            // Which reward milestone triggered this matching reward.
            $table->foreignId('reward_id')
                  ->constrained('rewards')
                  ->cascadeOnDelete();
            // The specific reward_claims row that created this log entry.
            $table->foreignId('claim_id')
                  ->constrained('reward_claims')
                  ->cascadeOnDelete();
            // The matching payout amount — always 100% of reward.reward_value.
            $table->decimal('amount', 15, 2)->notNull();
            // PENDING = waiting for sponsor to claim their own same reward (Kasus B).
            // PAID    = matching reward has been disbursed (Kasus A or trigger fired).
            $table->enum('status', ['pending', 'paid'])->notNull()->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            /*
             * COMPOSITE INDEX — Critical for the Kasus B trigger query performance:
             *   WHERE sponsor_id = ? AND reward_id = ? AND status = 'pending'
             * This query fires every time a sponsor's reward_claim is approved,
             * so it MUST be indexed to avoid full-table scans as data grows.
             */
            $table->index(['sponsor_id', 'reward_id', 'status'], 'idx_pending_matching_lookup');

            // Additional index for auditing downline-centric views.
            $table->index('downline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_reward_logs');
    }
};
