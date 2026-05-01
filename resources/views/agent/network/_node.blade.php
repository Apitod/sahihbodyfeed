{{--
    Partial rekursif: merender satu node beserta children-nya.
    Variabel: $node (array), $isRoot (bool)
--}}
@php
    $depth     = $node['depth'];
    $hasKids   = count($node['children']) > 0;
    $colors    = ['#F0A04B', '#B1C29E', '#FADA7A', '#FCE7C8'];
    $color     = $colors[$depth % count($colors)];
    $textColor = ($depth % count($colors) == 3) ? '#8a701d' : $color;
    $nodeId    = 'node-' . $node['id'];
    $isRoot    = $isRoot ?? false;
@endphp

<div class="tree-node {{ $isRoot ? 'tree-root' : '' }}"
     x-data="{ open: {{ $isRoot ? 'true' : 'false' }} }"
     @collapse-all.window="if(!{{ $isRoot ? 'true' : 'false' }}) open = false"
     id="{{ $nodeId }}">

    {{-- Card wrapper with connector line --}}
    <div class="tree-item {{ $hasKids && !$isRoot ? 'has-children' : '' }}">

        {{-- The agent card --}}
        <div class="agent-node-card"
             style="--accent: {{ $color }};"
             @if($hasKids)
             @click="open = !open"
             title="Klik untuk expand/collapse downline"
             @endif>

            {{-- Left accent bar --}}
            <div class="node-accent-bar"></div>

            <div class="node-content">
                {{-- Avatar --}}
                <div class="node-avatar" style="background: color-mix(in srgb, {{ $color }} 20%, white);">
                    <span style="color: {{ $textColor }};">{{ strtoupper(substr($node['name'], 0, 2)) }}</span>
                </div>

                {{-- Info --}}
                <div class="node-info">
                    <div class="node-name" title="{{ $node['name'] }}">{{ $node['name'] }}</div>
                    <div class="node-meta">
                        <span class="node-status-badge" style="background: color-mix(in srgb, {{ $color }} 15%, white); color: {{ $textColor }}; border: 1px solid color-mix(in srgb, {{ $color }} 30%, white);">
                            {{ $node['status'] }}
                        </span>
                        @if($depth > 0)
                            <span class="node-gen-label">Gen {{ $depth }}</span>
                        @else
                            <span class="node-gen-label">Anda</span>
                        @endif
                    </div>
                </div>

                {{-- Toggle indicator (only if has children) --}}
                @if($hasKids)
                    <div class="node-toggle" :class="open ? 'node-toggle--open' : ''">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Downline count footer --}}
            @if($node['downline_count'] > 0)
                <div class="node-footer">
                    <span>Downline</span>
                    <span class="node-count-badge" style="background: {{ $color }}; color: {{ ($depth % 4 == 3) ? '#8a701d' : '#fff' }};">{{ $node['downline_count'] }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Children branch --}}
    @if($hasKids)
        <div class="tree-children"
             x-show="open"
             x-transition:enter="tree-enter"
             x-transition:enter-start="tree-enter-start"
             x-transition:enter-end="tree-enter-end"
             x-transition:leave="tree-leave"
             x-transition:leave-start="tree-leave-start"
             x-transition:leave-end="tree-leave-end">

            <div class="tree-children-inner">
                @foreach($node['children'] as $child)
                    @include('agent.network._node', ['node' => $child, 'isRoot' => false])
                @endforeach
            </div>
        </div>
    @endif
</div>
