@extends('layouts.app')
@section('title', 'Manajemen Admin')

@section('content')
<style>
    .admin-avatar { width: 2.25rem; height: 2.25rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .8rem; font-weight: 800; flex-shrink: 0; }
</style>

{{-- Page Header --}}
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Manajemen Admin</h2>
                <div class="text-muted small mt-1">Kelola akun Admin (Tier-2) yang dapat memverifikasi transaksi dan agen.</div>
            </div>
            <div class="col-auto ms-auto d-flex gap-2">
                <a href="{{ route('superadmin.agents.index') }}" class="btn btn-outline-secondary" id="btn-go-agents">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"/><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/></svg>
                    Kelola Agen
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-admin" id="btn-create-admin">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                    Tambah Admin
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
<div class="container-xl mt-3">
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert">
    <div class="d-flex align-items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
        {{ session('success') }}
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Search Bar --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('superadmin.admins.index') }}" class="row g-2 align-items-end" id="admin-filter-form">
            <div class="col-12 col-md-5">
                <label class="form-label text-muted small mb-1">Cari Admin</label>
                <input type="text" name="search" id="admin-search-input" class="form-control"
                    placeholder="Nama atau username…" value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label text-muted small mb-1">Filter Status</label>
                <select name="status" id="admin-filter-status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" id="admin-btn-filter">Filter</button>
                <a href="{{ route('superadmin.admins.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header d-flex align-items-center py-3">
        <h3 class="card-title mb-0">Daftar Admin</h3>
        <span class="badge bg-blue-lt ms-2">{{ $admins->total() }} total</span>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table" id="admins-table">
            <thead>
                <tr>
                    <th class="text-muted small">#</th>
                    <th class="text-muted small">Nama / Username</th>
                    <th class="text-muted small">Email</th>
                    <th class="text-muted small">No. Telp</th>
                    <th class="text-muted small">Status</th>
                    <th class="text-muted small">Dibuat</th>
                    <th class="text-muted small text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr id="admin-row-{{ $admin->id }}">
                    <td class="text-muted small">{{ $admin->id }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="admin-avatar" style="background: linear-gradient(135deg, var(--brand-4), #d48a3a); color:#fff;">
                                {{ strtoupper(substr($admin->nama ?? $admin->username, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $admin->nama ?? '—' }}</div>
                                <div class="text-muted small">{{ $admin->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $admin->email ?? '—' }}</td>
                    <td class="text-muted small">{{ $admin->no_telp ?? '—' }}</td>
                    <td>
                        @if($admin->is_active)
                            <span class="badge bg-green-lt text-green">Aktif</span>
                        @else
                            <span class="badge bg-red-lt text-red">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $admin->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            {{-- Edit --}}
                            <button type="button" class="btn btn-sm btn-ghost-secondary btn-edit-admin" title="Edit"
                                data-id="{{ $admin->id }}"
                                data-nama="{{ $admin->nama }}"
                                data-username="{{ $admin->username }}"
                                data-email="{{ $admin->email }}"
                                data-no_telp="{{ $admin->no_telp }}"
                                data-bs-toggle="modal" data-bs-target="#modal-edit-admin">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/></svg>
                            </button>

                            {{-- Toggle Active --}}
                            @if($admin->is_active)
                            <form method="POST" action="{{ route('superadmin.admins.suspend', $admin) }}"
                                id="suspend-admin-{{ $admin->id }}"
                                onsubmit="return confirm('Yakin ingin mensuspend admin {{ $admin->username }}?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-ghost-warning" title="Suspend">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z"/><path d="M14 4h1a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-1a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z"/></svg>
                                </button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('superadmin.admins.activate', $admin) }}"
                                id="activate-admin-{{ $admin->id }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-ghost-success" title="Aktifkan">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                </button>
                            </form>
                            @endif

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('superadmin.admins.destroy', $admin) }}"
                                id="delete-admin-{{ $admin->id }}"
                                onsubmit="return confirm('Hapus akun admin {{ $admin->username }} secara permanen?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost-danger" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0"/><path d="M10 11l0 6"/><path d="M14 11l0 6"/><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <div class="fs-3 mb-2">👤</div>
                        <div class="fw-bold">Belum ada akun admin.</div>
                        <div class="small mt-1">Klik "Tambah Admin" untuk membuat akun admin pertama.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($admins->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted small">
            Menampilkan <strong>{{ $admins->firstItem() }}–{{ $admins->lastItem() }}</strong>
            dari <strong>{{ $admins->total() }}</strong> admin
        </p>
        <div class="ms-auto">{{ $admins->links() }}</div>
    </div>
    @endif
</div>
</div>{{-- /container-xl --}}

{{-- ═══════════════════ MODAL: TAMBAH ADMIN ═══════════════════ --}}
<div class="modal modal-blur fade" id="modal-create-admin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('superadmin.admins.store') }}" id="form-create-admin">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Akun Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Isi data pribadi dan kredensial untuk akun Admin baru.</p>

                    {{-- DATA PRIBADI --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="create-nama">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" id="create-nama" class="form-control @error('nama') is-invalid @enderror"
                            placeholder="Contoh: Budi Santoso" value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="create-email">Email</label>
                        <input type="email" name="email" id="create-email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="admin@example.com" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="create-no_telp">No. Telepon</label>
                        <input type="text" name="no_telp" id="create-no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                            placeholder="08xxxxxxxxxx" value="{{ old('no_telp') }}">
                        @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <hr class="my-3">

                    {{-- KREDENSIAL AKUN --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="create-username">
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="username" id="create-username" class="form-control @error('username') is-invalid @enderror"
                            placeholder="Unik, tanpa spasi" value="{{ old('username') }}" autocomplete="off" required>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="create-password">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password" id="create-password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold" for="create-password-confirmation">
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="create-password-confirmation"
                            class="form-control" placeholder="Ulangi password" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-create-admin">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14"/><path d="M5 12l14 0"/></svg>
                        Buat Akun Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════ MODAL: EDIT ADMIN ═══════════════════ --}}
<div class="modal modal-blur fade" id="modal-edit-admin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" id="form-edit-admin">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Akun Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="edit-nama">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="edit-email">Email</label>
                        <input type="email" name="email" id="edit-email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="edit-no_telp">No. Telepon</label>
                        <input type="text" name="no_telp" id="edit-no_telp" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <hr class="my-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="edit-username">
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="username" id="edit-username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="edit-password">Password Baru</label>
                        <input type="password" name="password" id="edit-password" class="form-control"
                            placeholder="Kosongkan jika tidak ingin mengubah" autocomplete="new-password">
                        <div class="form-hint">Isi hanya jika ingin mengganti password.</div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" for="edit-password-confirmation">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="edit-password-confirmation"
                            class="form-control" placeholder="Ulangi password baru" autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-edit-admin">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Populate edit modal with admin data
document.querySelectorAll('.btn-edit-admin').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id       = this.dataset.id;
        var nama     = this.dataset.nama || '';
        var username = this.dataset.username || '';
        var email    = this.dataset.email || '';
        var no_telp  = this.dataset.no_telp || '';

        document.getElementById('edit-nama').value     = nama;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-email').value    = email;
        document.getElementById('edit-no_telp').value  = no_telp;
        document.getElementById('edit-password').value = '';
        document.getElementById('edit-password-confirmation').value = '';

        // Set form action dynamically
        document.getElementById('form-edit-admin').action = '/superadmin/admins/' + id;
    });
});

// Auto-open create modal if validation errors exist (on page reload)
@if($errors->any())
    var modalEl = document.getElementById('modal-create-admin');
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
@endif
</script>
@endpush
@endsection
