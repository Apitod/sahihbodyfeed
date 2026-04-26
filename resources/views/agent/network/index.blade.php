@extends('layouts.app')

@section('title', 'Hierarki Jaringan')

@section('content')

<div class="page-header d-print-none">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title text-primary font-weight-bold">Hierarki Jaringan</h2>
            <div class="text-muted small mt-1">Kelola dan pantau struktur perkembangan tim Anda dalam satu tampilan interaktif.</div>
        </div>
    </div>
</div>

<div class="row row-cards">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center w-100">
                    <h3 class="card-title m-0">Diagram Jaringan Aktif</h3>
                    <div class="ms-auto btn-group bg-light rounded-pill p-1">
                        <button class="btn btn-icon btn-sm btn-ghost-primary rounded-circle border-0" id="zoom-in" title="Zoom In">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        </button>
                        <button class="btn btn-icon btn-sm btn-ghost-primary rounded-circle border-0" id="zoom-out" title="Zoom Out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>
                        </button>
                        <button class="btn btn-icon btn-sm btn-ghost-primary rounded-circle border-0" id="reset-zoom" title="Reset View">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1" /></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 position-relative">
                <div id="tree-container" style="width: 100%; height: 650px;"></div>
                
                <!-- Legend Overlay -->
                <div class="position-absolute bottom-0 start-0 p-3">
                    <div class="bg-white border rounded shadow-sm p-2 small">
                        <div class="d-flex align-items-center mb-1"><span class="bg-primary rounded-circle me-2" style="width:10px; height:10px;"></span> <strong>Biru:</strong> Sponsor (Anda)</div>
                        <div class="d-flex align-items-center"><span class="bg-secondary rounded-circle me-2" style="width:10px; height:10px;"></span> <strong>Abu:</strong> Downline</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Agent -->
<div class="modal modal-blur fade" id="modal-agent-detail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="avatar avatar-xl bg-blue-lt mb-3" id="modal-avatar">??</div>
                <h3 id="modal-name">Nama Agen</h3>
                <div class="text-muted" id="modal-status">Pangkat</div>
                <div class="mt-3">
                    <div class="hr-text">Statistik Tim</div>
                    <div id="modal-downline-count" class="h2 font-weight-bold">0</div>
                    <div class="text-muted small">Total Downline Langsung</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Include D3 for visualization -->
<script src="https://d3js.org/d3.v7.min.js"></script>
<style>
    #tree-container { 
        background: #f8fafc; 
        cursor: grab;
    }
    #tree-container:active { cursor: grabbing; }
    .node-card {
        fill: #ffffff;
        stroke: #e2e8f0;
        stroke-width: 1px;
        rx: 12;
        ry: 12;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .node-card:hover { 
        stroke: #3b82f6; 
        stroke-width: 2px;
    }
    .node-title { font-size: 13px; font-weight: 700; fill: #1e293b; pointer-events: none; }
    .node-subtitle { font-size: 10px; font-weight: 600; fill: #64748b; pointer-events: none; }
    .node-badge-bg { rx: 4; ry: 4; pointer-events: none; }
    .node-badge-text { fill: #ffffff; font-size: 9px; font-weight: 800; pointer-events: none; }
    .node-count-bg { fill: #f1f5f9; rx: 10; ry: 10; cursor: pointer; }
    .node-count-text { fill: #3b82f6; font-size: 10px; font-weight: 800; pointer-events: none; }
    .link { fill: none; stroke: #cbd5e1; stroke-width: 2px; stroke-dasharray: 4,2; }
</style>

<script>
    const data = @json($network);

    const container = document.getElementById('tree-container');
    const width = container.offsetWidth;
    const height = 650;

    const zoom = d3.zoom().on("zoom", (event) => {
        g.attr("transform", event.transform);
    });

    const svg = d3.select("#tree-container").append("svg")
        .attr("width", "100%")
        .attr("height", height)
        .call(zoom);

    const g = svg.append("g");

    const tree = d3.tree().nodeSize([240, 220]);

    const root = d3.hierarchy(data);
    tree(root);

    // Zoom Controls
    d3.select("#zoom-in").on("click", () => svg.transition().call(zoom.scaleBy, 1.3));
    d3.select("#zoom-out").on("click", () => svg.transition().call(zoom.scaleBy, 0.7));
    d3.select("#reset-zoom").on("click", () => svg.transition().call(zoom.transform, initialZoom));

    // Links
    g.selectAll(".link")
        .data(root.descendants().slice(1))
        .enter().append("path")
        .attr("class", "link")
        .attr("d", d => {
            return "M" + d.x + "," + d.y
                + "C" + d.x + "," + (d.y + d.parent.y) / 2
                + " " + d.parent.x + "," + (d.y + d.parent.y) / 2
                + " " + d.parent.x + "," + d.parent.y;
        });

    // Nodes
    const node = g.selectAll(".node")
        .data(root.descendants())
        .enter().append("g")
        .attr("class", "node")
        .attr("transform", d => "translate(" + (d.x - 90) + "," + (d.y - 45) + ")")
        .on("click", (event, d) => showDetail(d.data));

    // Card
    node.append("rect")
        .attr("width", 180)
        .attr("height", 90)
        .attr("class", "node-card")
        .style("filter", "drop-shadow(0 4px 6px -1px rgb(0 0 0 / 0.05))");

    // Indicator Border (Top)
    node.append("rect")
        .attr("width", 180)
        .attr("height", 4)
        .attr("rx", 2)
        .style("fill", d => d.parent ? "#94a3b8" : "#3b82f6");

    // Name
    node.append("text")
        .attr("x", 20)
        .attr("y", 35)
        .attr("class", "node-title")
        .text(d => d.data.name.length > 18 ? d.data.name.substring(0, 16) + "..." : d.data.name);

    // Status
    node.append("text")
        .attr("x", 20)
        .attr("y", 52)
        .attr("class", "node-subtitle")
        .text(d => d.data.status);

    // Count Pill (Floating Bottom Right)
    const countG = node.append("g")
        .attr("transform", "translate(140, 60)");
    
    countG.append("rect")
        .attr("width", 30)
        .attr("height", 20)
        .attr("class", "node-count-bg");

    countG.append("text")
        .attr("x", 15)
        .attr("y", 14)
        .attr("class", "node-count-text")
        .attr("text-anchor", "middle")
        .text(d => d.data.children ? d.data.children.length : 0);

    // Detail Function
    function showDetail(data) {
        document.getElementById('modal-name').innerText = data.name;
        document.getElementById('modal-status').innerText = data.status;
        document.getElementById('modal-avatar').innerText = data.name.substring(0, 2).toUpperCase();
        document.getElementById('modal-downline-count').innerText = data.children ? data.children.length : 0;
        
        const myModal = new bootstrap.Modal(document.getElementById('modal-agent-detail'));
        myModal.show();
    }

    // Initial positioning
    const initialZoom = d3.zoomIdentity.translate(width / 2, 80).scale(0.85);
    svg.call(zoom.transform, initialZoom);

</script>
@endsection
