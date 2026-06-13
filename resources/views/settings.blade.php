@extends('layouts.app')
@section('title', 'Pengaturan | Pentasera')

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
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera Logo" class="w-10 h-10 object-contain">
            <span class="logo-text text-sm">PENTASERA</span>
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

                <a href="{{ url('/my-tickets') }}" class="nav-item user-only">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tiket Saya
                </a>
            </div>
            <div class="nav-group">
                <p class="nav-label">Account</p>
                <a href="{{ url('/profile') }}" class="nav-item">
                    <i data-lucide="user-circle" class="w-5 h-5"></i> Informasi Dasar
                </a>
                <a href="{{ url('/settings') }}" class="nav-item active">
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
                <span>Pengaturan</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all creator-only">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="font-display text-3xl text-ink mb-2">Pengaturan</h1>
            <p class="text-gray-500 text-sm">Kelola keamanan akun dan preferensi Anda</p>
        </div>

        <div class="settings-grid">
            <!-- Security Settings -->
            <div class="settings-card">
                <h3 class="settings-card-title">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                    Keamanan Akun
                </h3>
                <div class="settings-item">
                    <div class="settings-info">
                        <h5>Ubah Kata Sandi</h5>
                        <p>Terakhir diubah 3 bulan yang lalu</p>
                    </div>
                    <button onclick="location.href='{{ url('/reset-password') }}'" class="text-rust font-bold text-xs hover:underline">Ubah</button>
                </div>
                <div class="settings-item">
                    <div class="settings-info">
                        <h5>Autentikasi Dua Faktor (2FA)</h5>
                        <p>Tambahkan lapisan keamanan ekstra</p>
                    </div>
                    <div class="relative inline-block w-10 h-5 transition duration-200 ease-in bg-gray-200 rounded-full cursor-pointer">
                        <div class="absolute left-0 w-5 h-5 transition duration-100 ease-in transform bg-white border-2 border-gray-200 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="settings-card">
                <h3 class="settings-card-title">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    Notifikasi
                </h3>
                <div class="settings-item">
                    <div class="settings-info">
                        <h5>Email Marketing</h5>
                        <p>Terima info event terbaru via email</p>
                    </div>
                    <div class="relative inline-block w-10 h-5 transition duration-200 ease-in bg-rust rounded-full cursor-pointer">
                        <div class="absolute right-0 w-5 h-5 transition duration-100 ease-in transform bg-white border-2 border-rust rounded-full"></div>
                    </div>
                </div>
                <div class="settings-item">
                    <div class="settings-info">
                        <h5>Notifikasi Penjualan</h5>
                        <p>Dapatkan update setiap ada tiket terjual</p>
                    </div>
                    <div class="relative inline-block w-10 h-5 transition duration-200 ease-in bg-rust rounded-full cursor-pointer">
                        <div class="absolute right-0 w-5 h-5 transition duration-100 ease-in transform bg-white border-2 border-rust rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Support Settings -->
            <div class="settings-card">
                <h3 class="settings-card-title">
                    <i data-lucide="headphones" class="w-5 h-5"></i>
                    Bantuan & Dukungan
                </h3>
                <div class="settings-item">
                    <div class="settings-info">
                        <h5>Customer Service</h5>
                        <p>Hubungi tim kami untuk bantuan teknis</p>
                    </div>
                    <button class="bg-rust/10 text-rust px-6 py-2.5 rounded-xl font-bold text-xs">Chat Sekarang</button>
                </div>
                <div class="settings-item">
                    <div class="settings-info">
                        <h5>Panduan Pengguna</h5>
                        <p>Pelajari cara mengelola event dengan efektif</p>
                    </div>
                    <button class="text-gray-400"><i data-lucide="external-link" class="w-4 h-4"></i></button>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="settings-card danger-zone">
                <div class="settings-item" style="border: none;">
                    <div class="settings-info">
                        <h5 class="text-rust-deep">Tutup Akun</h5>
                        <p>Menghapus akun dan semua data event Anda secara permanen</p>
                    </div>
                    <button onclick="openModal('close-account-modal')" class="danger-btn">Tutup Akun</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Close Account Modal -->
<div id="close-account-modal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="font-display text-xl text-rust-deep">Tutup Akun Permanen</h3>
            <button onclick="closeModal('close-account-modal')" class="text-gray-400 hover:text-ink transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 flex gap-4 mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex-shrink-0 flex items-center justify-center text-red-600">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-red-900 mb-1">Tindakan ini tidak dapat dibatalkan</h4>
                    <p class="text-xs text-red-700 leading-relaxed">Seluruh data event, informasi profil, dan riwayat transaksi Anda akan dihapus secara permanen dari server Pentasera.</p>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ketik "KONFIRMASI" untuk melanjutkan</label>
                <input type="text" id="confirm-delete-input" placeholder="KONFIRMASI" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-rust transition-all outline-none uppercase">
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('close-account-modal')" class="px-6 py-2.5 rounded-xl font-bold text-xs text-gray-500 hover:bg-gray-100 transition-all">Batal</button>
            <button onclick="handleCloseAccount()" class="bg-red-600 text-white px-8 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-red-600/20 hover:bg-red-700 transition-all">Tutup Akun Sekarang</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/settings.js') }}"></script>
@endpush
