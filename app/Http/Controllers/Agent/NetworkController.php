<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user()->agent;
        $generations = [];
        $this->collectGenerations($agent, 0, $generations);

        return view('agent.network.index', compact('generations'));
    }

    private function collectGenerations($agent, $depth, &$generations)
    {
        if (!isset($generations[$depth])) {
            $generations[$depth] = [];
        }
        
        $generations[$depth][] = [
            'id' => $agent->id,
            'name' => $agent->nama,
            'status' => $agent->status->label(),
            'upline_id' => $agent->upline_id,
            'downline_count' => $agent->downlines->count(),
        ];

        // Only go up to a certain depth if needed, but let's follow the tree
        foreach ($agent->downlines as $downline) {
            $this->collectGenerations($downline, $depth + 1, $generations);
        }
    }
}
