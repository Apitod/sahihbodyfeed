<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Add NIK column (max 16 digits, nullable) after 'alamat'.
            $table->string('nik', 16)->nullable()->after('alamat');

            // Drop the old KTP photo column to save storage.
            if (Schema::hasColumn('agents', 'foto_ktp')) {
                $table->dropColumn('foto_ktp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('nik');
            $table->string('foto_ktp')->nullable()->after('alamat');
        });
    }
};
