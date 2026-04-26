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
        // Support referral links like /register?ref=uplien_username
        $referrer = null;
        if ($request->has('ref')) {
            $referrer = Agent::whereHas('user', function($q) use ($request) {
                $q->where('username', $request->ref);
            })->first();
        }

        return view('auth.register', compact('referrer'));
    }

    public function processRegistration(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:50',
            'username'          => 'required|string|max:100|unique:users,username',
            'password'          => 'required|string|min:6|confirmed',
            'referral_agent_id' => 'nullable|exists:agents,id',
            'proof_of_payment'  => 'required|image|max:2048', // 2MB max for proof
        ]);

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
