<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user()->agent;
        
        $stats = [
            'total_commissions' => $agent->commissions()->sum('amount'),
            'total_downlines' => \App\Models\Agent::where('upline_id', $agent->id)->count(),
            'pending_payouts' => $agent->matchingRewardsAsSponsors()->where('status', 'pending')->count(),
        ];

        return view('agent.dashboard', compact('stats'));
    }
}
