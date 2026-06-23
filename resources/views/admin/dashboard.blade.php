@extends('layouts.app')
@section('title', 'Event Pending | Admin Pentasera')

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
    @include('admin.partials.sidebar', ['activePage' => 'dashboard'])

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <div>
                <div class="admin-breadcrumb"><a href="{{ url('/') }}">Home</a> / <span>Event Pending</span></div>
                <h1>Event Pending</h1>
                <div class="admin-date" id="admin-date-display"></div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="stat-icon users">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Pengguna</div>
                <div class="stat-value" id="stat-total-users">—</div>
                <div class="stat-sub" id="stat-users-detail">Memuat...</div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-icon events">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Event</div>
                <div class="stat-value" id="stat-total-events">—</div>
                <div class="stat-sub" id="stat-events-detail">Memuat...</div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-icon pending">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Menunggu Persetujuan</div>
                <div class="stat-value" id="stat-pending">—</div>
                <div class="stat-sub">Event perlu ditinjau</div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-icon revenue">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value" id="stat-revenue">—</div>
                <div class="stat-sub" id="stat-transactions-detail">Memuat...</div>
            </div>
        </div>

        <!-- Pending Events Table -->
        <div class="admin-section" id="pending-section">
            <div class="admin-section-header">
                <h2>
                    <i data-lucide="alert-circle" class="w-5 h-5" style="color: var(--admin-warning);"></i>
                    Event Menunggu Persetujuan
                    <span class="pending-count" id="pending-count-badge" style="display:none;">0</span>
                </h2>
            </div>

            <div id="pending-events-container">
                <!-- Loading state -->
                <div class="admin-loading" id="pending-loading">
                    <div class="spinner"></div>
                    <p>Memuat data event...</p>
                </div>

                <!-- Table (hidden initially) -->
                <table class="admin-table" id="pending-events-table" style="display: none;">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Tiket</th>
                            <th>Diajukan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pending-events-body">
                        <!-- Rows inserted by JS -->
                    </tbody>
                </table>

                <!-- Empty state (hidden initially) -->
                <div class="admin-empty-state" id="pending-empty" style="display: none;">
                    <div class="empty-icon">
                        <i data-lucide="check-circle" class="w-8 h-8"></i>
                    </div>
                    <h3>Semua Beres!</h3>
                    <p>Tidak ada event yang menunggu persetujuan saat ini.</p>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Reject Modal -->
<div class="admin-modal-overlay" id="reject-modal">
    <div class="admin-modal">
        <h3>Tolak Event</h3>
        <p>Berikan alasan penolakan agar creator bisa memperbaiki event-nya.</p>
        <input type="hidden" id="reject-event-id">
        <textarea id="reject-reason" placeholder="Contoh: Deskripsi event kurang lengkap, silakan tambahkan detail waktu dan lokasi..."></textarea>
        <div class="admin-modal-actions">
            <button class="btn-cancel" onclick="closeRejectModal()">Batal</button>
            <button class="btn-confirm-reject" id="btn-confirm-reject" onclick="confirmReject()">
                Tolak Event
            </button>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="admin-toast-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-dashboard.js') }}?v={{ time() }}"></script>
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
