@extends('layouts.app')
@section('title', 'Kelola Pengguna | Pentasera Admin')

@section('custom-nav'){{-- Admin uses its own sidebar --}}@endsection
@section('custom-footer'){{-- No footer on admin pages --}}@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="admin-dashboard-container" id="admin-dashboard-container">
    <!-- Admin Mobile Top Bar -->
    <div class="admin-mobile-bar">
        <button class="sidebar-toggle-btn" id="sidebar-toggle-btn" aria-label="Open sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <span class="logo-text" style="font-size:14px; letter-spacing:2px; color: var(--admin-accent); font-weight: bold;">PENTASERA</span>
    </div>

    <!-- Sidebar Overlay -->
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay"></div>

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

<!-- User Detail Modal -->
<div class="admin-modal-overlay" id="detail-user-modal">
    <div class="admin-modal detail-modal">
        <!-- Modal Header -->
        <div class="detail-modal-header">
            <h3><i data-lucide="user-circle" class="w-5 h-5"></i> Detail Pengguna</h3>
            <button class="detail-modal-close" onclick="closeDetailUserModal()" title="Tutup">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Loading State -->
        <div class="detail-loading" id="detail-loading">
            <div class="spinner"></div>
            <p>Memuat data pengguna...</p>
        </div>

        <!-- Detail Content (hidden until loaded) -->
        <div class="detail-content" id="detail-content" style="display:none;">
            <!-- Profile Card -->
            <div class="detail-profile-card">
                <div class="detail-avatar" id="detail-avatar">U</div>
                <div class="detail-profile-info">
                    <h4 id="detail-name">-</h4>
                    <p class="detail-email" id="detail-email">-</p>
                </div>
                <span class="role-badge" id="detail-role-badge">-</span>
            </div>

            <!-- Profile Stats -->
            <div class="detail-stats-row">
                <div class="detail-stat-item">
                    <i data-lucide="phone" class="w-4 h-4"></i>
                    <div>
                        <span class="detail-stat-label">No. Telepon</span>
                        <span class="detail-stat-value" id="detail-phone">-</span>
                    </div>
                </div>
                <div class="detail-stat-item">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <div>
                        <span class="detail-stat-label">Status</span>
                        <span class="detail-stat-value" id="detail-status">-</span>
                    </div>
                </div>
                <div class="detail-stat-item">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    <div>
                        <span class="detail-stat-label">Bergabung</span>
                        <span class="detail-stat-value" id="detail-joined">-</span>
                    </div>
                </div>
                <div class="detail-stat-item">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <div>
                        <span class="detail-stat-label">Total Belanja</span>
                        <span class="detail-stat-value accent" id="detail-total-spent">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Transactions Section -->
            <div class="detail-transactions-section">
                <h4><i data-lucide="receipt" class="w-4 h-4"></i> Riwayat Transaksi <span class="detail-tx-count" id="detail-tx-count">0</span></h4>

                <div class="detail-table-wrap">
                    <table class="admin-table detail-table" id="detail-tx-table" style="display:none;">
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Nama Event</th>
                                <th>Tanggal</th>
                                <th>Tiket</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="detail-tx-body">
                        </tbody>
                    </table>
                </div>

                <!-- Empty transactions state -->
                <div class="detail-tx-empty" id="detail-tx-empty" style="display:none;">
                    <i data-lucide="inbox" class="w-6 h-6"></i>
                    <p>Belum ada riwayat transaksi.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="admin-toast-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-users.js') }}?v={{ time() }}"></script>
<script>
(function() {
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar   = document.getElementById('admin-sidebar');
    const overlay   = document.getElementById('admin-sidebar-overlay');

    function openSidebar() {
        if (sidebar)  sidebar.classList.add('open');
        if (overlay)  overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        if (sidebar)  sidebar.classList.remove('open');
        if (overlay)  overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);

    if (sidebar) {
        sidebar.querySelectorAll('a.nav-item').forEach(function(link) {
            link.addEventListener('click', closeSidebar);
        });
    }
})();
</script>
@endpush
