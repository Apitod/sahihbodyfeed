<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MatchingRewardController extends Controller
{
    public function index(Request $request)
    {
        $matchingLogs = $request->user()->agent->matchingRewardsAsSponsors()
            ->with(['reward', 'downline'])
            ->latest()
            ->paginate(20);

        return view('agent.matching_rewards.index', compact('matchingLogs'));
    }
}
