<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 1 – Commission Status Workflow.
     *
     * Business rule (Feature 3):
     *   Commissions are NOT immediately 'paid' at creation time.
     *   They enter 'menunggu' (waiting) on the day they are generated,
     *   then the 01:00 WITA cron accumulates them and moves them to 'pending',
     *   and finally admin disbursement marks them 'paid'.
     *
     * Status lifecycle:
     *   menunggu → pending → paid
     *
     * Migration strategy (SQLite + MySQL safe):
     *   SQLite stores ENUMs as plain strings — no CHECK constraint is enforced,
     *   so we only need to alter the DEFAULT value.
     *   For MySQL we rebuild the ENUM definition to include 'menunggu'.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // MySQL: redefine the ENUM to add the new case and update the default.
            DB::statement("
                ALTER TABLE commissions
                MODIFY COLUMN status ENUM('menunggu','pending','paid')
                NOT NULL DEFAULT 'menunggu'
            ");
        } else {
            // SQLite / PostgreSQL: the column is already a string — just change default.
            Schema::table('commissions', function (Blueprint $table) {
                $table->string('status')->default('menunggu')->change();
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("
                ALTER TABLE commissions
                MODIFY COLUMN status ENUM('pending','paid')
                NOT NULL DEFAULT 'pending'
            ");
        } else {
            Schema::table('commissions', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }
};
