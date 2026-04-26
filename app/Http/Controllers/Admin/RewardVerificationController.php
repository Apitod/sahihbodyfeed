<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardClaim;
use App\Services\RewardClaimService;
use Illuminate\Http\Request;

class RewardVerificationController extends Controller
{
    public function __construct(
        private readonly RewardClaimService $rewardClaimService
    ) {}

    public function index()
    {
        $claims = RewardClaim::with(['agent.user', 'reward', 'approver'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END")
            ->latest()
            ->paginate(50);

        return view('admin.verifications.rewards', compact('claims'));
    }

    public function approve(RewardClaim $claim, Request $request)
    {
        try {
            $this->rewardClaimService->approveClaim($claim, $request->user());
            return back()->with('success', "Klaim reward {$claim->reward->name} untuk agen {$claim->agent->nama} berhasil disetujui. Matching reward rules telah diproses.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(RewardClaim $claim, Request $request)
    {
        try {
            $this->rewardClaimService->rejectClaim($claim, $request->user());
            return back()->with('success', "Klaim reward {$claim->reward->name} untuk agen {$claim->agent->nama} telah ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
