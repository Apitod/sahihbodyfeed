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
        $agentId = $request->user()->agent->id;
        $transactions = $request->user()->agent->transactions()->latest()->paginate(20);
        return view('agent.transactions.index', compact('transactions'));
    }
}
