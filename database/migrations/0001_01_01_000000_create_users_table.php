<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: The `role` column acts as a cached/sync column alongside Spatie Laravel-Permission.
     * This prevents expensive pivot-table joins for simple role checks (e.g. gate checks,
     * middleware guards). It MUST always be kept in sync when Spatie roles are assigned.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100)->unique()->notNull();
            $table->string('password', 255)->notNull();
            // Redundant role cache — kept in sync with Spatie roles for fast lookups.
            $table->enum('role', ['admin', 'agent'])->notNull()->default('agent');
            $table->boolean('is_active')->notNull()->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
