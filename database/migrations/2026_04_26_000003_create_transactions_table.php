<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Financial transactions submitted by agents.
     *
     * type values (from Flowchart 1 & 2):
     *   - new_agent   : Initial registration payment of Rp2,650,000 (FC1)
     *   - repeat_order: Subsequent product order of Rp2,350,000 (FC2)
     *
     * status values:
     *   - pending  : Submitted, awaiting admin verification
     *   - verified : Admin approved — triggers commission distribution background task
     *   - rejected : Admin rejected — no commission distributed
     *
     * verified_by: FK to users.id (admin who verified). Nullable because it is
     * only populated after the admin action, not at submission time.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')
                  ->constrained('agents')
                  ->cascadeOnDelete();
            $table->enum('type', ['new_agent', 'repeat_order'])->notNull();
            $table->decimal('amount', 15, 2)->notNull();
            $table->enum('status', ['pending', 'verified', 'rejected'])
                  ->notNull()
                  ->default('pending');
            // Path/filename of the uploaded payment proof image.
            $table->string('proof_of_payment', 255)->nullable();
            // Admin who performed the verification action.
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // Common admin dashboard query: list all pending transactions.
            $table->index(['status', 'type']);
            // Fetch all transactions for one agent.
            $table->index('agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
