@extends('layouts.app')

@section('title', 'Komisi Saya')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Komisi Transaksi Downline</h2>
                <div class="text-muted mt-1">Riwayat komisi dari downline Anda berdasarkan level generasi.</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form action="{{ route('agent.commissions') }}" method="GET" id="filterForm" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label">Level Generasi</label>
                <select name="generation_level" class="form-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Level</option>
                    <option value="1" {{ request('generation_level') == '1' ? 'selected' : '' }}>Generasi 1</option>
                    <option value="2" {{ request('generation_level') == '2' ? 'selected' : '' }}>Generasi 2</option>
                    <option value="3" {{ request('generation_level') == '3' ? 'selected' : '' }}>Generasi 3</option>
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Info cards (dari $stats, bukan paginator) --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small">Total Komisi</div>
            <div class="h4 mb-0 text-primary">Rp {{ number_format($stats['total']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small mb-1">Sudah Dibayar</div>
            <div class="h4 mb-0 text-success">Rp {{ number_format($stats['paid']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small mb-1">Sedang Diproses</div>
            <div class="h4 mb-0 text-warning">Rp {{ number_format($stats['pending']) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small mb-1">Menunggu</div>
            <div class="h4 mb-0 text-secondary">Rp {{ number_format($stats['menunggu']) }}</div>
        </div>
    </div>
</div>

{{-- Keterangan status --}}
<div class="alert alert-info alert-dismissible mb-3" role="alert">
    <div class="d-flex align-items-start">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 mt-1 flex-shrink-0" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><path d="M12 8h.01" /><path d="M11 12h1v4h1" /></svg>
        <div>
            <strong>Keterangan Status Komisi:</strong>
            <span class="badge bg-secondary text-light ms-2">Menunggu</span> Proses setelah 1 hari kerja.
            <span class="badge bg-warning text-light ms-2">Diproses</span> Sedang diproses admin, menunggu pencairan.
            <span class="badge bg-success text-light ms-2">Lunas</span> Sudah ditransfer ke rekening Anda.
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Tipe Komisi</th>
                    <th>Level</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $comm)
                <tr>
                    <td class="text-muted small">#{{ $comm->transaction_id }}</td>
                    <td class="text-muted small">{{ $comm->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $comm->type->label() }}</td>
                    <td><span class="badge bg-blue-lt text-blue">Gen-{{ $comm->generation_level }}</span></td>
                    <td class="fw-bold text-success">Rp {{ number_format((float)$comm->amount, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $statusMap = match($comm->status->value) {
                                'menunggu' => ['bg-secondary text-white', 'Menunggu',    'Sedang antri — akan diproses besok oleh sistem.'],
                                'pending'  => ['bg-warning text-dark',   'Diproses',    'Sedang diproses admin, menunggu pencairan.'],
                                'paid'     => ['bg-success text-white',  'Lunas',       'Komisi sudah ditransfer ke rekening Anda.'],
                                default    => ['bg-secondary',          $comm->status->value, ''],
                            };
                        @endphp
                        <span class="badge {{ $statusMap[0] }}"
                              data-bs-toggle="tooltip"
                              title="{{ $statusMap[2] }}">
                            {{ $statusMap[1] }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($comm->status->value === 'paid' && $comm->transfer_proof)
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-success"
                                data-bs-toggle="modal"
                                data-bs-target="#modalLihatBukti"
                                data-comm-id="{{ $comm->id }}"
                                data-nominal="Rp {{ number_format((float)$comm->amount, 0, ',', '.') }}"
                                data-tanggal-bayar="{{ $comm->paid_at ? $comm->paid_at->timezone('Asia/Makassar')->format('d M Y H:i') . ' WITA' : '—' }}"
                                data-notes="{{ $comm->payment_notes ?? '' }}"
                                data-proof-url="{{ Storage::url($comm->transfer_proof) }}"
                                data-proof-is-pdf="{{ str_ends_with($comm->transfer_proof, '.pdf') ? 'true' : 'false' }}"
                                data-invoice-url="{{ route('agent.commissions.invoice', $comm) }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                Lihat Bukti
                            </button>
                        @elseif($comm->status->value === 'paid')
                            <span class="text-muted small fst-italic">Bukti belum diupload</span>
                        @elseif($comm->status->value === 'pending')
                            <span class="badge bg-blue-lt text-blue">Sedang diproses</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada komisi transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex align-items-center">
        {{ $commissions->links() }}
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL: LIHAT BUKTI TRANSFER & INVOICE (Agent view)
════════════════════════════════════════════════════════ --}}
<div class="modal modal-blur fade" id="modalLihatBukti" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Bukti Pembayaran Komisi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                {{-- Summary bar --}}
                <div class="bg-success bg-opacity-10 border-bottom px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-muted small">Nominal Komisi</div>
                            <div class="fw-bold fs-4 text-dark" id="agent-modal-nominal">—</div>
                        </div>
                        <div class="col-auto text-end">
                            <div class="text-muted small">Tanggal Dibayar</div>
                            <div class="fw-semibold" id="agent-modal-tanggal">—</div>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    {{-- Kiri: Preview Bukti Transfer --}}
                    <div class="col-md-7 border-end p-4">
                        <h6 class="fw-bold mb-3 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><rect x="4" y="4" width="16" height="16" rx="3" /><path d="M4 9h16" /><path d="M9 15l2 2l4 -4" /></svg>
                            Bukti Transfer Admin
                        </h6>

                        {{-- Image proof --}}
                        <div id="agent-proof-image-wrap">
                            <img id="agent-proof-img"
                                 src=""
                                 alt="Bukti Transfer"
                                 class="img-fluid rounded border shadow-sm"
                                 style="max-height:280px; object-fit:contain; width:100%; display:none;">
                        </div>

                        {{-- PDF proof --}}
                        <div id="agent-proof-pdf-wrap" class="d-none">
                            <div class="alert alert-info py-2 small mb-2">
                                File bukti berupa PDF.
                            </div>
                            <a id="agent-proof-pdf-link" href="#" target="_blank" class="btn btn-outline-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                Buka / Unduh PDF Bukti Transfer
                            </a>
                        </div>

                        {{-- Notes --}}
                        <div id="agent-modal-notes-wrap" class="mt-3 d-none">
                            <div class="text-muted small fw-semibold mb-1">Catatan dari Admin:</div>
                            <div class="bg-light rounded p-2 small fst-italic" id="agent-modal-notes"></div>
                        </div>
                    </div>

                    {{-- Kanan: Invoice --}}
                    <div class="col-md-5 p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-2 text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9h1" /><path d="M9 13h6" /><path d="M9 17h6" /></svg>
                                Invoice Komisi
                            </h6>
                            <p class="text-muted small mb-0">
                                Unduh invoice resmi sebagai bukti penerimaan komisi Anda. Invoice berisi nominal, data rekening, dan detail transaksi.
                            </p>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a id="agent-btn-invoice"
                               href="#"
                               target="_blank"
                               class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                Preview &amp; Cetak Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ── Populate modal via direct click listener (agent side) ──
    document.querySelectorAll('[data-bs-target="#modalLihatBukti"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const d = this.dataset;

            // Summary text
            document.getElementById('agent-modal-nominal').textContent  = d.nominal || '—';
            document.getElementById('agent-modal-tanggal').textContent  = d.tanggalBayar || '—';

            // Notes
            const notesWrap = document.getElementById('agent-modal-notes-wrap');
            const notesEl   = document.getElementById('agent-modal-notes');
            if (d.notes && d.notes.trim() !== '') {
                notesWrap.classList.remove('d-none');
                notesEl.textContent = d.notes;
            } else {
                notesWrap.classList.add('d-none');
            }

            // Proof (image or PDF)
            const proofUrl  = d.proofUrl;
            const isPdf     = d.proofIsPdf === 'true';
            const imgWrap   = document.getElementById('agent-proof-image-wrap');
            const pdfWrap   = document.getElementById('agent-proof-pdf-wrap');
            const img       = document.getElementById('agent-proof-img');
            const pdfLink   = document.getElementById('agent-proof-pdf-link');

            if (isPdf) {
                imgWrap.classList.add('d-none');
                pdfWrap.classList.remove('d-none');
                pdfLink.href = proofUrl;
                img.style.display = 'none';
            } else {
                imgWrap.classList.remove('d-none');
                pdfWrap.classList.add('d-none');
                img.src = proofUrl;
                img.style.display = 'block';
            }

            // Invoice URL
            document.getElementById('agent-btn-invoice').href = d.invoiceUrl || '#';
        });
    });
});
</script>
@endsection

