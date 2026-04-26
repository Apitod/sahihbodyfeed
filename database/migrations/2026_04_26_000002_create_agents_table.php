<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Central agent profile table. upline_id is nullable (self-referencing FK) to support
     * the root/top-level agent who has no sponsor above them.
     *
     * Status levels (from Flowchart 3 — Logika Perubahan Status):
     *   - agent      : default starting status
     *   - supervisor : unlocked at 20 total_points
     *   - ass_manager: unlocked at 100 total_points
     *   - manager    : unlocked at 500 total_points
     *
     * total_points accumulates +1 per verified Repeat Order (Flowchart 2).
     */
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->notNull();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            // Self-referencing FK: sponsor (upline). NULL = top-level agent (no sponsor).
            $table->foreignId('upline_id')
                  ->nullable()
                  ->constrained('agents')
                  ->nullOnDelete();
            $table->unsignedInteger('total_points')->notNull()->default(0);
            $table->enum('status', ['agent', 'supervisor', 'ass_manager', 'manager'])
                  ->notNull()
                  ->default('agent');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            // Guarantee one-to-one with users.
            $table->unique('user_id');
            // Speed up hierarchical upline traversal queries.
            $table->index('upline_id');
            // Common filter: list all agents at a given status tier.
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
