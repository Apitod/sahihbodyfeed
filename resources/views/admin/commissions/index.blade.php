@extends('layouts.app')

@section('title', 'Laporan Komisi')

@section('content')
@php
    $baseRoute = auth()->user()->isSuperAdmin() ? 'superadmin.commissions' : 'admin.commissions';
@endphp
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Laporan Komisi Agen</h2>
                <div class="text-muted mt-1">Kelola dan cairkan komisi agen yang berstatus pending.</div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex gap-2">
                    <a href="{{ route($baseRoute . '.pdf', ['status' => $status]) }}" class="btn btn-primary" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 17v-6" /><path d="M9.5 14.5l2.5 2.5l2.5 -2.5" /></svg>
                        Download PDF {{ $status ? '(' . ucfirst($status) . ')' : '(Semua)' }}
                    </a>
                    @if($status === 'pending' && $countPending > 0)
                        <form action="{{ route($baseRoute . '.mark-paid') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah mentransfer semua komisi pending? Tindakan ini akan mengubah status komisi menjadi Paid.');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Tandai Paid (Massal)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route($baseRoute . '.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">Status Komisi</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->value }}" @selected($status == $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if($status === 'pending')
            <div class="col-12 col-md-8 text-end">
                <div class="text-muted small">Total Komisi Pending Saat Ini</div>
                <div class="h2 mb-0 text-primary">Rp {{ number_format($totalPending) }}</div>
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Penerima</th>
                    <th>Nominal</th>
                    <th>Tipe</th>
                    <th>Gen</th>
                    <th>Status</th>
                    <th>Tanggal Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $comm)
                <tr>
                    <td class="text-muted small">#{{ $comm->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $comm->recipient->nama }}</div>
                        <div class="text-muted small">{{ $comm->recipient->bank_name ?? 'Bank N/A' }} - {{ $comm->recipient->bank_account ?? 'Rek N/A' }}</div>
                    </td>
                    <td class="fw-bold">Rp {{ number_format($comm->amount) }}</td>
                    <td class="text-muted small">
                        {{ match($comm->type->value) {
                            'downline_registration' => 'Registrasi Agen',
                            'repeat_order' => 'Repeat Order',
                            default => $comm->type->value
                        } }}
                    </td>
                    <td class="text-muted">Gen-{{ $comm->generation_level }}</td>
                    <td>
                        @php
                            $badgeColor = match($comm->status->value) {
                                'menunggu' => 'bg-secondary text-white',
                                'pending'  => 'bg-warning text-dark',
                                'paid'     => 'bg-success text-white',
                                default    => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ $comm->status->label() }}</span>
                        @if($comm->transfer_proof)
                            <span class="badge bg-blue-lt text-blue ms-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs" width="12" height="12" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                Bukti Ada
                            </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $comm->created_at->format('d M Y H:i') }}</td>
                    <td class="text-center">
                        {{-- Tombol Konfirmasi: disabled jika masih menunggu atau belum diproses --}}
                        @php $isActionable = in_array($comm->status->value, ['pending', 'paid']); @endphp
                        <button
                            type="button"
                            class="btn btn-sm {{ $isActionable ? 'btn-primary' : 'btn-secondary' }}"
                            @if($isActionable)
                                data-bs-toggle="modal"
                                data-bs-target="#modalKonfirmasi"
                                data-comm-id="{{ $comm->id }}"
                                data-nama="{{ $comm->recipient->nama }}"
                                data-amount="Rp {{ number_format($comm->amount) }}"
                                data-bank="{{ $comm->recipient->bank_name ?? '—' }}"
                                data-rekening="{{ $comm->recipient->bank_account ?? '—' }}"
                                data-atas-nama="{{ $comm->recipient->bank_account_name ?? $comm->recipient->nama }}"
                                data-status="{{ $comm->status->value }}"
                                data-has-proof="{{ $comm->transfer_proof ? 'true' : 'false' }}"
                                data-upload-url="{{ route($baseRoute . '.upload-proof', $comm) }}"
                                data-invoice-url="{{ route($baseRoute . '.invoice', $comm) }}"
                                data-preview-url="{{ route($baseRoute . '.invoice.preview', $comm) }}"
                            @else
                                disabled
                                title="Tombol aktif setelah status menjadi Pending (diproses)"
                            @endif
                        >
                            @if($isActionable)
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                            Konfirmasi
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-xs me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>
                            Menunggu
                            @endif
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Tidak ada data komisi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="card-footer">
        {{ $commissions->links() }}
    </div>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════
     MODAL KONFIRMASI PEMBAYARAN KOMISI
════════════════════════════════════════════════════════ --}}
<div class="modal modal-blur fade" id="modalKonfirmasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11l3 3l8 -8" /><path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" /></svg>
                    Konfirmasi Pembayaran Komisi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                {{-- Summary bar --}}
                <div class="bg-primary bg-opacity-10 border-bottom px-4 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-muted small">Penerima</div>
                            <div class="fw-bold fs-5" id="modal-nama">—</div>
                        </div>
                        <div class="col-auto text-end">
                            <div class="text-muted small">Nominal Transfer</div>
                            <div class="fw-bold fs-4 text-dark" id="modal-amount">—</div>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    {{-- ── LEFT: Invoice ── --}}
                    <div class="col-md-6 border-end p-4">
                        <h6 class="fw-bold mb-3 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9h1" /><path d="M9 13h6" /><path d="M9 17h6" /></svg>
                            Invoice Komisi
                        </h6>

                        {{-- Data ringkasan untuk referensi --}}
                        <div class="mb-3">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted small ps-0" width="40%">Bank</td>
                                        <td class="fw-semibold small" id="modal-bank">—</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small ps-0">No. Rekening</td>
                                        <td class="fw-semibold small" id="modal-rekening">—</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small ps-0">Atas Nama</td>
                                        <td class="fw-semibold small" id="modal-atas-nama">—</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-grid gap-2">
                            <a id="btn-preview-invoice"
                               href="#"
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                Preview Invoice
                            </a>
                            <a id="btn-download-invoice"
                               href="#"
                               target="_blank"
                               class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                Cetak / Download Invoice
                            </a>
                        </div>
                    </div>

                    {{-- ── RIGHT: Upload Bukti Transfer ── --}}
                    <div class="col-md-6 p-4">
                        <h6 class="fw-bold mb-3 text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                            Upload Bukti Transfer
                        </h6>

                        <div id="proof-already-uploaded" class="alert alert-success small py-2 d-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Bukti transfer sudah diupload sebelumnya. Upload ulang untuk mengganti.
                        </div>

                        <form id="formUploadProof" method="POST" enctype="multipart/form-data"
                              action="">
                            @csrf
                            <input type="hidden" id="hidden-upload-url" value="">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">File Bukti Transfer <span class="text-danger">*</span></label>
                                <input type="file"
                                       name="transfer_proof"
                                       id="input-transfer-proof"
                                       class="form-control"
                                       accept=".jpg,.jpeg,.png,.pdf"
                                       required>
                                <div class="form-hint">Format: JPG, PNG, atau PDF. Maks 5MB.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan Pembayaran <span class="text-muted small">(opsional)</span></label>
                                <textarea name="payment_notes"
                                          id="input-payment-notes"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Contoh: Transfer BCA 06/05/2026..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 9l5 -5l5 5" /><path d="M12 4l0 12" /></svg>
                                    Upload &amp; Konfirmasi Pembayaran
                                </button>
                            </div>
                        </form>
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

    // ── 1. Populate modal via direct click listener (tidak bergantung relatedTarget) ──
    document.querySelectorAll('[data-bs-target="#modalKonfirmasi"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const d = this.dataset;

            // Summary
            document.getElementById('modal-nama').textContent      = d.nama    || '—';
            document.getElementById('modal-amount').textContent    = d.amount  || '—';
            document.getElementById('modal-bank').textContent      = d.bank    || '—';
            document.getElementById('modal-rekening').textContent  = d.rekening || '—';
            document.getElementById('modal-atas-nama').textContent = d.atasNama || '—';

            // Invoice links
            document.getElementById('btn-preview-invoice').href  = d.previewUrl || '#';
            document.getElementById('btn-download-invoice').href = d.invoiceUrl  || '#';

            // Upload URL — store globally on the form element itself
            const form = document.getElementById('formUploadProof');
            form._uploadUrl = d.uploadUrl || '';

            // Already-uploaded notice
            const notice = document.getElementById('proof-already-uploaded');
            const fileInput = document.getElementById('input-transfer-proof');
            if (d.hasProof === 'true') {
                notice.classList.remove('d-none');
                fileInput.removeAttribute('required');
            } else {
                notice.classList.add('d-none');
                fileInput.setAttribute('required', 'required');
            }

            // Reset fields
            fileInput.value = '';
            document.getElementById('input-payment-notes').value = '';
        });
    });

    // ── 2. Form submit via fetch (100% bypass browser action) ──────────────────────
    const form = document.getElementById('formUploadProof');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const url = form._uploadUrl;
        if (!url || url.trim() === '') {
            alert('Gagal: URL upload tidak ditemukan. Tutup modal dan coba lagi.');
            return;
        }

        const submitBtn = form.querySelector('button[type=submit]');
        const origHTML  = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Mengupload...';

        fetch(url, {
            method: 'POST',
            body:   new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(function (response) {
            // Laravel's back()->with() menyebabkan redirect — ikuti ke halaman yg benar
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                window.location.reload();
            }
        })
        .catch(function (err) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origHTML;
            alert('Error: ' + err.message);
        });
    });

});
</script>
@endsection

