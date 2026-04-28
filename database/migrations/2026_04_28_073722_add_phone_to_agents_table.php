<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Placed after upline_id for logical grouping with referral hierarchy fields.
            $table->string('phone', 15)
                  ->unique()
                  ->nullable()
                  ->after('nama');

            // Speed up lookups during registration validation.
            $table->index('phone');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropColumn('phone');
        });
    }
};
