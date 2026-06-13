<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Services\AgentRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly AgentRegistrationService $registrationService
    ) {}

    public function showRegistrationForm(Request $request)
    {
        // Support referral links like /register?ref=username.
        $referrer = null;
        if ($request->has('ref')) {
            $referrer = Agent::whereHas('user', function ($q) use ($request) {
                $q->where('username', $request->ref);
            })->first();
        }

        return view('auth.register', compact('referrer'));
    }

    public function processRegistration(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:50',
            'no_telp'           => 'required|numeric|max_digits:15|unique:agents,no_telp',
            'username'          => 'required|string|max:100|unique:users,username',
            'password'          => 'required|string|min:6|confirmed',
            'referral_agent_id' => 'nullable|exists:agents,id',
            // Manual username entry (when no pre-filled upline via URL).
            'referral_username' => 'nullable|string|exists:users,username',
            'proof_of_payment'  => 'required|image|max:2048', // 2MB max for proof
        ]);

        // Resolve referral_username → referral_agent_id (if username was typed manually
        // and no hidden referral_agent_id was pre-filled via ?ref= URL).
        if (! empty($validated['referral_username']) && empty($validated['referral_agent_id'])) {
            $referrerByUsername = Agent::whereHas('user', function ($q) use ($validated) {
                $q->where('username', $validated['referral_username']);
            })->first();
            
            if ($referrerByUsername) {
                $validated['referral_agent_id'] = $referrerByUsername->id;
            }
        }
        unset($validated['referral_username']); // Not used by the service directly.

        // Handle file upload
        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('payments', 'public');
            $validated['proof_of_payment'] = $path;
        }

        // Use service to create User, Agent, and Transaction (FC1)
        $transaction = $this->registrationService->submitRegistration($validated);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Akun Anda segera kami aktifkan setelah admin memverifikasi pembayaran Anda (Rp2.650.000).');
    }
}
