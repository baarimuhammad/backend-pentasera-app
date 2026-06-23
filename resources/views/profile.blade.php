@extends('layouts.app')
@section('title', 'Informasi Dasar | Pentasera')

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
                <a href="{{ url('/dashboard') }}">Pentasera</a>
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
                        <div class="avatar-preview" id="avatarPreview" style="cursor:pointer;" onclick="document.getElementById('avatarInput').click()">
                            <img id="avatarImage" src="" alt="Avatar" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;">
                            <i data-lucide="user" class="w-16 h-16 text-rust/20" id="avatarIcon"></i>
                        </div>
                        <input type="file" id="avatarInput" accept="image/jpg,image/jpeg,image/png,image/webp" style="display:none;">
                        <p class="text-xs text-gray-400 max-w-[180px] mx-auto leading-relaxed">Pastikan kamu mengunggah foto kamu atau logo organizer kamu</p>
                        <div class="mt-4">
                            <button onclick="document.getElementById('avatarInput').click()" class="text-rust font-bold text-xs hover:underline">Ganti Foto</button>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Nama Lengkap <span class="text-rust">*</span></label>
                            <input type="text" class="form-input" id="inputNama" placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <div class="relative">
                                <input type="email" class="form-input bg-gray-50" id="inputEmail" readonly>
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-green-500 flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    Terverifikasi
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nomor Ponsel</label>
                            <input type="tel" class="form-input" id="inputNoHp" placeholder="Contoh: 08123456789">
                        </div>

                        <!-- Creator-only fields -->
                        <div class="form-group full creator-only" id="organizerSection">
                            <hr class="my-6 border-gray-100">
                            <h3 class="font-bold text-lg text-ink mb-6">Informasi Organizer</h3>
                        </div>
                        <div class="form-group full creator-only">
                            <label>Nama Organizer</label>
                            <input type="text" class="form-input" id="inputOrganizerName" placeholder="Masukkan nama organizer">
                        </div>
                        <div class="form-group full creator-only">
                            <label>Alamat</label>
                            <textarea class="form-input form-textarea" id="inputAddress" placeholder="Masukkan alamat lengkap"></textarea>
                            <p class="text-right text-[10px] text-gray-400 mt-1">Sisa karakter 150</p>
                        </div>
                        <div class="form-group full creator-only">
                            <label>Tentang Kami</label>
                            <textarea class="form-input form-textarea" id="inputDeskripsi" placeholder="Ceritakan sedikit tentang organizer Anda"></textarea>
                            <p class="text-right text-[10px] text-gray-400 mt-1">Sisa karakter 250</p>
                        </div>
                        <div class="form-group creator-only">
                            <label>Telepon Organizer</label>
                            <input type="tel" class="form-input" id="inputContactPhone" placeholder="Contoh: 08123456789">
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-50 flex justify-end gap-4">
                    <button class="px-8 py-3 rounded-xl font-bold text-gray-400 hover:text-ink transition-colors">Batal</button>
                    <button onclick="saveProfile()" id="btnSaveProfile" class="bg-rust text-white px-10 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
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
