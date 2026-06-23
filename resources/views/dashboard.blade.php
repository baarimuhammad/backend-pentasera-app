@extends('layouts.app')

@section('title', 'Dashboard | Pentasera')

@section('custom-nav')
{{-- Dashboard pages use their own sidebar, no main nav --}}
@endsection

@section('custom-footer')
{{-- Dashboard pages have no main footer --}}
@endsection

@section('content')
<div class="dashboard-container" id="dashboard-container">
    <!-- Mobile Top Bar (hamburger, visible only on < 1024px) -->
    <div class="dashboard-mobile-bar">
        <button class="sidebar-toggle-btn" id="sidebar-toggle-btn" aria-label="Open sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <span class="logo-text" style="font-size:14px; letter-spacing:2px; color:#4A3000;">PENTASERA</span>
    </div>

    <!-- Sidebar Overlay (mobile backdrop) -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <aside class="dashboard-sidebar" id="dashboard-sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera Logo" class="w-10 h-10 object-contain">
            <span class="logo-text text-sm">PENTASERA</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Main Menu</p>
                <a href="{{ url('/dashboard') }}" class="nav-item active creator-only">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item creator-only">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                </a>
                <a href="{{ url('/my-tickets') }}" class="nav-item user-only">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tiket Saya
                </a>
            </div>
            <div class="nav-group">
                <p class="nav-label">Akun</p>
                <a href="{{ url('/profile') }}" class="nav-item">
                    <i data-lucide="user-circle" class="w-5 h-5"></i> Informasi Dasar
                </a>
                <a href="{{ url('/settings') }}" class="nav-item">
                    <i data-lucide="settings" class="w-5 h-5"></i> Pengaturan
                </a>
                <a href="{{ url('/pusat-bantuan') }}" class="nav-item">
                    <i data-lucide="help-circle" class="w-5 h-5"></i> Pusat Bantuan
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <button onclick="toggleRoleAndRedirect()" class="switch-mode-btn cursor-pointer">
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                <span>Beralih ke Pembeli</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <div class="dashboard-header">
            <div>
                <div class="breadcrumb"><a href="{{ url('/') }}">Home</a> / <span>Dashboard</span></div>
                <h1 class="text-2xl font-bold text-ink mt-2">Dashboard Overview</h1>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="px-6 py-3 bg-rust text-white rounded-xl font-bold shadow-lg hover:bg-rust-deep transition-all text-sm">+ Buat Event Baru</a>
            </div>
        </div>

        <!-- Mission Card -->
        <div class="mission-card">
            <div class="mission-info">
                <h2 class="mission-title">Misi Kurator Kamu 🎭</h2>
                <p class="mission-desc">Selesaikan 3 langkah untuk mulai menjual tiket event pertamamu.</p>
                <div class="progress-container">
                    <div class="progress-bar"><div class="progress-fill" style="width:33%"></div></div>
                    <span class="progress-text">1/3</span>
                </div>
            </div>
            <div class="mission-steps">
                <div class="step-card">
                    <div class="step-icon"><i data-lucide="user-check" class="w-6 h-6"></i></div>
                    <p class="step-label">Lengkapi Profil</p>
                    <a href="{{ url('/profile') }}" class="step-btn primary">Lengkapi</a>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i data-lucide="calendar-plus" class="w-6 h-6"></i></div>
                    <p class="step-label">Buat Event Pertama</p>
                    <a href="{{ url('/create-event') }}" class="step-btn secondary">Buat Event</a>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label"><i data-lucide="calendar" class="w-4 h-4"></i> Total Event</span>
                    <a href="{{ url('/my-events') }}" class="stat-link">Lihat →</a>
                </div>
                <div class="stat-value" id="dash-stat-events">0</div>
                <div class="stat-unit" id="dash-stat-events-active">0 event aktif</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label"><i data-lucide="ticket" class="w-4 h-4"></i> Tiket Terjual</span>
                </div>
                <div class="stat-value" id="dash-stat-tickets">0</div>
                <div class="stat-unit">Tiket</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label"><i data-lucide="banknote" class="w-4 h-4"></i> Pendapatan</span>
                </div>
                <div class="stat-value" id="dash-stat-revenue">Rp 0</div>
                <div class="stat-unit">Total</div>
            </div>
        </div>

        <!-- Event List -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-ink">Event Saya</h2>
                <a href="{{ url('/my-events') }}" class="text-sm text-rust font-bold hover:underline">Lihat Semua →</a>
            </div>
            <div id="dashboard-events-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <p class="text-gray-400 text-sm col-span-full" id="dash-events-empty">Memuat data event...</p>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
<script>
(function() {
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const sidebar   = document.getElementById('dashboard-sidebar');
    const overlay   = document.getElementById('sidebar-overlay');

    function openSidebar() {
        if (sidebar)  sidebar.classList.add('active');
        if (overlay)  overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        if (sidebar)  sidebar.classList.remove('active');
        if (overlay)  overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (overlay)   overlay.addEventListener('click', closeSidebar);

    // Close sidebar when a nav link is clicked (mobile UX)
    if (sidebar) {
        sidebar.querySelectorAll('a.nav-item').forEach(function(link) {
            link.addEventListener('click', closeSidebar);
        });
    }
})();
</script>
@endpush
