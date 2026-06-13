@extends('layouts.app')
@section('title', 'Kelola Pengguna | Pentasera Admin')

@section('custom-nav'){{-- Admin uses its own sidebar --}}@endsection
@section('custom-footer'){{-- No footer on admin pages --}}@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="admin-dashboard-container">
    <!-- Admin Sidebar -->
    @include('admin.partials.sidebar', ['activePage' => 'users'])

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <div>
                <div class="admin-breadcrumb"><a href="{{ url('/') }}">Home</a> / <a href="{{ url('/admin/dashboard') }}">Admin</a> / <span>Kelola Pengguna</span></div>
                <h1>Kelola Pengguna</h1>
            </div>
        </div>

        <!-- Users Section -->
        <div class="admin-section">
            <!-- Search & Filter Toolbar -->
            <div class="admin-toolbar">
                <div class="admin-search-wrap">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <input type="text" class="admin-search-input" id="user-search" placeholder="Cari nama atau email pengguna...">
                </div>
                <div class="admin-filter-tabs" id="role-filter-tabs">
                    <button class="admin-filter-tab active" data-role="">Semua</button>
                    <button class="admin-filter-tab" data-role="buyer">Buyer</button>
                    <button class="admin-filter-tab" data-role="creator">Creator</button>
                    <button class="admin-filter-tab" data-role="admin">Admin</button>
                </div>
            </div>

            <!-- Loading -->
            <div class="admin-loading" id="users-loading">
                <div class="spinner"></div>
                <p>Memuat data pengguna...</p>
            </div>

            <!-- Users Table -->
            <table class="admin-table" id="users-table" style="display: none;">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terdaftar</th>
                        <th>Orders</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <!-- Rows inserted by JS -->
                </tbody>
            </table>

            <!-- Empty State -->
            <div class="admin-empty-state" id="users-empty" style="display: none;">
                <div class="empty-icon">
                    <i data-lucide="user-x" class="w-8 h-8"></i>
                </div>
                <h3>Tidak Ada Pengguna</h3>
                <p>Tidak ada pengguna yang sesuai dengan filter Anda.</p>
            </div>

            <!-- Pagination -->
            <div class="admin-pagination" id="users-pagination" style="display: none;"></div>
        </div>
    </main>
</div>

<!-- Edit User Modal -->
<div class="admin-modal-overlay" id="edit-user-modal">
    <div class="admin-modal">
        <h3>Ubah Role Pengguna</h3>
        <p id="edit-user-info">-</p>
        <input type="hidden" id="edit-user-id">
        <div style="margin-bottom: 16px;">
            <label style="display:block; font-size:12px; font-weight:700; color:var(--admin-text-dim); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Role Baru</label>
            <select id="edit-user-role" style="width:100%; padding:10px 14px; border:1px solid var(--admin-border); border-radius:10px; font-size:13px; font-family:'Inter',sans-serif; color:var(--admin-text); background:var(--admin-bg); outline:none;">
                <option value="buyer">Buyer</option>
                <option value="creator">Creator</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="admin-modal-actions">
            <button class="btn-cancel" onclick="closeEditUserModal()">Batal</button>
            <button class="btn-admin-primary" id="btn-confirm-edit" onclick="confirmEditUser()">Simpan</button>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="admin-modal-overlay" id="delete-user-modal">
    <div class="admin-modal">
        <h3>Hapus Pengguna</h3>
        <p id="delete-user-info">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
        <input type="hidden" id="delete-user-id">
        <div class="admin-modal-actions">
            <button class="btn-cancel" onclick="closeDeleteUserModal()">Batal</button>
            <button class="btn-confirm-reject" id="btn-confirm-delete" onclick="confirmDeleteUser()">Hapus Pengguna</button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="admin-toast-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-users.js') }}?v={{ time() }}"></script>
@endpush
