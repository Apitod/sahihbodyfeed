<ul class="navbar-nav pt-lg-3">
    {{-- Badge khusus Superadmin --}}
    <li class="nav-item mb-2 px-3">
        <div class="d-flex align-items-center gap-2 py-2 px-3 rounded-3"
             style="background: rgba(240,160,75,0.2); border: 1px solid rgba(240,160,75,0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon text-warning" width="18" height="18" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 6l4 6l5 -4l-2 11h-14l-2 -11l5 4z" /></svg>
            <span class="fw-black text-white small text-uppercase" style="letter-spacing:0.08em;">Super Admin</span>
        </div>
    </li>

    {{-- Dashboard --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}"
           href="{{ route('superadmin.dashboard') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
            </span>
            <span class="nav-link-title">Dashboard Eksekutif</span>
        </a>
    </li>

    {{-- Manajemen Agen (Full CRUD) --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('superadmin.agents.*') ? 'active' : '' }}"
           href="{{ route('superadmin.agents.index') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
            </span>
            <span class="nav-link-title">Manajemen Agen</span>
        </a>
    </li>

    {{-- Verifikasi Akhir Transaksi --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('superadmin.verifications.transactions') ? 'active' : '' }}"
           href="{{ route('superadmin.verifications.transactions') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                    <path d="M9 12l2 2l4 -4" /></svg>
            </span>
            <span class="nav-link-title">Approval Transaksi</span>
        </a>
    </li>

    {{-- Verifikasi Akhir Klaim Reward --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('superadmin.verifications.rewards') ? 'active' : '' }}"
           href="{{ route('superadmin.verifications.rewards') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 12l2 2l4 -4" />
                    <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" /></svg>
            </span>
            <span class="nav-link-title">Approval Reward</span>
        </a>
    </li>

    {{-- Laporan Komisi --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('superadmin.commissions.*') ? 'active' : '' }}"
           href="{{ route('superadmin.commissions.index') }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                    <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" /></svg>
            </span>
            <span class="nav-link-title">Laporan Komisi</span>
        </a>
    </li>
</ul>
