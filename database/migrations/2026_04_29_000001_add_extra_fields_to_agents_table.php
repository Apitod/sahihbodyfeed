<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 1 – Agent Schema Overhaul.
     *
     * Changes:
     *   1. Rename `phone` → `no_telp`  (matches Indonesian business terminology).
     *   2. Add `alamat`             — full address text.
     *   3. Add `foto_ktp`           — stored path of the uploaded KTP image.
     *   4. Add `bank_name`          — bank name for commission payouts.
     *   5. Add `bank_account`       — account number.
     *   6. Add `bank_account_name`  — account holder name (may differ from agent nama).
     *
     * All new columns are nullable so existing rows are unaffected.
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // ── 1. Rename phone → no_telp ──────────────────────────────────
            // Laravel handles renaming the associated unique index automatically.
            $table->renameColumn('phone', 'no_telp');

            // ── 2. Address ─────────────────────────────────────────────────
            $table->text('alamat')->nullable()->after('no_telp');

            // ── 3. KTP Photo path ──────────────────────────────────────────
            $table->string('foto_ktp', 255)->nullable()->after('alamat');

            // ── 4–6. Bank data ─────────────────────────────────────────────
            $table->string('bank_name', 100)->nullable()->after('foto_ktp');
            $table->string('bank_account', 30)->nullable()->after('bank_name');
            $table->string('bank_account_name', 100)->nullable()->after('bank_account');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'alamat',
                'foto_ktp',
                'bank_name',
                'bank_account',
                'bank_account_name',
            ]);

            // Restore original column name.
            $table->renameColumn('no_telp', 'phone');
        });
    }
};
