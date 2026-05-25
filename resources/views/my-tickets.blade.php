@extends('layouts.app')
@section('title', 'Tiket Saya | Pentasara')

@section('custom-nav')
{{-- Dashboard pages use their own sidebar, no main nav --}}
@endsection

@section('custom-footer')
{{-- Dashboard pages have no main footer --}}
@endsection

@push('styles')
<style>
    .ticket-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(232, 194, 133, 0.1);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        margin-bottom: 24px;
    }
    @media (min-width: 768px) {
        .ticket-card {
            flex-direction: row;
        }
    }
    .ticket-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border-color: var(--rust);
    }
    .ticket-divider {
        border-left: 2px dashed #f0f0f0;
        position: relative;
    }
    .ticket-divider::before, .ticket-divider::after {
        content: '';
        position: absolute;
        left: -11px;
        width: 20px;
        height: 20px;
        background: #FDFCFB;
        border-radius: 50%;
    }
    .ticket-divider::before { top: -10px; }
    .ticket-divider::after { bottom: -10px; }

    .status-badge-ticket {
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-success { background: #E6F6EC; color: #059669; }
    .status-pending { background: #FFFBEB; color: #D97706; }
    .status-used { background: #F1F5F9; color: #64748B; }
    .status-phys { background: #EFF6FF; color: #3B82F6; }

    /* Modal Styles */
    .ticket-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(26, 15, 10, 0.8);
        backdrop-filter: blur(8px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .ticket-modal-content {
        background: white;
        border-radius: 32px;
        width: 100%;
        max-width: 450px;
        overflow: hidden;
        position: relative;
        animation: ticketModalIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes ticketModalIn {
        from { transform: scale(0.9) translateY(20px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }

    .search-container-tickets {
        position: relative;
        max-width: 400px;
        width: 100%;
    }
    .search-container-tickets .lucide {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        width: 18px;
        height: 18px;
    }
    .search-input-tickets {
        width: 100%;
        padding: 12px 16px 12px 48px;
        border-radius: 14px;
        border: 1px solid rgba(232, 194, 133, 0.2);
        background: white;
        font-size: 14px;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .search-input-tickets:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 4px rgba(184, 76, 43, 0.05);
    }

    /* Ticket tabs */
    .ticket-tabs {
        display: flex;
        gap: 32px;
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 32px;
    }
    .ticket-tab-btn {
        padding-bottom: 16px;
        border: none;
        background: none;
        border-bottom: 2px solid transparent;
        color: #9ca3af;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .ticket-tab-btn:hover {
        color: #6b7280;
    }
    .ticket-tab-btn.active {
        border-bottom-color: var(--rust);
        color: var(--rust);
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasara" class="w-10 h-10 object-contain">
            <span class="logo-text text-sm">PENTASARA</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Main Menu</p>
                <a href="{{ url('/dashboard') }}" class="nav-item creator-only">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item creator-only">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                </a>
                <a href="{{ url('/manage-access') }}" class="nav-item creator-only">
                    <i data-lucide="users" class="w-5 h-5"></i> Kelola Akses
                </a>
                <a href="{{ url('/my-tickets') }}" class="nav-item user-only active">
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
                <span>Beralih ke Penyelenggara</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">
        <header class="dashboard-header">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Pentasara</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span>Tiket Saya</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/events') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari Event Lain
                </a>
            </div>
        </header>

        <div class="mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h1 class="font-display text-3xl text-ink mb-2">Tiket Saya</h1>
                <p class="text-gray-500 text-sm">Review dan kelola akses pementasan budaya Anda</p>
            </div>
            <div class="search-container-tickets">
                <i data-lucide="search"></i>
                <input type="text" class="search-input-tickets" placeholder="Cari berdasarkan nama event..." id="ticketSearch">
            </div>
        </div>

        <!-- Tabs -->
        <div class="ticket-tabs" id="ticketTabs">
            <button class="ticket-tab-btn active" data-filter="aktif">Aktif</button>
            <button class="ticket-tab-btn" data-filter="selesai">Selesai</button>
            <button class="ticket-tab-btn" data-filter="batal">Batal</button>
        </div>

        <!-- Tickets Grid (dynamically rendered by JS) -->
        <div class="max-w-5xl" id="tickets-list">
            <!-- Tickets will be loaded dynamically from API -->
            <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                <div class="w-12 h-12 border-4 border-gray-200 border-t-rust rounded-full animate-spin mb-4"></div>
                <p class="text-sm">Memuat tiket...</p>
            </div>
        </div>
    </main>
</div>

<!-- Ticket View Modal (QR Code) -->
<div id="ticketModal" class="ticket-modal">
    <div class="ticket-modal-content">
        <div class="p-8 pb-12 flex flex-col items-center">
            <button onclick="closeTicket()" class="absolute top-6 right-6 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200 transition-all cursor-pointer">
                <i data-lucide="x" class="w-5 h-5 text-ink"></i>
            </button>

            <div class="w-16 h-1 bg-gray-100 rounded-full mb-8"></div>

            <h3 id="modalTitle" class="font-display text-2xl text-ink text-center mb-1">Event Name</h3>
            <p id="modalId" class="text-xs font-bold text-rust tracking-widest uppercase mb-8">#ID-00000</p>

            <div class="bg-gray-50 p-6 rounded-3xl border-2 border-dashed border-gray-200 mb-10 w-full flex flex-col items-center">
                <div id="modalQrCode" class="w-full min-h-[120px] bg-white p-6 rounded-2xl shadow-xl shadow-rust/5 flex items-center justify-center mb-6">
                    <!-- QR Code rendered here by JS -->
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-ink">Tunjukkan QR Code ini ke Petugas</p>
                    <p class="text-xs text-gray-400 mt-1 max-w-[200px]">Valid untuk akses masuk & pengambilan merchandise</p>
                </div>
            </div>

            <div class="w-full flex gap-3">
                <button class="flex-grow bg-ink text-white py-4 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-black transition-all cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4"></i> Download PDF
                </button>
                <button class="w-14 h-14 border border-gray-200 rounded-2xl flex items-center justify-center hover:bg-gray-50 transition-all cursor-pointer">
                    <i data-lucide="share-2" class="w-5 h-5 text-gray-400"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="{{ asset('js/my-tickets.js') }}"></script>
<script>
    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeTicket();
    });

    // Close on backdrop click
    document.getElementById('ticketModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('ticketModal')) closeTicket();
    });
</script>
@endpush
