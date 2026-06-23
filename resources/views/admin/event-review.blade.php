@extends('layouts.app')
@section('title', 'Review Event | Admin Pentasera')

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
        <!-- Loading State -->
        <div class="admin-loading" id="review-loading">
            <div class="spinner"></div>
            <p>Memuat detail event...</p>
        </div>

        <!-- Error State -->
        <div class="review-error-state" id="review-error" style="display: none;">
            <div class="empty-icon">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>
            <h3>Event Tidak Ditemukan</h3>
            <p id="review-error-msg">Event yang dicari tidak ditemukan atau sudah diproses.</p>
            <a href="{{ url('/admin/dashboard') }}" class="btn-admin-primary" style="margin-top: 16px; text-decoration: none;">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Content (hidden initially) -->
        <div id="review-content" style="display: none;">
            <!-- Header -->
            <div class="admin-header">
                <div>
                    <div class="admin-breadcrumb">
                        <a href="{{ url('/') }}">Home</a> /
                        <a href="{{ url('/admin/dashboard') }}">Event Pending</a> /
                        <span>Review Event</span>
                    </div>
                    <h1 id="review-title">Review Event</h1>
                </div>
                <a href="{{ url('/admin/dashboard') }}" class="btn-admin-secondary" id="btn-back">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>

            <!-- Event Banner -->
            <div class="review-banner-section">
                <div class="review-banner-wrap">
                    <img id="review-banner" src="" alt="Event Banner" class="review-banner-img">
                    <div class="review-banner-overlay">
                        <div class="review-status-badge" id="review-status-badge">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            Menunggu Persetujuan
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="review-grid">
                <!-- Left: Event Details -->
                <div class="review-left">
                    <!-- Event Info Card -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i data-lucide="info" class="w-5 h-5"></i>
                            <h2>Informasi Event</h2>
                        </div>
                        <div class="review-card-body">
                            <div class="review-detail-row">
                                <div class="review-detail-label">
                                    <i data-lucide="type" class="w-4 h-4"></i> Nama Event
                                </div>
                                <div class="review-detail-value" id="detail-nama">-</div>
                            </div>
                            <div class="review-detail-row">
                                <div class="review-detail-label">
                                    <i data-lucide="tag" class="w-4 h-4"></i> Kategori
                                </div>
                                <div class="review-detail-value">
                                    <span class="event-category" id="detail-kategori">-</span>
                                </div>
                            </div>
                            <div class="review-detail-row">
                                <div class="review-detail-label">
                                    <i data-lucide="calendar" class="w-4 h-4"></i> Tanggal & Waktu
                                </div>
                                <div class="review-detail-value" id="detail-datetime">-</div>
                            </div>
                            <div class="review-detail-row">
                                <div class="review-detail-label">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i> Lokasi
                                </div>
                                <div class="review-detail-value" id="detail-lokasi">-</div>
                            </div>
                            <div class="review-detail-row">
                                <div class="review-detail-label">
                                    <i data-lucide="clock" class="w-4 h-4"></i> Diajukan Pada
                                </div>
                                <div class="review-detail-value" id="detail-submitted">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                            <h2>Deskripsi Event</h2>
                        </div>
                        <div class="review-card-body">
                            <div class="review-description" id="detail-deskripsi">
                                <p>Belum ada deskripsi.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets Card -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i data-lucide="ticket" class="w-5 h-5"></i>
                            <h2>Daftar Tiket</h2>
                            <span class="review-ticket-count" id="ticket-count">0</span>
                        </div>
                        <div class="review-card-body" id="tickets-container">
                            <div class="admin-empty-state" id="tickets-empty">
                                <p>Belum ada tiket yang ditambahkan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Creator Info & Actions -->
                <div class="review-right">
                    <!-- Creator Card -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            <h2>Penyelenggara</h2>
                        </div>
                        <div class="review-card-body">
                            <div class="review-creator-info">
                                <div class="review-creator-avatar" id="creator-avatar">C</div>
                                <div>
                                    <div class="review-creator-name" id="creator-name">-</div>
                                    <div class="review-creator-email" id="creator-email">-</div>
                                </div>
                            </div>
                            <div class="review-creator-org">
                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                <span id="creator-organizer">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Event Rules Card -->
                    <div class="review-card">
                        <div class="review-card-header">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                            <h2>Aturan Event</h2>
                        </div>
                        <div class="review-card-body">
                            <div class="review-rule-item">
                                <div class="review-rule-label">Maks Tiket/Transaksi</div>
                                <div class="review-rule-value" id="rule-max-ticket">-</div>
                            </div>
                            <div class="review-rule-item">
                                <div class="review-rule-label">1 Email = 1 Transaksi</div>
                                <div class="review-rule-value" id="rule-one-email">-</div>
                            </div>
                            <div class="review-rule-item">
                                <div class="review-rule-label">Identitas Unik per Tiket</div>
                                <div class="review-rule-value" id="rule-identity">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Card -->
                    <div class="review-card review-summary-card">
                        <div class="review-card-header">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            <h2>Ringkasan</h2>
                        </div>
                        <div class="review-card-body">
                            <div class="review-summary-grid">
                                <div class="review-summary-item">
                                    <div class="review-summary-value" id="summary-tickets">0</div>
                                    <div class="review-summary-label">Tipe Tiket</div>
                                </div>
                                <div class="review-summary-item">
                                    <div class="review-summary-value" id="summary-capacity">0</div>
                                    <div class="review-summary-label">Total Kuota</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="review-actions-card">
                        <button class="review-btn-approve" id="btn-approve-event" onclick="approveEvent()">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Setujui Event
                        </button>
                        <button class="review-btn-reject" id="btn-reject-event" onclick="openRejectModal()">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            Tolak Event
                        </button>
                    </div>
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
<script>
    const REVIEW_EVENT_ID = {{ $eventId }};
</script>
<script src="{{ asset('js/admin-event-review.js') }}?v={{ time() }}"></script>
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
