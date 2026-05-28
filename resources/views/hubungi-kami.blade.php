@extends('layouts.app')
@section('title', 'Hubungi Kami | Pentasera')

@section('custom-nav')
<!-- Navbar (matching original style-support.css .navbar) -->
<nav class="navbar">
    <div class="logo">Pentasera</div>
    <ul class="nav-links">
        <li><a href="{{ url('/') }}">Eksplorasi</a></li>
        <li><a href="{{ url('/events') }}">Kalender</a></li>
        <li><a href="{{ url('/events') }}">Arsip Seni</a></li>
        <li><a href="{{ url('/tentang-kami') }}">Tentang Kami</a></li>
    </ul>
    <div class="nav-btns">
        <!-- Logged Out View -->
        <div class="logged-out-only"></div>

        <!-- Logged In View -->
        <div class="logged-in-only" style="display: flex; align-items: center; gap: 15px;">
            <!-- Profile Dropdown -->
            <div class="profile-container">
                <div class="profile-img-wrap">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop" alt="User Profile">
                </div>
                <div class="dropdown-menu">
                    <div class="dropdown-header" onclick="toggleRole()">
                        <div class="switch-icon">
                            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                        </div>
                        <div class="dropdown-header-text">
                            <div class="dropdown-header-title" style="font-family: 'Inter', sans-serif;">Beralih ke akun</div>
                            <div class="dropdown-header-role" id="dropdown-role-label" style="font-family: 'Inter', sans-serif;">Penyelenggara</div>
                        </div>
                    </div>
                    <div class="dropdown-list" style="font-family: 'Inter', sans-serif;">
                        <a href="{{ url('/dashboard') }}" class="dropdown-item creator-only">
                            <i data-lucide="layout-dashboard"></i> Dashboard
                        </a>
                        <a href="{{ url('/my-events') }}" class="dropdown-item creator-only">
                            <i data-lucide="calendar"></i> Event Saya
                        </a>
                        <a href="{{ url('/profile') }}" class="dropdown-item">
                            <i data-lucide="user"></i> Informasi Dasar
                        </a>
                        <a href="{{ url('/settings') }}" class="dropdown-item">
                            <i data-lucide="settings"></i> Pengaturan
                        </a>
                        <a href="#" onclick="logout(); return false;" class="dropdown-item logout">
                            <i data-lucide="log-out"></i> Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
@endsection

@section('content')
<!-- Header Section -->
<header class="hero-section">
    <h1 style="font-style: italic; font-weight: 400; font-size: 52px;">Hubungi Kami</h1>
    <p class="hero-desc">Mari berkolaborasi menjaga warisan budaya Nusantara. Tim kami di Jawa Timur siap membantu pertanyaan Anda mengenai Pentasera.</p>
</header>

<!-- Main Contact Section -->
<div class="contact-grid">

    <!-- Kolom Kiri: Info Kontak -->
    <aside class="contact-info">
        <div class="info-card">
            <div class="info-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <div class="icon-wrap" style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #fdf2e9; color: var(--primary);">
                    <i data-lucide="map-pin"></i>
                </div>
                <h4 style="font-family: 'Playfair Display'; font-weight: 700; font-size: 20px;">Kantor Pusat</h4>
            </div>
            <p style="font-size: 14px; color: #4A3000;">Jl. Tunjungan No. 12, Genteng,<br>Surabaya, Jawa Timur 60275<br>Indonesia</p>
        </div>

        <div class="info-card">
            <div class="info-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <div class="icon-wrap" style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #fdf2e9; color: var(--primary);">
                    <i data-lucide="mail"></i>
                </div>
                <h4 style="font-family: 'Playfair Display'; font-weight: 700; font-size: 20px;">Surat Elektronik</h4>
            </div>
            <p style="font-size: 14px; color: #4A3000;">halo@pentasera.id<br>info@pentasera.id</p>
        </div>

        <div class="info-card">
            <div class="info-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <div class="icon-wrap" style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #fdf2e9; color: var(--primary);">
                    <i data-lucide="phone"></i>
                </div>
                <h4 style="font-family: 'Playfair Display'; font-weight: 700; font-size: 20px;">Telepon</h4>
            </div>
            <p style="font-size: 14px; color: #4A3000;">(031) 555-0123<br>+62 812-3456-7890</p>
        </div>

        <div class="map-placeholder">
            <img src="https://images.unsplash.com/photo-1501705388883-4ed8a543392c?q=80&w=2070" alt="Map Location" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </aside>

    <!-- Kolom Kanan: Form -->
    <main class="contact-form-card">
        <h3 style="font-family: 'Playfair Display'; font-style: italic; font-size: 28px; margin-bottom: 30px; color: #1A0F0A;">Kirimkan Pesan Anda</h3>
        <form class="contact-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" placeholder="Tulis nama Anda" required>
                </div>
                <div class="form-group">
                    <label>Alamat Email</label>
                    <input type="email" placeholder="email@contoh.id" required>
                </div>
            </div>

            <div class="form-group">
                <label>Subjek</label>
                <select required>
                    <option value="" disabled selected>Pilih keperluan Anda</option>
                    <option value="kerjasama">Kerjasama/Kolaborasi</option>
                    <option value="pertunjukan">Info Pertunjukan</option>
                    <option value="tiketing">Masalah Tiket</option>
                </select>
            </div>

            <div class="form-group">
                <label>Pesan</label>
                <textarea rows="6" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..." required></textarea>
            </div>

            <button type="submit" class="btn-dark-round" style="width: auto; padding: 14px 45px; border-radius: 12px; display: flex; align-items: center; gap: 10px;">
                Kirim Pesan <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </main>
</div>

<!-- Bottom Discussion Section -->
<section class="discussion-section">
    <div class="discussion-content">
        <h2>Mari Berdiskusi Secara Langsung</h2>
        <p>Kami selalu terbuka untuk berdiskusi mengenai pelestarian budaya dan inovasi pertunjukan seni Nusantara.</p>
    </div>
    <div class="discussion-image">
        <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=2070" alt="Discussion">
    </div>
</section>
@endsection

@section('custom-footer')
<!-- Footer (matching original style-support.css .footer) -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-title">Pentasera</div>
            <p class="footer-copy">© 2024 Pentasera. Menjaga Tradisi dalam Modernitas.</p>
            <div class="footer-social-wrap">
                <p class="social-label">Hubungi Kurator:</p>
                <div class="social-icons">
                    <a href="https://instagram.com" target="_blank" class="social-icon" title="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    </a>
                    <a href="https://wa.me" target="_blank" class="social-icon" title="WhatsApp Chat">
                        <i data-lucide="message-circle"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-links">
            <a href="{{ url('/pusat-bantuan') }}">Bantuan</a>
            <a href="{{ url('/kebijakan-privasi') }}">Kebijakan Privasi</a>
            <a href="{{ url('/syarat-ketentuan') }}">Syarat & Ketentuan</a>
            <a href="{{ url('/hubungi-kami') }}">Hubungi Kami</a>
        </div>
    </div>
</footer>
@endsection
