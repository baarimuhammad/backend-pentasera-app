@extends('layouts.app')
@section('title', 'Syarat & Ketentuan | Pentasera')

@section('custom-nav')
<!-- Navbar Minimalis untuk halaman legal -->  
<nav class="navbar-legal">
    <a href="{{ url('/') }}" class="back-link">
        <span class="arrow">&larr;</span> Pentasera
    </a>
    <div class="doc-type">DOKUMEN HUKUM</div>
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
<!-- Header -->
<header class="legal-header">
    <div class="update-badge">PEMBARUAN TERAKHIR: 24 MEI 2024</div>
    <h1 class="italic-title">Syarat & Ketentuan</h1>
    <p class="intro-text">Terima kasih telah memilih Pentasera. Harap baca dokumen ini dengan saksama untuk memahami hak dan kewajiban Anda saat menggunakan platform kami.</p>
</header>

<!-- Top Info Cards -->
<section class="terms-top-grid">
    <div class="terms-card-beige">
        <span class="card-icon"><i data-lucide="scale"></i></span>
        <h4>Landasan Hukum</h4>
        <p>Ketentuan ini merupakan perjanjian yang mengikat antara Anda sebagai pengguna dengan Pentasera mengenai akses dan penggunaan layanan kami.</p>
    </div>
    <div class="terms-card-brown">
        <span class="card-icon"><i data-lucide="shield-check"></i></span>
        <h4>Privasi Anda</h4>
        <p>Kami menjaga data budaya dan pribadi Anda dengan standar enkripsi tertinggi untuk melindungi identitas Anda.</p>
    </div>
</section>

<!-- Isi Dokumen -->
<main class="terms-content">

    <!-- Pasal 1 -->
    <section class="terms-section">
        <h2><i data-lucide="info"></i> 1. Definisi Layanan</h2>
        <div class="section-body">
            <p>"Pentasera" adalah platform digital yang dikelola oleh PT Warisan Budaya Digital, yang menyediakan layanan kurasi acara, penjualan tiket, dan arsip seni tradisional Indonesia.</p>
            <p>"Pengguna" merujuk pada individu atau entitas yang mendaftar, mengakses, atau menggunakan bagian mana pun dari layanan kami.</p>
        </div>
    </section>

    <!-- Pasal 2 -->
    <section class="terms-section">
        <h2><i data-lucide="users"></i> 2. Akun & Keanggotaan</h2>
        <div class="section-body">
            <p>Untuk mengakses fitur tertentu, Anda diwajibkan membuat akun. Anda bertanggung jawab penuh atas kerahasiaan informasi login Anda dan segala aktivitas yang terjadi di bawah akun Anda.</p>
            <ul class="check-list">
                <li>Informasi yang diberikan harus akurat dan mutakhir.</li>
                <li>Satu identitas hanya diperbolehkan memiliki satu akun aktif.</li>
            </ul>
        </div>
    </section>

    <!-- Komitmen Box -->
    <div class="commitment-box">
        <i data-lucide="quote" class="w-12 h-12 mx-auto mb-4 opacity-20"></i>
        <p>"Kami berkomitmen untuk menjaga integritas seni tradisional. Setiap pelanggaran hak cipta atas konten arsip seni akan ditindak lanjuti sesuai hukum yang berlaku di Republik Indonesia."</p>
    </div>

    <!-- Pasal 3 -->
    <section class="terms-section">
        <h2><i data-lucide="shopping-cart"></i> 3. Pembelian & Pembatalan</h2>
        <div class="section-body">
            <p>Seluruh transaksi pembelian tiket yang telah berhasil dikonfirmasi bersifat final. Pengembalian dana (refund) hanya dapat diproses apabila:</p>
            <div class="sub-card-grid">
                <div class="sub-card" style="border-left: 3px solid var(--primary);">
                    <h5 style="font-family: 'Playfair Display'; font-style: italic; margin-bottom: 5px;">Pembatalan Penyelenggara</h5>
                    <p>Jika acara dibatalkan sepenuhnya oleh pihak kurator atau penyelenggara tanpa jadwal pengganti.</p>
                </div>
                <div class="sub-card" style="border-left: 3px solid var(--primary);">
                    <h5 style="font-family: 'Playfair Display'; font-style: italic; margin-bottom: 5px;">Keadaan Kahar</h5>
                    <p>Bencana alam atau kebijakan pemerintah yang melarang pelaksanaan kegiatan kerumunan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pasal 4 -->
    <section class="terms-section">
        <h2><i data-lucide="copyright"></i> 4. Hak Kekayaan Intelektual</h2>
        <div class="section-body">
            <p>Seluruh konten dalam Pentasera, termasuk namun tidak terbatas pada teks, grafik, logo, foto pertunjukan, dan rekaman audio-visual, adalah milik Pentasera atau pemberi lisensinya.</p>
            <p>Pengguna dilarang keras mengunduh, mereproduksi, atau mendistribusikan konten arsip seni tanpa izin tertulis yang sah.</p>
        </div>
    </section>

    <hr class="separator">

    <!-- Pertanyaan -->
    <section class="terms-footer-action">
        <div class="footer-text">
            <h3>Masih memiliki pertanyaan?</h3>
            <p>Tim kurasi dan hukum kami siap membantu Anda.</p>
        </div>
        <div class="footer-btns">
            <a href="{{ url('/pusat-bantuan') }}" class="btn-gray-round">Pusat Bantuan</a>
            <a href="{{ url('/hubungi-kami') }}" class="btn-dark-round">Kontak Kami</a>
        </div>
    </section>

</main>
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
