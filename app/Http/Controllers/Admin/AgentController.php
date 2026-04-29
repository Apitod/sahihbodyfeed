<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AgentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use App\Services\AgentRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Admin\AgentController
 *
 * Provides full CRUD over Agent records plus two status-transition actions:
 *   approve() — activates a pending/inactive agent account.
 *   suspend()  — suspends an active agent, blocking their login.
 *
 * Access is restricted to authenticated admins via the 'role:admin' middleware
 * applied to the entire admin route group in routes/web.php.
 */
class AgentController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService,
    ) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    /**
     * Display a paginated, filterable list of all agents.
     */
    public function index(Request $request): View
    {
        $query = Agent::with(['user', 'upline'])
            ->withCount('downlines');

        // Search by name or referral code.
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$search}%"));
            });
        }

        // Filter by status.
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $agents   = $query->latest()->paginate(20)->withQueryString();
        $statuses = AgentStatus::cases();

        return view('admin.agents.index', compact('agents', 'statuses'));
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    /**
     * Deep-dive view: agent profile, direct downlines, and commission history.
     */
    public function show(Agent $agent): View
    {
        $agent->load(['user', 'upline.user']);

        $downlines = $agent->downlines()
            ->with('user')
            ->withCount('downlines')
            ->latest()
            ->paginate(10, ['*'], 'downlines_page');

        $commissions = $agent->commissions()
            ->with('transaction')
            ->latest()
            ->paginate(15, ['*'], 'commissions_page');

        $commissionTotal = $agent->commissions()->sum('amount');

        return view('admin.agents.show', compact(
            'agent',
            'downlines',
            'commissions',
            'commissionTotal',
        ));
    }

    // ─── Create / Store ───────────────────────────────────────────────────────

    /**
     * Show the form to create a new agent (with immediate activation).
     */
    public function create(): View
    {
        $uplineAgents = Agent::with('user')->orderBy('nama')->get();

        return view('admin.agents.create', compact('uplineAgents'));
    }

    /**
     * Persist a brand-new agent created by the admin.
     *
     * Unlike the public registration flow, admin-created agents are immediately
     * activated — no payment proof or transaction verification step required.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username'   => 'required|string|max:60|unique:users,username',
            'password'   => 'required|string|min:8|confirmed',
            'nama'       => 'required|string|max:120',
            'phone'      => 'nullable|string|max:20',
            'upline_id'  => 'nullable|exists:agents,id',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'username'  => $validated['username'],
                'password'  => Hash::make($validated['password']),
                'role'      => UserRole::Agent,
                'is_active' => true,
            ]);

            $user->assignRole('agent');

            Agent::create([
                'user_id'      => $user->id,
                'nama'         => $validated['nama'],
                'phone'        => $validated['phone'] ?? null,
                'upline_id'    => $validated['upline_id'] ?? null,
                'total_points' => 0,
                'status'       => AgentStatus::Agent,
                'joined_at'    => now(),
            ]);

            Log::info("Admin created new agent [{$validated['nama']}] with user [{$user->id}].");
        });

        return redirect()->route('admin.agents.index')
            ->with('success', "Agen '{$validated['nama']}' berhasil dibuat.");
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    /**
     * Show the edit form for an existing agent.
     */
    public function edit(Agent $agent): View
    {
        $agent->load('user');
        $uplineAgents = Agent::with('user')
            ->where('id', '!=', $agent->id)
            ->orderBy('nama')
            ->get();

        return view('admin.agents.edit', compact('agent', 'uplineAgents'));
    }

    /**
     * Update an agent's profile data.
     * Username/password changes are handled separately via the User model.
     */
    public function update(Request $request, Agent $agent): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:120',
            'phone'     => 'nullable|string|max:20',
            'upline_id' => [
                'nullable',
                'exists:agents,id',
                // Prevent self-referencing.
                function ($attribute, $value, $fail) use ($agent) {
                    if ((int) $value === $agent->id) {
                        $fail('Agen tidak bisa menjadi upline-nya sendiri.');
                    }
                },
            ],
        ]);

        $agent->update($validated);

        Log::info("Admin updated agent [{$agent->id}].");

        return redirect()->route('admin.agents.show', $agent)
            ->with('success', 'Data agen berhasil diperbarui.');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    /**
     * Delete an agent record and their linked user account.
     *
     * Safeguard: prevents deletion if the agent has any downlines (would break the
     * referral chain) or unresolved pending commissions.
     */
    public function destroy(Agent $agent): RedirectResponse
    {
        if ($agent->downlines()->exists()) {
            return back()->with('error', 'Agen tidak dapat dihapus karena masih memiliki downline aktif.');
        }

        $nama = $agent->nama;

        DB::transaction(function () use ($agent) {
            $userId = $agent->user_id;
            $agent->delete();
            User::find($userId)?->delete();
        });

        Log::warning("Admin deleted agent [{$nama}].");

        return redirect()->route('admin.agents.index')
            ->with('success', "Agen '{$nama}' telah dihapus.");
    }

    // ─── Status Transitions ───────────────────────────────────────────────────

    /**
     * Approve a pending (inactive) agent.
     *
     * If the agent has a pending new_agent transaction, we delegate to
     * AgentRegistrationService::approveRegistration() so commissions are
     * distributed exactly as defined in Flowchart 1.
     *
     * If no pending transaction exists (e.g., admin-created agent manually set
     * inactive), we simply activate the user account.
     */
    public function approve(Agent $agent): RedirectResponse
    {
        if ($agent->user->is_active) {
            return back()->with('error', 'Akun agen ini sudah aktif.');
        }

        // Prefer the service-level approval path when a pending transaction exists.
        $pendingTransaction = $agent->transactions()
            ->where('type', TransactionType::NewAgent)
            ->where('status', TransactionStatus::Pending)
            ->latest()
            ->first();

        if ($pendingTransaction) {
            $this->registrationService->approveRegistration($pendingTransaction, auth()->user());
        } else {
            // No pending registration transaction — direct activation.
            DB::transaction(function () use ($agent) {
                $agent->user->update(['is_active' => true]);
                $agent->update(['joined_at' => $agent->joined_at ?? now()]);
                $agent->user->assignRole('agent');

                Log::info("Admin directly activated agent [{$agent->id}] (no pending transaction found).");
            });
        }

        return back()->with('success', "Agen '{$agent->nama}' berhasil diaktifkan.");
    }

    /**
     * Suspend an active agent.
     *
     * Sets is_active = false on the linked user account, immediately preventing
     * login. The agent record is preserved for audit / downline integrity.
     */
    public function suspend(Agent $agent): RedirectResponse
    {
        if (! $agent->user->is_active) {
            return back()->with('error', 'Agen ini sudah dalam status tidak aktif.');
        }

        $agent->user->update(['is_active' => false]);

        Log::warning("Admin suspended agent [{$agent->id}] ({$agent->nama}).");

        return back()->with('success', "Agen '{$agent->nama}' telah disuspend.");
    }
}
