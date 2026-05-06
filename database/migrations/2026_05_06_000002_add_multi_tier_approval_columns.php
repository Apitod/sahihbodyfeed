<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration 2 of 2: Add multi-tier approval tracking columns.
 *
 * TRANSACTIONS table changes:
 *  - Add `verified_by_admin_id`         (FK → users, nullable) — Admin Tier-2 first-review
 *  - Rename `verified_by` → `verified_by_superadmin_id`
 *  - Expand status values: pending_superadmin, approved (was verified)
 *
 * REWARD_CLAIMS table changes:
 *  - Add `verified_by_admin_id`         (FK → users, nullable)
 *  - Rename `approved_by` → `approved_by_superadmin_id`
 *  - Expand status values likewise
 *
 * Supports: MySQL, MariaDB, PostgreSQL, SQLite
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // =====================================================================
        // TRANSACTIONS
        // =====================================================================

        // Correct order for Postgres:
        //   1. DROP old constraint (allows 'pending','verified','rejected')
        //   2. UPDATE data: 'verified' → 'approved'
        //   3. ADD new constraint (allows new set including 'approved','pending_superadmin')
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_status_check');
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // MySQL: expand enum BEFORE data update so no constraint violation
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending','pending_superadmin','approved','rejected','verified') NOT NULL DEFAULT 'pending'");
        }

        // Update legacy 'verified' rows to the new canonical 'approved'
        DB::statement("UPDATE transactions SET status = 'approved' WHERE status = 'verified'");

        // Now lock in the final constraint / enum
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_status_check CHECK (status IN ('pending','pending_superadmin','approved','rejected'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending','pending_superadmin','approved','rejected') NOT NULL DEFAULT 'pending'");
        }
        // SQLite: TEXT column — no constraint needed.

        // 3. Add the admin tracking column + FK
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by_admin_id')
                  ->nullable()
                  ->after('verified_by');

            $table->foreign('verified_by_admin_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });

        // 4. Rename verified_by → verified_by_superadmin_id
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('verified_by', 'verified_by_superadmin_id');
        });

        // =====================================================================
        // REWARD_CLAIMS
        // =====================================================================

        // 1. Expand status column
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reward_claims DROP CONSTRAINT IF EXISTS reward_claims_status_check');
            DB::statement("ALTER TABLE reward_claims ADD CONSTRAINT reward_claims_status_check CHECK (status IN ('pending','pending_superadmin','approved','rejected'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE reward_claims MODIFY COLUMN status ENUM('pending','pending_superadmin','approved','rejected') NOT NULL DEFAULT 'pending'");
        }

        // 2. Add the admin tracking column + FK
        Schema::table('reward_claims', function (Blueprint $table) {
            $table->unsignedBigInteger('verified_by_admin_id')
                  ->nullable()
                  ->after('approved_by');

            $table->foreign('verified_by_admin_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
        });

        // 3. Rename approved_by → approved_by_superadmin_id
        Schema::table('reward_claims', function (Blueprint $table) {
            $table->renameColumn('approved_by', 'approved_by_superadmin_id');
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        // =====================================================================
        // REWARD_CLAIMS (reverse)
        // =====================================================================
        Schema::table('reward_claims', function (Blueprint $table) {
            $table->renameColumn('approved_by_superadmin_id', 'approved_by');
            $table->dropForeign(['verified_by_admin_id']);
            $table->dropColumn('verified_by_admin_id');
        });

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reward_claims DROP CONSTRAINT IF EXISTS reward_claims_status_check');
            DB::statement("ALTER TABLE reward_claims ADD CONSTRAINT reward_claims_status_check CHECK (status IN ('pending','approved','rejected'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE reward_claims MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
        }

        // =====================================================================
        // TRANSACTIONS (reverse)
        // =====================================================================
        DB::table('transactions')->where('status', 'approved')->update(['status' => 'verified']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('verified_by_superadmin_id', 'verified_by');
            $table->dropForeign(['verified_by_admin_id']);
            $table->dropColumn('verified_by_admin_id');
        });

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_status_check');
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT transactions_status_check CHECK (status IN ('pending','verified','rejected'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending'");
        }
    }
};
