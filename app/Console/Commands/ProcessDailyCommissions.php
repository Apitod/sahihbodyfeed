<?php

namespace App\Console\Commands;

use App\Enums\CommissionStatus;
use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ProcessDailyCommissions
 *
 * Scheduled at 01:00 WITA (Asia/Makassar = UTC+8).
 * Converts all yesterday's `menunggu` commissions → `pending`
 * so they appear in the admin's disbursement queue.
 *
 * Run manually:
 *   php artisan commissions:process-daily
 *
 * Scheduled via routes/console.php at 01:00 WITA every day.
 */
class ProcessDailyCommissions extends Command
{
    protected $signature   = 'commissions:process-daily
                                {--date= : Override the target date (Y-m-d). Defaults to yesterday.}
                                {--dry-run : Preview affected rows without updating.}';

    protected $description = 'Accumulate yesterday\'s menunggu commissions into pending status (runs at 01:00 WITA).';

    public function handle(): int
    {
        $timezone = 'Asia/Makassar'; // WITA

        // Resolve target date (previous calendar day in WITA).
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'), $timezone)->toDateString()
            : Carbon::now($timezone)->subDay()->toDateString();

        $this->info("Processing commissions for date: {$targetDate} (WITA)");

        // Find all menunggu commissions whose created_at falls on the target date.
        $query = Commission::where('status', CommissionStatus::Menunggu)
            ->whereDate('created_at', $targetDate);

        $count = $query->count();

        if ($count === 0) {
            $this->info('No menunggu commissions found for this date. Nothing to process.');
            Log::info("ProcessDailyCommissions: No menunggu commissions found for {$targetDate}.");
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("[DRY RUN] Would update {$count} commission(s) from menunggu → pending.");
            return self::SUCCESS;
        }

        // Bulk-update to pending inside a transaction.
        DB::transaction(function () use ($query, $count, $targetDate) {
            $query->update(['status' => CommissionStatus::Pending->value]);
        });

        $total = Commission::where('status', CommissionStatus::Pending)
            ->whereDate('updated_at', Carbon::now('Asia/Makassar')->toDateString())
            ->sum('amount');

        $this->info("✔ Updated {$count} commission(s) to 'pending'. Total amount: Rp " . number_format($total));

        Log::info(
            "ProcessDailyCommissions: {$count} commissions moved menunggu→pending for {$targetDate}. " .
            "Total: Rp " . number_format($total)
        );

        return self::SUCCESS;
    }
}
