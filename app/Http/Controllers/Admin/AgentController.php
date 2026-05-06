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
     * Also passes the hierarchy tree for the collapsible tree tab.
     */
    public function index(Request $request): View
    {
        $query = Agent::with(['user', 'upline'])
            ->withCount('downlines');

        // Search by name or username.
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('username', 'like', "%{$search}%"));
            });
        }

        // Filter by status.
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $agents   = $query->latest()->paginate(20)->withQueryString();
        $statuses = AgentStatus::cases();

        // Load root agents with 3 levels of downlines for the hierarchy tree tab.
        $rootAgents = Agent::with([
                'user',
                'downlines.user',
                'downlines.downlines.user',
                'downlines.downlines.downlines.user',
            ])
            ->whereNull('upline_id')
            ->orderBy('nama')
            ->get();

        return view('admin.agents.index', compact('agents', 'statuses', 'rootAgents'));
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
     * Admin-created agents are immediately activated — no payment proof or
     * transaction verification step is required.
     *
     * NIK is stored as a plain string (max 16 digits) to avoid file storage overhead.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // ── User credentials ───────────────────────────────────────────────────
            'username'          => 'required|string|max:60|unique:users,username',
            'password'          => 'required|string|min:8|confirmed',
            // ── Personal data ───────────────────────────────────────────────────────
            'nama'              => 'required|string|max:120',
            'no_telp'           => 'nullable|string|max:20|unique:agents,no_telp',
            'alamat'            => 'nullable|string|max:500',
            // ── NIK (16 digit max, numeric string) ─────────────────────────────────
            'nik'               => 'nullable|string|digits_between:1,16|unique:agents,nik',
            // ── Bank data ───────────────────────────────────────────────────────────
            'bank_name'         => 'nullable|string|max:100',
            'bank_account'      => 'nullable|string|max:30',
            'bank_account_name' => 'nullable|string|max:100',
            // ── Hierarchy ────────────────────────────────────────────────────────────
            'upline_id'         => 'nullable|exists:agents,id',
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
                'user_id'           => $user->id,
                'nama'              => $validated['nama'],
                'no_telp'           => $validated['no_telp']           ?? null,
                'alamat'            => $validated['alamat']            ?? null,
                'nik'               => $validated['nik']               ?? null,
                'bank_name'         => $validated['bank_name']         ?? null,
                'bank_account'      => $validated['bank_account']      ?? null,
                'bank_account_name' => $validated['bank_account_name'] ?? null,
                'upline_id'         => $validated['upline_id']         ?? null,
                'total_points'      => 0,
                'status'            => AgentStatus::Agent,
                'joined_at'         => now(),
            ]);

            Log::info("Admin created agent [{$validated['nama']}] with user [{$user->id}].");
        });

        $baseRoute = auth()->user()->isSuperAdmin() ? 'superadmin.agents' : 'admin.agents';

        return redirect()->route($baseRoute . '.index')
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
     *
     * Username and password changes go through the User model directly.
     * NIK is stored as a plain 16-digit string — no file I/O required.
     */
    public function update(Request $request, Agent $agent): RedirectResponse
    {
        $validated = $request->validate([
            // ── Personal data ─────────────────────────────────────────────────────
            'nama'              => 'required|string|max:120',
            'no_telp'           => 'nullable|string|max:20|unique:agents,no_telp,' . $agent->id,
            'alamat'            => 'nullable|string|max:500',
            // ── NIK (optional on update, must still be unique) ───────────────────
            'nik'               => 'nullable|string|digits_between:1,16|unique:agents,nik,' . $agent->id,
            // ── Bank data ───────────────────────────────────────────────────────────
            'bank_name'         => 'nullable|string|max:100',
            'bank_account'      => 'nullable|string|max:30',
            'bank_account_name' => 'nullable|string|max:100',
            // ── Hierarchy ────────────────────────────────────────────────────────────
            'upline_id'         => [
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

        $baseRoute = auth()->user()->isSuperAdmin() ? 'superadmin.agents' : 'admin.agents';

        return redirect()->route($baseRoute . '.show', $agent)
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
            if ($userId) {
                User::where('id', $userId)->delete();
            }
        });

        Log::warning("Admin deleted agent [{$nama}].");

        $baseRoute = auth()->user()->isSuperAdmin() ? 'superadmin.agents' : 'admin.agents';

        return redirect()->route($baseRoute . '.index')
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
