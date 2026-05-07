<?php

namespace App\Http\Controllers\Superadmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Superadmin\AdminController
 *
 * Full CRUD for Admin (Tier-2) user accounts.
 * Only Superadmin can create, edit, activate, suspend, or delete admin accounts.
 */
class AdminController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    /**
     * Display a paginated list of all admin users.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', UserRole::Admin);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('is_active', $status === 'active');
        }

        $admins = $query->latest()->paginate(20)->withQueryString();

        return view('superadmin.admins.index', compact('admins'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    /**
     * Create and persist a new admin account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => 'required|string|max:120',
            'username' => 'required|string|max:60|unique:users,username',
            'email'    => 'nullable|email|max:120|unique:users,email',
            'no_telp'  => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nama.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah digunakan.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::transaction(function () use ($validated) {
            $admin = User::create([
                'nama'      => $validated['nama'],
                'username'  => $validated['username'],
                'email'     => $validated['email'] ?? null,
                'no_telp'   => $validated['no_telp'] ?? null,
                'password'  => Hash::make($validated['password']),
                'role'      => UserRole::Admin,
                'is_active' => true,
            ]);

            $admin->assignRole('admin');

            Log::info("Superadmin created admin account [{$admin->username}] (ID: {$admin->id}).");
        });

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Admin '{$validated['username']}' berhasil dibuat.");
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    /**
     * Update an existing admin's profile and (optionally) password.
     */
    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_if($admin->role !== UserRole::Admin, 403);

        $validated = $request->validate([
            'nama'     => 'required|string|max:120',
            'username' => 'required|string|max:60|unique:users,username,' . $admin->id,
            'email'    => 'nullable|email|max:120|unique:users,email,' . $admin->id,
            'no_telp'  => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($admin, $validated) {
            $data = [
                'nama'     => $validated['nama'],
                'username' => $validated['username'],
                'email'    => $validated['email'] ?? null,
                'no_telp'  => $validated['no_telp'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $data['password'] = Hash::make($validated['password']);
            }

            $admin->update($data);

            Log::info("Superadmin updated admin account [{$admin->username}] (ID: {$admin->id}).");
        });

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Data admin '{$admin->username}' berhasil diperbarui.");
    }

    // ─── Toggle Active ────────────────────────────────────────────────────────

    /**
     * Activate a suspended admin account.
     */
    public function activate(User $admin): RedirectResponse
    {
        abort_if($admin->role !== UserRole::Admin, 403);

        $admin->update(['is_active' => true]);
        Log::info("Superadmin activated admin [{$admin->username}].");

        return back()->with('success', "Admin '{$admin->username}' berhasil diaktifkan.");
    }

    /**
     * Suspend an active admin account.
     */
    public function suspend(User $admin): RedirectResponse
    {
        abort_if($admin->role !== UserRole::Admin, 403);

        $admin->update(['is_active' => false]);
        Log::warning("Superadmin suspended admin [{$admin->username}].");

        return back()->with('success', "Admin '{$admin->username}' telah disuspend.");
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    /**
     * Permanently delete an admin account.
     */
    public function destroy(User $admin): RedirectResponse
    {
        abort_if($admin->role !== UserRole::Admin, 403);

        $username = $admin->username;
        $admin->delete();

        Log::warning("Superadmin deleted admin account [{$username}].");

        return redirect()->route('superadmin.admins.index')
            ->with('success', "Admin '{$username}' telah dihapus.");
    }
}
