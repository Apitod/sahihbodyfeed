<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add optional personal profile fields to the users table.
 *
 * These fields are primarily used for Admin (Tier-2) accounts created by Superadmin.
 * Agent accounts use the separate `agents` table for personal data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama', 120)->nullable()->after('username');
            $table->string('email', 120)->nullable()->unique()->after('nama');
            $table->string('no_telp', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'email', 'no_telp']);
        });
    }
};
