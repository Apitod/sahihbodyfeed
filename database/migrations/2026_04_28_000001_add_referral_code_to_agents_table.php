<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add referral_code column to the agents table.
     *
     * referral_code is a short unique string (e.g. "SBFA1B2C3") that every agent
     * receives automatically on creation. This code is shared by an agent to recruit
     * new members. During registration the prospective agent enters this code to
     * link themselves as a downline (upline_id) of the referring agent.
     *
     * Format: "SBF" prefix + 6 random alphanumeric characters.
     *         Example: SBFHJ7K3
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Placed after upline_id for logical grouping with referral hierarchy fields.
            $table->string('referral_code', 20)
                  ->unique()
                  ->nullable()
                  ->after('upline_id');

            // Speed up lookups during registration validation.
            $table->index('referral_code');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropIndex(['referral_code']);
            $table->dropColumn('referral_code');
        });
    }
};
