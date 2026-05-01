@extends('layouts.app')

@section('title', 'Verifikasi Transaksi')

@section('content')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">Verifikasi Transaksi Agen</h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.verifications.ro.create') }}" class="btn btn-primary" id="btn-buat-ro">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Buat Transaksi RO
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Agen</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                            <th>Bukti Bayar</th>
                            <th>Status</th>
                            <th>Verifikator</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td>
                                <span class="text-muted">#{{ $txn->id }}</span><br>
                                <small>{{ $txn->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $txn->agent->nama }}</div>
                                <div class="text-muted small">User: {{ $txn->agent->user->username ?? '-' }}</div>
                                @if($txn->agent->no_telp)
                                    <div class="small mt-1 text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-inline" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                        </svg>
                                        {{ $txn->agent->no_telp }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($txn->type->value === 'new_agent')
                                    <span class="badge bg-purple-lt">{{ $txn->type->label() }}</span>
                                @else
                                    <span class="badge bg-blue-lt">{{ $txn->type->label() }}</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format((float)$txn->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($txn->proof_of_payment)
                                    <a href="{{ asset('storage/' . $txn->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Bukti</a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                @if($txn->status->value === 'pending')
                                    <span class="badge bg-yellow text-yellow-fg">{{ $txn->status->label() }}</span>
                                @elseif($txn->status->value === 'verified')
                                    <span class="badge bg-green text-green-fg">{{ $txn->status->label() }}</span>
                                @else
                                    <span class="badge bg-red text-red-fg">{{ $txn->status->label() }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $txn->verifier->username ?? '-' }}<br>
                                <small>{{ $txn->verified_at ? $txn->verified_at->format('d/m H:i') : '' }}</small>
                            </td>
                            <td>
                                @if($txn->status->value === 'pending')
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('admin.transactions.approve', $txn->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success"
                                                onclick="return confirm('Peringatan: Konfirmasi pembayaran valid? Ini akan memicu pembagian komisi/poin.');">
                                                Valid
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.transactions.reject', $txn->id) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Tolak transaksi ini?');">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>

                                    @if($txn->type->value === 'new_agent' && $txn->agent->no_telp)
                                        @php
                                            $waNumber  = preg_replace('/^0/', '62', $txn->agent->no_telp);
                                            $waMessage = urlencode("Halo {$txn->agent->nama}, selamat bergabung di Sahihbodyfeed! Akun Anda sudah aktif. Silakan login untuk mulai berjualan. 🎉");
                                            $waUrl = "https://wa.me/{$waNumber}?text={$waMessage}";
                                        @endphp
                                        <button type="button" class="btn btn-primary btn-sm mt-2 w-100" onclick="window.open('{{ $waUrl }}', '_blank'); document.getElementById('form-approve-{{ $txn->id }}').submit();">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                            Lanjutkan & Direct WA
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
