<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $agentId = $request->user()->agent->id;
        $commissions = $request->user()->agent->commissions()->with('transaction')->latest()->paginate(20);

        return view('agent.commissions.index', compact('commissions'));
    }
}
