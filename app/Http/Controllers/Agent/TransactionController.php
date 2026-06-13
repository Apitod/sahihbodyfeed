<?php

namespace App\Http\Controllers\Agent;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Services\RepeatOrderService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private readonly RepeatOrderService $repeatOrderService
    ) {}

    public function createRepeatOrder()
    {
        $amount = TransactionType::RepeatOrder->amount();
        return view('agent.transactions.create_ro', compact('amount'));
    }

    public function storeRepeatOrder(Request $request)
    {
        $request->validate([
            'proof_of_payment' => 'required|image|max:2048',
        ]);

        $agent = $request->user()->agent;
        $path = $request->file('proof_of_payment')->store('payments', 'public');

        $this->repeatOrderService->submitOrder($agent, $path);

        return redirect()->route('agent.transactions.index')
                         ->with('success', 'Repeat Order berhasil diajukan! Menunggu verifikasi admin.');
    }

    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'all');
        $transactions = $request->user()->agent->transactions()
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(20);

        return view('agent.transactions.index', compact('transactions', 'statusFilter'));
    }
}
