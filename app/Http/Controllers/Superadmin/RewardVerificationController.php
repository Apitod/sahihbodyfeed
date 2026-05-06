<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\RewardClaim;
use App\Services\RewardClaimService;
use Illuminate\Http\Request;

/**
 * Superadmin Tier-1: Final approval for Reward Claims.
 */
class RewardVerificationController extends Controller
{
    public function __construct(
        private readonly RewardClaimService $claimService
    ) {}

    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'pending_superadmin');

        $claims = RewardClaim::with(['agent.user', 'reward', 'adminVerifier:id,username', 'superadminApprover:id,username'])
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->latest()
            ->paginate(50);

        $pendingCount = RewardClaim::where('status', ClaimStatus::PendingSuperadmin)->count();

        return view('superadmin.verifications.rewards', compact('claims', 'statusFilter', 'pendingCount'));
    }

    public function approve(RewardClaim $claim, Request $request)
    {
        if (! in_array($claim->status, [ClaimStatus::PendingSuperadmin, ClaimStatus::Pending])) {
            return back()->with('error', 'Klaim tidak dalam status yang dapat disetujui.');
        }

        try {
            $superadmin = $request->user();
            $this->claimService->approveClaim($claim, $superadmin);

            $claim->update([
                'approved_by_superadmin_id' => $superadmin->id,
                'approved_at'               => now(),
            ]);

            return back()->with('success', "Klaim reward dari {$claim->agent->nama} disetujui.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(RewardClaim $claim, Request $request)
    {
        if ($claim->status === ClaimStatus::Approved) {
            return back()->with('error', 'Klaim yang sudah disetujui tidak dapat ditolak.');
        }

        try {
            $superadmin = $request->user();
            $this->claimService->rejectClaim($claim, $superadmin);

            $claim->update([
                'approved_by_superadmin_id' => $superadmin->id,
                'approved_at'               => now(),
            ]);

            return back()->with('success', "Klaim reward #{$claim->id} ditolak.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
