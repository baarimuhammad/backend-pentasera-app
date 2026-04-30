@extends('layouts.app')
@section('title', 'Informasi Dasar | Pentasara')

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
                <a href="{{ url('/dashboard') }}" class="nav-item creator-only">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item creator-only">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                </a>
                <a href="{{ url('/manage-access') }}" class="nav-item creator-only">
                    <i data-lucide="users" class="w-5 h-5"></i> Kelola Akses
                </a>
                <a href="{{ url('/my-tickets') }}" class="nav-item user-only">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tiket Saya
                </a>
            </div>
            <div class="nav-group">
                <p class="nav-label">Account</p>
                <a href="{{ url('/profile') }}" class="nav-item active">
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
                <span>Informasi Dasar</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all creator-only">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="font-display text-3xl text-ink mb-2">Profil Kamu</h1>
            <p class="text-gray-500 text-sm">Lengkapi informasi penyelenggara untuk membangun kepercayaan pembeli</p>
        </div>

        <section class="profile-section">
            <div class="banner-upload">
                <i data-lucide="image-plus" class="w-10 h-10 text-gray-300 mb-4"></i>
                <p class="font-bold text-gray-400">Unggah gambar/poster/banner</p>
                <p class="text-[10px] text-gray-300 mt-2">Direkomendasikan 1200 x 200px dan tidak lebih dari 2Mb</p>
            </div>

            <div class="profile-content-padding">
                <h3 class="font-bold text-lg text-ink mb-8">Informasi Personal</h3>

                <div class="profile-grid">
                    <div class="avatar-upload">
                        <div class="avatar-preview">
                            <i data-lucide="user" class="w-16 h-16 text-rust/20"></i>
                        </div>
                        <p class="text-xs text-gray-400 max-w-[180px] mx-auto leading-relaxed">Pastikan kamu mengunggah foto kamu atau logo organizer kamu</p>
                        <div class="mt-4">
                            <button class="text-rust font-bold text-xs hover:underline">Ganti Foto</button>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Nama Organizer <span class="text-rust">*</span></label>
                            <input type="text" class="form-input" value="rizaldi rizki" placeholder="Masukkan nama organizer">
                        </div>
                        <div class="form-group">
                            <label>Email Penyelenggara</label>
                            <div class="relative">
                                <input type="email" class="form-input bg-gray-50" value="rizkirizaldi199@gmail.com" readonly>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-green-500 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    Terverifikasi
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nomor Ponsel</label>
                            <div class="flex gap-2">
                                <input type="tel" class="form-input" placeholder="Contoh: 08123456789">
                                <button class="bg-white border border-gray-200 px-4 rounded-xl text-xs font-bold text-ink hover:bg-gray-50 transition-colors">Tambah</button>
                            </div>
                        </div>
                        <div class="form-group full">
                            <label>Alamat</label>
                            <textarea class="form-input form-textarea" placeholder="Masukkan alamat lengkap"></textarea>
                            <p class="text-right text-[10px] text-gray-400 mt-1">Sisa karakter 150</p>
                        </div>
                        <div class="form-group full">
                            <label>Tentang Kami</label>
                            <textarea class="form-input form-textarea" placeholder="Ceritakan sedikit tentang organizer Anda"></textarea>
                            <p class="text-right text-[10px] text-gray-400 mt-1">Sisa karakter 250</p>
                        </div>
                        <div class="form-group">
                            <label>Username Instagram</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">@</span>
                                <input type="text" class="form-input pl-8" placeholder="username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username Twitter</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">@</span>
                                <input type="text" class="form-input pl-8" placeholder="username">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-50 flex justify-end gap-4">
                    <button class="px-8 py-3 rounded-xl font-bold text-gray-400 hover:text-ink transition-colors">Batal</button>
                    <button onclick="saveProfile()" class="bg-rust text-white px-10 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </section>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/profile.js') }}"></script>
@endpush
