@extends('layouts.dashboard')
@section('title', 'Admin Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/admins.css') }}">
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h1>Admin Management</h1>
            <p>Kelola akun admin dan hak akses di platform Digitalance.</p>
        </div>
        <div class="page-header-right">
            <button class="btn-primary" id="btn-add-admin">
                <i class="ri-shield-user-line"></i> Tambah Admin
            </button>
        </div>
    </div>

    <div class="stats-row" id="stats-row">
        <div class="stat-card">
            <div class="stat-icon teal"><i class="ri-shield-user-line"></i></div>
            <div class="stat-text"><span class="stat-value">{{ count($administrators) }}</span><span
                    class="stat-label">Total Admin</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="ri-checkbox-circle-line"></i></div>
            <div class="stat-text"><span class="stat-value">0 </span><span class="stat-label">Admin Aktif</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="ri-moon-line"></i></div>
            <div class="stat-text"><span class="stat-value">0 </span><span class="stat-label">Nonaktif</span></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="ri-forbid-line"></i></div>
            <div class="stat-text"><span class="stat-value">0 </span><span class="stat-label">Suspended</span>
            </div>
        </div>
    </div>

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">Semua</button>
        <button class="filter-tab" data-filter="Active">Aktif</button>
        <button class="filter-tab" data-filter="Inactive">Nonaktif</button>
        <button class="filter-tab" data-filter="Suspended">Suspended</button>
    </div>

    <div class="admin-card-grid" id="admin-card-grid">
        @foreach ($administrators as $admin)
            <div class="user-card-item">
                <div class="user-card-top">
                    <div class="user-card-avatar-wrap">
                        <img class="user-card-avatar" src="https://picsum.photos/seed/admin/200/200"
                            alt="{{ $admin->name }}" />
                        <span class="user-online-dot ${STATUS_DOT_MAP[a.status] ?? 'offline'}"></span>
                    </div>
                    <span class="status-pill">
                        <i class="ri-circle-fill" style="font-size:7px;"></i> status
                    </span>
                </div>
                <h3 class="user-card-name">{{ $admin->name }}</h3>
                <span class="user-card-role"><i class="ri-shield-user-line"></i> Admin</span>
                <span class="user-card-location"><i class="ri-mail-line"></i> {{ $admin->email }}</span>
                <button class="btn-detail" style="margin-top:auto;" onclick="openAdminModal(${a.id})">
                    <i class="ri-eye-line"></i> Lihat Profil
                </button>
            </div>
        @endforeach
    </div>
@endsection

@section('modals')
    <div class="modal-overlay" id="admin-modal-overlay">
        <div class="modal-box" id="admin-modal-box"></div>
    </div>

    <div class="modal-overlay" id="modalCreate">
        <form action="" method="post" class="modal-content">
            @csrf
            <div class="modal-header">
                <h2>Tambah Admin Baru</h2>
                <button class="close-modal" id="btn-close-add-admin"><i class="ri-close-line"></i></button>
            </div>
            <form id="form-add-admin">
                <div class="form-row">
                    <div class="form-group"><label>Nama Lengkap</label><input type="text" id="add-admin-name" required
                            placeholder="e.g. Budi Santoso" /></div>
                    <div class="form-group"><label>Email</label><input type="email" id="add-admin-email" required
                            placeholder="budi@digitalance.id" /></div>
                </div>
                <div class="form-group"><label>No. Telepon</label><input type="text" id="add-admin-phone"
                        placeholder="+62 812-xxxx-xxxx" /></div>
                <div class="form-group"><label>Bio</label>
                    <textarea id="add-admin-bio" rows="2" placeholder="Deskripsi singkat..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-secondary" id="btn-cancel-add-admin">Batal</button>
                    <button type="submit" class="btn-primary"><i class="ri-save-line"></i> Simpan Admin</button>
                </div>
            </form>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/dashboard/admin/admins.js') }}"></script>
@endsection
