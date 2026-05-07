<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            // File path to the transfer proof uploaded by admin
            $table->string('transfer_proof')->nullable()->after('paid_at');
            // Optional admin notes on the payment
            $table->text('payment_notes')->nullable()->after('transfer_proof');
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn(['transfer_proof', 'payment_notes']);
        });
    }
};
