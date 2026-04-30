{{--
    _tree_node.blade.php
    Recursive partial for the collapsible agent hierarchy tree.

    Variables:
      $agent  — the Agent model instance (with user + downlines pre-loaded)
      $depth  — integer depth level (0 = root / Agen Utama)
--}}
@php
    $hasKids    = $agent->downlines->isNotEmpty();
    $genClass   = 'gen-' . min($depth, 3);
    $genLabel   = match($depth) {
        0       => 'Agen Utama',
        1       => 'Gen-1',
        2       => 'Gen-2',
        3       => 'Gen-3',
        default => 'Gen-' . $depth,
    };

    // Deterministic colour from agent id for avatar
    $colors = ['#206bc4','#2ba766','#d63939','#f76707','#7230d1','#a46e09','#1098ad'];
    $avatarColor = $colors[$agent->id % count($colors)];
@endphp

<li class="tree-node" id="tree-node-{{ $agent->id }}">
    {{-- Row --}}
    <div class="tree-item" role="button" aria-expanded="{{ $hasKids ? 'false' : 'false' }}"
         aria-controls="tree-kids-{{ $agent->id }}">

        {{-- Expand chevron --}}
        <span class="tree-toggle {{ $hasKids ? '' : 'leaf' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                 stroke-width="2.5" stroke="currentColor" fill="none">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 6l6 6l-6 6"/>
            </svg>
        </span>

        {{-- Avatar --}}
        <span class="tree-avatar" style="background:{{ $avatarColor }}">
            {{ strtoupper(mb_substr($agent->nama, 0, 2)) }}
        </span>

        {{-- Info --}}
        <div class="tree-info">
            <div class="tree-name">
                <a href="{{ route('admin.agents.show', $agent) }}"
                   class="text-reset text-decoration-none link-primary"
                   onclick="event.stopPropagation()">{{ $agent->nama }}</a>
            </div>
            <div class="tree-meta">
                <code class="small">{{ $agent->referral_code }}</code>
                @if($agent->user?->username)
                    &nbsp;·&nbsp; {{ $agent->user->username }}
                @endif
                @if($hasKids)
                    &nbsp;·&nbsp; {{ $agent->downlines->count() }} downline
                @endif
            </div>
        </div>

        {{-- Generation pill --}}
        <span class="gen-pill {{ $genClass }} d-none d-sm-inline-flex">{{ $genLabel }}</span>

        {{-- Active/inactive dot --}}
        @if($agent->user?->is_active)
            <span class="badge bg-green-lt text-green ms-1" title="Aktif">●</span>
        @else
            <span class="badge bg-red-lt text-red ms-1" title="Tidak Aktif">●</span>
        @endif

    </div>{{-- /tree-item --}}

    {{-- Recursive children --}}
    @if($hasKids)
        <div class="tree-children" id="tree-kids-{{ $agent->id }}">
            <ul class="agent-tree">
                @foreach($agent->downlines as $child)
                    @include('admin.agents._tree_node', [
                        'agent' => $child,
                        'depth' => $depth + 1,
                    ])
                @endforeach
            </ul>
        </div>
    @endif

</li>
