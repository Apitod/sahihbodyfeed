<?php

namespace App\Http\Controllers\Agent;

use App\Exceptions\InsufficientPointsException;
use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Services\RewardClaimService;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(
        private readonly RewardClaimService $rewardClaimService
    ) {}

    public function index(Request $request)
    {
        $agent = $request->user()->agent;
        $rewards = Reward::orderBy('required_points')->get();
        $claims = $agent->rewardClaims()->get()->keyBy('reward_id');

        return view('agent.rewards.index', compact('agent', 'rewards', 'claims'));
    }

    public function claim(Reward $reward, Request $request)
    {
        $agent = $request->user()->agent;

        // Prevent duplicate claims for the same reward if already pending or approved
        $existingClaim = $agent->rewardClaims()
                               ->where('reward_id', $reward->id)
                               ->whereIn('status', ['pending', 'pending_superadmin', 'approved'])
                               ->first();

        if ($existingClaim) {
            return back()->with('error', 'Anda sudah pernah mengklaim atau sedang menunggu verifikasi untuk reward ini.');
        }

        try {
            $this->rewardClaimService->submitClaim($agent, $reward);
            return back()->with('success', "Klaim reward {$reward->name} berhasil diajukan! Menunggu verifikasi admin.");
        } catch (InsufficientPointsException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
