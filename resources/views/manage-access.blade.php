@extends('layouts.app')
@section('title', 'Kelola Akses | Pentasara')

@section('custom-nav')
{{-- Dashboard pages use their own sidebar, no main nav --}}
@endsection

@section('custom-footer')
{{-- Dashboard pages have no main footer --}}
@endsection

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
                <a href="{{ url('/dashboard') }}" class="nav-item">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                </a>
                <a href="{{ url('/manage-access') }}" class="nav-item active">
                    <i data-lucide="users" class="w-5 h-5"></i> Kelola Akses
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
                <a href="{{ url('/dashboard') }}">Pentasara</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span>Kelola Akses</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="font-display text-3xl text-ink mb-2">Kelola Akses</h1>
            <p class="text-gray-500 text-sm">Atur siapa saja yang dapat mengelola event Anda</p>
        </div>

        <!-- Users Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3 class="table-title">Pengguna</h3>
                <div class="table-actions">
                    <div class="search-input-wrapper">
                        <i data-lucide="search"></i>
                        <input type="text" class="search-input" placeholder="Cari pengguna...">
                    </div>
                    <button class="bg-rust text-white px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-2">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        Undang
                    </button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">RR</div>
                                <span class="font-bold">rizaldi rizki</span>
                            </div>
                        </td>
                        <td>rizkirizaldi199@gmail.com</td>
                        <td class="font-bold">Tarian Kecak Uluwatu</td>
                        <td><span class="status-badge active">AKTIF</span></td>
                        <td>
                            <button class="text-gray-400 hover:text-ink"><i data-lucide="more-vertical" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar bg-gray-100 text-gray-400">JD</div>
                                <span class="font-bold">John Doe</span>
                            </div>
                        </td>
                        <td>john.doe@example.com</td>
                        <td class="font-bold text-gray-400">Gamelan Jawa Heritage</td>
                        <td><span class="status-badge bg-gray-100 text-gray-400">DRAFT</span></td>
                        <td>
                            <button class="text-gray-400 hover:text-ink"><i data-lucide="more-vertical" class="w-4 h-4"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Event List Section -->
        <div class="data-table-container">
            <div class="table-header">
                <h3 class="table-title">Daftar Event Terkait</h3>
                <div class="table-actions">
                    <div class="search-input-wrapper">
                        <i data-lucide="search"></i>
                        <input type="text" class="search-input" placeholder="Cari event...">
                    </div>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold">Tarian Kecak Uluwatu</td>
                        <td>Seni Tari</td>
                        <td><span class="status-badge active">AKTIF</span></td>
                        <td><a href="{{ url('/manage-event/1') }}" class="text-rust font-bold text-xs">Kelola Akses</a></td>
                    </tr>
                    <tr>
                        <td class="font-bold">Gamelan Jawa Heritage</td>
                        <td>Musik Tradisional</td>
                        <td><span class="status-badge bg-gray-100 text-gray-400">DRAFT</span></td>
                        <td><a href="{{ url('/manage-event/2') }}" class="text-rust font-bold text-xs">Kelola Akses</a></td>
                    </tr>
                    <tr>
                        <td class="font-bold">Wayang Kulit Purwa</td>
                        <td>Pertunjukan</td>
                        <td><span class="status-badge bg-ink text-white">SELESAI</span></td>
                        <td><a href="{{ url('/event-report/3') }}" class="text-rust font-bold text-xs">Lihat Laporan</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
function toggleRoleAndRedirect() {
    const currentRole = localStorage.getItem('pentasara_role') || 'creator';
    const newRole = currentRole === 'creator' ? 'user' : 'creator';
    localStorage.setItem('pentasara_role', newRole);
    window.location.href = '/';
}
</script>
@endpush
