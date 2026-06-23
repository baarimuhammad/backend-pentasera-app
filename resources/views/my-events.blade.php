@extends('layouts.app')
@section('title', 'Event Saya | Pentasera')

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
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera" class="w-10 h-10 object-contain">
            <span class="logo-text text-sm">PENTASERA</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Main Menu</p>
                <a href="{{ url('/dashboard') }}" class="nav-item creator-only">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item active creator-only">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                    <span id="sidebar-event-count" class="ml-auto bg-white/20 text-white text-[10px] px-2 py-0.5 rounded-full">0</span>
                </a>
                <a href="{{ url('/my-tickets') }}" class="nav-item user-only">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tiket Saya
                </a>
            </div>
            <div class="nav-group">
                <p class="nav-label">Account</p>
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
        <header class="dashboard-header">
            <div class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Pentasera</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span>Event Saya</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="font-display text-3xl text-ink mb-2">Event</h1>
            <p class="text-gray-500 text-sm">Manajemen daftar pementasan budaya Anda</p>
        </div>

        <div class="event-tabs">
            <div class="event-tab active" onclick="switchMyEventTab('aktif')">Event Aktif <span id="count-aktif" class="ml-1 text-xs opacity-70"></span></div>
            <div class="event-tab" onclick="switchMyEventTab('pending')">Menunggu Approval <span id="count-pending" class="ml-1 text-xs opacity-70"></span></div>
            <div class="event-tab" onclick="switchMyEventTab('draft')">Event Draft <span id="count-draft" class="ml-1 text-xs opacity-70"></span></div>
            <div class="event-tab" onclick="switchMyEventTab('lalu')">Event Lalu <span id="count-lalu" class="ml-1 text-xs opacity-70"></span></div>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div class="search-input-wrapper">
                <i data-lucide="search"></i>
                <input type="text" id="search-events" class="search-input" placeholder="Cari event..." oninput="filterEvents()">
            </div>
            <button class="w-10 h-10 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-rust transition-colors">
                <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Tab Content: Aktif -->
        <div id="tab-aktif" class="event-tab-content active">
            <div class="event-dashboard-grid" id="grid-aktif">
                <div class="text-center py-10 text-gray-400 col-span-full loading-placeholder">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gray-200 rounded-full"></div>
                        <p class="font-bold">Memuat event aktif...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Menunggu Approval -->
        <div id="tab-pending" class="event-tab-content">
            <div class="event-dashboard-grid" id="grid-pending">
                <div class="text-center py-10 text-gray-400 col-span-full loading-placeholder">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gray-200 rounded-full"></div>
                        <p class="font-bold">Memuat event pending...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Draft -->
        <div id="tab-draft" class="event-tab-content">
            <div class="event-dashboard-grid" id="grid-draft">
                <div class="text-center py-10 text-gray-400 col-span-full loading-placeholder">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gray-200 rounded-full"></div>
                        <p class="font-bold">Memuat event draft...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Lalu -->
        <div id="tab-lalu" class="event-tab-content">
            <div class="event-dashboard-grid" id="grid-lalu">
                <div class="text-center py-10 text-gray-400 col-span-full loading-placeholder">
                    <div class="animate-pulse">
                        <div class="w-12 h-12 mx-auto mb-3 bg-gray-200 rounded-full"></div>
                        <p class="font-bold">Memuat event lalu...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/my-events.js') }}"></script>
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

    if (sidebar) {
        sidebar.querySelectorAll('a.nav-item').forEach(function(link) {
            link.addEventListener('click', closeSidebar);
        });
    }
})();
</script>
@endpush
