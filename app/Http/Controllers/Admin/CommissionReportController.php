<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommissionReportController extends Controller
{
    /**
     * Display a paginated list of commissions for the admin to review.
     * Allows filtering by status (Menunggu, Pending, Paid).
     */
    public function index(Request $request)
    {
        $status = $request->input('status', CommissionStatus::Pending->value);

        $commissions = Commission::with(['recipient', 'transaction.agent'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        // Calculate totals for pending commissions
        $totalPending = Commission::where('status', CommissionStatus::Pending)->sum('amount');
        $countPending = Commission::where('status', CommissionStatus::Pending)->count();

        $statuses = CommissionStatus::cases();

        return view('admin.commissions.index', compact('commissions', 'status', 'statuses', 'totalPending', 'countPending'));
    }

    /**
     * Generate and download a PDF report of commissions based on status.
     */
    public function downloadPdf(Request $request)
    {
        $status = $request->input('status');

        $query = Commission::with(['recipient.user'])->orderBy('recipient_id');
        
        if ($status) {
            $query->where('status', $status);
        }

        $commissions = $query->get();

        if ($commissions->isEmpty()) {
            return back()->with('error', 'Tidak ada komisi untuk dicetak pada status ini.');
        }

        // Group by recipient for the report
        $grouped = $commissions->groupBy('recipient_id');
        $date = now()->timezone('Asia/Makassar')->format('d M Y H:i');
        
        $statusLabel = $status ? ucfirst($status) : 'Semua';

        $pdf = Pdf::loadView('admin.commissions.report_pdf', compact('grouped', 'date', 'statusLabel'));
        
        return $pdf->download('laporan_komisi_' . strtolower($statusLabel) . '_' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Mark all currently "pending" commissions as "paid".
     */
    public function markAsPaid()
    {
        $count = Commission::where('status', CommissionStatus::Pending)->count();

        if ($count === 0) {
            return back()->with('error', 'Tidak ada komisi pending untuk dibayarkan.');
        }

        DB::transaction(function () {
            Commission::where('status', CommissionStatus::Pending)
                ->update([
                    'status' => CommissionStatus::Paid->value,
                    'paid_at' => now(),
                ]);
        });

        return back()->with('success', "Sebanyak {$count} komisi berhasil ditandai sebagai 'Paid'.");
    }

    /**
     * Upload bukti transfer untuk satu komisi individual.
     */
    public function uploadProof(Request $request, Commission $commission)
    {
        $request->validate([
            'transfer_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_notes'  => 'nullable|string|max:500',
        ]);

        // Hapus file lama jika ada
        if ($commission->transfer_proof && Storage::disk('public')->exists($commission->transfer_proof)) {
            Storage::disk('public')->delete($commission->transfer_proof);
        }

        $path = $request->file('transfer_proof')->store('transfer_proofs', 'public');

        $commission->update([
            'transfer_proof' => $path,
            'payment_notes'  => $request->input('payment_notes'),
            // Otomatis tandai paid jika belum
            'status'  => CommissionStatus::Paid,
            'paid_at' => $commission->paid_at ?? now(),
        ]);

        return back()->with('success', "Bukti transfer untuk komisi #{$commission->id} berhasil diupload.");
    }

    /**
     * Download invoice komisi individual sebagai PDF.
     */
    public function downloadInvoice(Commission $commission)
    {
        $commission->load('recipient');
        $date = now()->timezone('Asia/Makassar')->format('d M Y H:i');

        $pdf = Pdf::loadView('admin.commissions.invoice_pdf', compact('commission', 'date'))
            ->setPaper('a5', 'portrait');

        return $pdf->download("invoice_komisi_{$commission->id}.pdf");
    }

    /**
     * Preview invoice komisi individual di browser (inline).
     */
    public function previewInvoice(Commission $commission)
    {
        $commission->load('recipient');
        $date = now()->timezone('Asia/Makassar')->format('d M Y H:i');

        $pdf = Pdf::loadView('admin.commissions.invoice_pdf', compact('commission', 'date'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream("invoice_komisi_{$commission->id}.pdf");
    }
}
