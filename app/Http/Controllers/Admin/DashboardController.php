<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_agents' => \App\Models\Agent::count(),
            'pending_verifications' => \App\Models\Transaction::where('status', 'pending')->count(),
            'pending_reward_claims' => \App\Models\RewardClaim::where('status', 'pending')->count(),
            'total_transactions_value' => \App\Models\Transaction::where('status', 'verified')->sum('amount'),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
}
