<ul class="navbar-nav pt-lg-3">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('agent.dashboard') }}" >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
            </span>
            <span class="nav-link-title">Dashboard</span>
        </a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false" >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
            </span>
            <span class="nav-link-title">Transaksi</span>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('agent.repeat_order.create') }}">Repeat Order Baru</a>
            <a class="dropdown-item" href="{{ route('agent.transactions.index') }}">Riwayat Transaksi</a>
        </div>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="false" >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M10 12h4" /></svg>
            </span>
            <span class="nav-link-title">Keuangan</span>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('agent.commissions') }}">Komisi</a>
            <a class="dropdown-item" href="{{ route('agent.rewards') }}">Reward</a>
            <a class="dropdown-item" href="{{ route('agent.matching_rewards') }}">Matching Reward</a>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('agent.network') }}" >
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M5 17m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M19 7m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M19 17m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M7 8l4 3" /><path d="M7 16l4 -3" /><path d="M17 8l-4 3" /><path d="M17 16l-4 -3" /></svg>
            </span>
            <span class="nav-link-title">Jaringan</span>
        </a>
    </li>
</ul>
