<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Transaction;
use App\Models\RewardClaim;
use App\Models\Commission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        
        $stats = [
            'total_agents' => Agent::count(),
            'new_agents_month' => Agent::whereMonth('joined_at', $now->month)->whereYear('joined_at', $now->year)->count(),
            'pending_verifications' => Transaction::where('status', 'pending')->count(),
            'pending_reward_claims' => RewardClaim::where('status', 'pending')->count(),
            'total_transactions_value' => Transaction::where('status', 'verified')->sum('amount'),
            'total_commissions_generated' => Commission::sum('amount'),
            'total_paid_commissions' => Commission::where('status', 'paid')->sum('amount'),
        ];

        $recentTransactions = Transaction::with('agent')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentAgents = Agent::orderBy('joined_at', 'desc')
            ->limit(5)
            ->get();

        $pendingTransactions = Transaction::with('agent')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentTransactions', 'recentAgents', 'pendingTransactions'));
    }
}
