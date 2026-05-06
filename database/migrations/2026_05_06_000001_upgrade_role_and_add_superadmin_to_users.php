<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration 1 of 2: Upgrade user role enum to include 'superadmin'.
 *
 * SQLite does not support ALTER COLUMN for enum types, so we use a raw
 * DB statement approach that works cross-driver where possible.
 * For SQLite (dev environment), we check the driver and use the compatible path.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Drop old check constraint, add new one that includes 'superadmin'
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin','admin','agent'))");
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'agent') NOT NULL DEFAULT 'agent'");
        }
        // SQLite: TEXT column — no constraint needed.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Reassign any superadmin back to admin before reverting enum
            DB::statement("UPDATE users SET role = 'admin' WHERE role = 'superadmin'");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent') NOT NULL DEFAULT 'agent'");
        }
    }
};
