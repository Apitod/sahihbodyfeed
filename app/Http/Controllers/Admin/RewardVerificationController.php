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
        if ($claim->status !== \App\Enums\ClaimStatus::Pending) {
            return back()->with('error', 'Hanya klaim berstatus Pending yang dapat direview.');
        }

        try {
            $claim->update([
                'status'               => \App\Enums\ClaimStatus::PendingSuperadmin,
                'verified_by_admin_id' => $request->user()->id,
            ]);
            return back()->with('success', "Klaim reward {$claim->reward->name} telah diverifikasi admin. Menunggu persetujuan Superadmin.");
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
