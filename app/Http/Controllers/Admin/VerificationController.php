<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\AgentRegistrationService;
use App\Services\RepeatOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService,
        private readonly RepeatOrderService $repeatOrderService
    ) {}

    public function transactionsList()
    {
        // Simple query without data tables for MVP. Order pending first.
        $transactions = Transaction::with(['agent.user', 'verifier'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'verified' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(50);
            
        return view('admin.verifications.transactions', compact('transactions'));
    }

    public function approveTransaction(Transaction $transaction, Request $request)
    {
        try {
            $adminUser = $request->user();
            
            if ($transaction->type === TransactionType::NewAgent) {
                // FC1 Branch
                $this->registrationService->approveRegistration($transaction, $adminUser);
                $message = "Registrasi Agen {$transaction->agent->nama} berhasil diverifikasi. Komisi telah dibagikan.";
            } elseif ($transaction->type === TransactionType::RepeatOrder) {
                // FC2 Branch
                $this->repeatOrderService->approveOrder($transaction, $adminUser);
                $message = "Repeat Order dari {$transaction->agent->nama} berhasil diverifikasi. Poin bertambah dan komisi dibagikan.";
            } else {
                throw new \Exception("Tipe transaksi tidak dikenali.");
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectTransaction(Transaction $transaction, Request $request)
    {
        try {
            $adminUser = $request->user();

            if ($transaction->type === TransactionType::NewAgent) {
                $this->registrationService->rejectRegistration($transaction, $adminUser);
            } elseif ($transaction->type === TransactionType::RepeatOrder) {
                $this->repeatOrderService->rejectOrder($transaction, $adminUser);
            }

            return back()->with('success', "Transaksi #{$transaction->id} berhasil ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
