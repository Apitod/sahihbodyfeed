<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $agentId = $request->user()->agent->id;
        $query = $request->user()->agent->commissions()->with('transaction');

        if ($request->filled('generation_level')) {
            $query->where('generation_level', $request->generation_level);
        }

        $commissions = $query->latest()->paginate(20)->withQueryString();

        return view('agent.commissions.index', compact('commissions'));
    }
}
