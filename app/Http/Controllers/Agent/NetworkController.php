<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    /**
     * Maximum depth of tree to render (0 = root, 1 = gen1, etc.)
     * Keeps it performant with large networks.
     */
    private const MAX_DEPTH = 3;

    public function index(Request $request)
    {
        $agent = $request->user()->agent;

        // Build the nested tree starting from the logged-in agent.
        $tree = $this->buildTree($agent, 0);

        // Derive team count from the already-built tree (no second DB traversal).
        $totalTeam = $this->countFromTree($tree);

        return view('agent.network.index', compact('tree', 'totalTeam'));
    }

    /**
     * Recursively builds a nested tree array for rendering.
     */
    private function buildTree(Agent $agent, int $depth): array
    {
        $node = [
            'id'             => $agent->id,
            'name'           => $agent->nama,
            'status'         => $agent->status->label(),
            'upline_id'      => $agent->upline_id,
            'downline_count' => $agent->downlines->count(),
            'depth'          => $depth,
            'children'       => [],
        ];

        if ($depth < self::MAX_DEPTH) {
            // Load downlines eager to avoid N+1
            $agent->load('downlines');
            foreach ($agent->downlines as $downline) {
                $node['children'][] = $this->buildTree($downline, $depth + 1);
            }
        }

        return $node;
    }

    /**
     * Count all descendants recursively for the total team badge.
     */
    private function countDescendants(Agent $agent): int
    {
        $count = 0;
        $agent->load('downlines');
        foreach ($agent->downlines as $downline) {
            $count += 1 + $this->countDescendants($downline);
        }
        return $count;
    }

    /**
     * Derive total descendant count from an already-built tree array.
     * Avoids a second recursive DB traversal.
     */
    private function countFromTree(array $node): int
    {
        $count = 0;
        foreach ($node['children'] as $child) {
            $count += 1 + $this->countFromTree($child);
        }
        return $count;
    }
}
