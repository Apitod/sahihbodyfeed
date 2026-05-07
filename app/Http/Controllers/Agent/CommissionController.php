<?php

namespace App\Http\Controllers\Agent;

use App\Enums\CommissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user()->agent;

        $query = $agent->commissions()->with('transaction');

        if ($request->filled('generation_level')) {
            $query->where('generation_level', $request->generation_level);
        }

        // Summary stats (without pagination)
        $statsQuery = $agent->commissions();
        if ($request->filled('generation_level')) {
            $statsQuery->where('generation_level', $request->generation_level);
        }

        $stats = [
            'total'    => (float) $statsQuery->clone()->sum('amount'),
            'paid'     => (float) $statsQuery->clone()->where('status', CommissionStatus::Paid)->sum('amount'),
            'pending'  => (float) $statsQuery->clone()->where('status', CommissionStatus::Pending)->sum('amount'),
            'menunggu' => (float) $statsQuery->clone()->where('status', CommissionStatus::Menunggu)->sum('amount'),
        ];

        $commissions = $query->latest()->paginate(20)->withQueryString();

        return view('agent.commissions.index', compact('commissions', 'stats'));
    }

    /**
     * Preview invoice komisi milik agen yang sedang login (read-only).
     */
    public function previewInvoice(Commission $commission, Request $request)
    {
        // Pastikan komisi ini milik agen yang login
        $agent = $request->user()->agent;
        if ($commission->recipient_id !== $agent->id) {
            abort(403, 'Anda tidak berhak mengakses invoice ini.');
        }

        $commission->load('recipient');
        $date = now()->timezone('Asia/Makassar')->format('d M Y H:i');

        $pdf = Pdf::loadView('admin.commissions.invoice_pdf', compact('commission', 'date'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream("invoice_komisi_{$commission->id}.pdf");
    }
}
