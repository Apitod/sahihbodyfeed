@extends('layouts.app')

@section('title', 'Hierarki Jaringan')

@section('content')

<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title text-primary font-weight-bold">Hierarki Jaringan</h2>
            <div class="text-muted small mt-1">Kelola dan pantau struktur perkembangan tim Anda per generasi dalam tampilan interaktif.</div>
        </div>
    </div>
</div>

<div class="row row-cards" x-data="{ 
    collapsed: {},
    limits: {},
    toggleGen(depth) {
        this.collapsed[depth] = !this.collapsed[depth];
    },
    isCollapsed(depth) {
        return !!this.collapsed[depth];
    },
    getLimit(depth) {
        return this.limits[depth] || 5;
    },
    showMore(depth) {
        this.limits[depth] = (this.limits[depth] || 5) + 5;
    }
}">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex align-items-center w-100">
                    <h3 class="card-title m-0">Diagram Jaringan Aktif</h3>
                    <div class="ms-auto">
                        <span class="badge bg-blue-lt px-3 py-2 rounded-pill">
                            <i class="ti ti-users me-1"></i> {{ collect($generations)->flatten(1)->count() }} Total Tim
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 bg-light-subtle">
                <div class="d-flex overflow-auto pb-4 network-wrapper" style="min-height: 500px; gap: 2.5rem;">
                    @foreach($generations as $depth => $agents)
                        <div class="flex-shrink-0 transition-all duration-300" 
                             :style="isCollapsed({{ $depth }}) ? 'width: 40px' : 'width: 280px'">
                            
                            <!-- Generation Header -->
                            <div class="sticky-top bg-transparent" style="z-index: 10;">
                                <div class="rounded-3 shadow-sm d-flex align-items-center transition-all duration-300 overflow-hidden"
                                     :class="isCollapsed({{ $depth }}) ? 'bg-secondary text-white justify-content-center' : 'bg-white border text-dark p-2 justify-content-between'"
                                     @click="toggleGen({{ $depth }})" 
                                     style="cursor: pointer; height: 48px;">
                                    
                                    <template x-if="!isCollapsed({{ $depth }})">
                                        <div class="d-flex align-items-center w-100">
                                            <div class="gen-indicator me-2 rounded-circle" 
                                                 style="width: 10px; height: 10px; background: {{ $depth == 0 ? '#3b82f6' : ($depth % 2 == 0 ? '#10b981' : '#f59e0b') }}; flex-shrink: 0;">
                                            </div>
                                            <span class="fw-bold text-nowrap flex-grow-1">
                                                {{ $depth == 0 ? 'Sponsor' : 'Gen ' . $depth }}
                                            </span>
                                            <span class="badge bg-light text-dark rounded-pill border me-2 small">{{ count($agents) }}</span>
                                            <i class="ti ti-chevron-left fs-4"></i>
                                        </div>
                                    </template>

                                    <template x-if="isCollapsed({{ $depth }})">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="fw-bold small mb-n1">{{ $depth }}</span>
                                            <i class="ti ti-chevron-right small"></i>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Agent Cards -->
                            <div class="d-flex flex-column gap-3 mt-4" x-show="!isCollapsed({{ $depth }})" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                                @foreach($agents as $index => $agent)
                                    <div class="agent-card bg-white rounded-4 shadow-sm border p-3 position-relative overflow-hidden" 
                                         x-show="{{ $index }} < getLimit({{ $depth }})"
                                         data-bs-toggle="modal" data-bs-target="#modal-agent-detail" 
                                         onclick="showDetail({{ json_encode($agent) }})"
                                         style="cursor: pointer; transition: all 0.2s ease;">
                                        
                                        <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background: {{ $depth == 0 ? '#3b82f6' : ($depth % 2 == 0 ? '#10b981' : '#f59e0b') }};"></div>
                                        
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md rounded-circle bg-light text-primary fw-bold me-3">
                                                {{ substr($agent['name'], 0, 2) }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate" title="{{ $agent['name'] }}">{{ $agent['name'] }}</div>
                                                <div class="text-muted small d-flex align-items-center">
                                                    <span class="badge bg-blue-lt py-0 px-1 me-1" style="font-size: 0.65rem;">{{ $agent['status'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($agent['downline_count'] > 0)
                                            <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Total Downline</span>
                                                <span class="badge bg-light text-primary rounded-pill border">{{ $agent['downline_count'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                @if(count($agents) > 5)
                                    <div x-show="getLimit({{ $depth }}) < {{ count($agents) }}" class="text-center mt-2">
                                        <button class="btn btn-sm btn-ghost-primary rounded-pill w-100 py-2 fw-bold" @click="showMore({{ $depth }})">
                                            <i class="ti ti-plus me-1"></i> Tampilkan Lebih Banyak (<span x-text="{{ count($agents) }} - getLimit({{ $depth }})"></span> lagi)
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Agent -->
<div class="modal modal-blur fade" id="modal-agent-detail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center py-5">
                <div class="avatar avatar-xl rounded-circle bg-blue-lt mb-4 shadow-sm" id="modal-avatar" style="width: 80px; height: 80px; font-size: 1.5rem;">??</div>
                <h3 id="modal-name" class="mb-1 fw-bold">Nama Agen</h3>
                <div class="badge bg-blue-lt px-3 mb-4" id="modal-status">Pangkat</div>
                
                <div class="row g-2 mt-2">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-4">
                            <div class="text-muted small mb-1">Downline Langsung</div>
                            <div id="modal-downline-count" class="h2 fw-bold mb-0 text-primary">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light w-100 rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .network-wrapper {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .network-wrapper::-webkit-scrollbar {
        height: 6px;
    }
    .network-wrapper::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 10px;
    }
    .agent-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border-color: #3b82f6 !important;
    }
    .transition-all {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .ti {
        vertical-align: middle;
    }
</style>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function showDetail(agent) {
            document.getElementById('modal-name').innerText = agent.name;
            document.getElementById('modal-status').innerText = agent.status;
            document.getElementById('modal-avatar').innerText = agent.name.substring(0, 2).toUpperCase();
            document.getElementById('modal-downline-count').innerText = agent.downline_count;
        }
    </script>
@endsection
