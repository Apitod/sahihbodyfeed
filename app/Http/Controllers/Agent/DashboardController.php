<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Enums\AgentStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user()->agent;
        
        // Basic stats
        $stats = [
            'total_commissions' => $agent->commissions()->sum('amount'),
            'total_downlines' => Agent::where('upline_id', $agent->id)->count(),
            'pending_payouts' => $agent->matchingRewardsAsSponsors()->where('status', 'pending')->count(),
            'total_points' => $agent->total_points,
        ];

        // Next rank information
        $currentStatus = $agent->status;
        $nextStatus = null;
        $pointsNeeded = 0;
        $progressPercent = 100;

        $statuses = AgentStatus::cases();
        foreach ($statuses as $index => $status) {
            if ($status === $currentStatus && isset($statuses[$index + 1])) {
                $nextStatus = $statuses[$index + 1];
                $pointsNeeded = $nextStatus->requiredPoints() - $agent->total_points;
                $pointsNeeded = max(0, $pointsNeeded);
                
                $range = $nextStatus->requiredPoints() - $status->requiredPoints();
                $currentProgress = $agent->total_points - $status->requiredPoints();
                $progressPercent = min(100, round(($currentProgress / $range) * 100));
                break;
            }
        }

        $nextRank = [
            'label' => $nextStatus ? $nextStatus->label() : 'Peringkat Tertinggi',
            'needed' => $pointsNeeded,
            'percent' => $progressPercent,
        ];

        // Recent Data
        $recentCommissions = $agent->commissions()->latest()->limit(5)->get();
        $recentTransactions = $agent->transactions()->latest()->limit(5)->get();

        return view('agent.dashboard', compact('stats', 'nextRank', 'recentCommissions', 'recentTransactions'));
    }
}
