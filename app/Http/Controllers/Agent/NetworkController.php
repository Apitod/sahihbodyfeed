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
        
        // Fetch downline hierarchy (recursively or just next level)
        // For a diagram, we'll fetch the tree starting from this agent
        $network = $this->getDownlineData($agent);

        return view('agent.network.index', compact('network'));
    }

    private function getDownlineData(Agent $agent)
    {
        return [
            'id' => $agent->id,
            'name' => $agent->nama,
            'status' => $agent->status->label(),
            'children' => $agent->downlines->map(function($downline) {
                return $this->getDownlineData($downline);
            })->toArray()
        ];
    }
}
