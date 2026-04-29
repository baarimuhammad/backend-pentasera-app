@extends('layouts.app')
@section('title', 'Pusat Bantuan | Pentasara')

@section('custom-nav')
<!-- Navbar dengan Search Bar (matching original style-support.css .navbar) -->
<nav class="navbar">
    <div class="logo">Pentasara</div>
    <ul class="nav-links">
        <li><a href="{{ url('/') }}">Eksplorasi</a></li>
        <li><a href="{{ url('/events') }}">Kalender</a></li>
        <li><a href="{{ url('/events') }}">Arsip Seni</a></li>
        <li><a href="{{ url('/tentang-kami') }}">Tentang Kami</a></li>
    </ul>
    <div class="nav-right-search">
        <div class="search-box-small">
            <span>🔍</span>
            <input type="text" placeholder="Cari tradisi...">
        </div>
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
    </div>
</nav>
@endsection

@section('content')
<!-- Header Bantuan -->
<header class="help-header">
    <h1>Pusat Bantuan <span>Pentasera</span></h1>
    <p>Temukan jawaban atas pertanyaan Anda mengenai pelestarian seni dan manajemen acara di platform Pentasara.</p>
</header>

<!-- Main Layout: Sidebar + FAQ -->
<div class="help-container">

    <!-- Sidebar Kategori -->
    <aside class="help-sidebar">
        <button class="cat-btn active" onclick="switchHelpCategory('tiket', this)">
            <div class="icon-wrap"><i data-lucide="ticket"></i></div>
            <span>Pembelian Tiket</span>
        </button>
        <button class="cat-btn" onclick="switchHelpCategory('event', this)">
            <div class="icon-wrap"><i data-lucide="calendar"></i></div>
            <span>Membuat Event</span>
        </button>
        <button class="cat-btn" onclick="switchHelpCategory('bayar', this)">
            <div class="icon-wrap"><i data-lucide="credit-card"></i></div>
            <span>Pembayaran</span>
        </button>
        <button class="cat-btn" onclick="switchHelpCategory('akun', this)">
            <div class="icon-wrap"><i data-lucide="user"></i></div>
            <span>Akun & Profil</span>
        </button>
    </aside>

    <!-- FAQ Section -->
    <main class="help-faq-list">

        <div id="cat-tiket" class="help-cat-section">
            <!-- Item 1 -->
            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara membeli tiket pertunjukan? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Membeli tiket di Pentasera sangat mudah. Ikuti langkah-langkah berikut:</p>
                    <div class="steps-list">
                        <p><strong>01.</strong> Pilih event yang ingin Anda kunjungi dari halaman Eksplorasi.</p>
                        <p><strong>02.</strong> Klik tombol "Pesan Tiket" pada halaman detail acara.</p>
                        <p><strong>03.</strong> Pilih kategori kursi atau jenis tiket yang tersedia.</p>
                        <p><strong>04.</strong> Selesaikan pembayaran melalui metode yang Anda pilih.</p>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="faq-item">
                <div class="faq-question">
                    Apakah saya bisa membatalkan tiket yang sudah dibeli? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Tiket yang sudah dibeli bersifat final, namun pengembalian dana dapat diproses jika terjadi pembatalan sepihak oleh penyelenggara.</p>
                </div>
            </div>
        </div>

        <div id="cat-event" class="help-cat-section hidden">
            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara mendaftarkan event budaya saya? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Anda dapat mendaftar sebagai kurator melalui menu "Membuat Event" dan mengisi formulir profil komunitas seni Anda.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    Apa saja persyaratan menjadi penyelenggara? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Anda perlu memiliki komunitas seni atau legalitas penyelenggara acara yang sah untuk memastikan kualitas pertunjukan di platform kami.</p>
                </div>
            </div>
        </div>

        <div id="cat-bayar" class="help-cat-section hidden">
            <div class="faq-item">
                <div class="faq-question">
                    Metode pembayaran apa saja yang didukung? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Kami mendukung berbagai metode pembayaran mulai dari transfer bank, dompet digital (OVO, GoPay), hingga kartu kredit internasional.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    Apakah pembayaran saya aman? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Sangat aman. Kami menggunakan enkripsi SSL tingkat tinggi dan bekerja sama dengan payment gateway terpercaya untuk memproses setiap transaksi.</p>
                </div>
            </div>
        </div>

        <div id="cat-akun" class="help-cat-section hidden">
            <div class="faq-item">
                <div class="faq-question">
                    Lupa kata sandi akun saya? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Anda dapat menggunakan fitur "Lupa Password" di halaman masuk untuk mengatur ulang kata sandi melalui email yang terdaftar.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    Bagaimana cara mengubah profil saya? <span>⌄</span>
                </div>
                <div class="faq-answer">
                    <p>Setelah masuk, buka menu "Profil" di pojok kanan atas untuk mengubah foto, biografi, atau informasi kontak Anda.</p>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- CTA Bantuan -->
<section class="help-cta-section">
    <div class="help-cta-card">
        <div class="cta-info">
            <h2>Masih butuh bantuan?</h2>
            <p>Tim kurasi kami siap membantu Anda 24/7 untuk memastikan pengalaman budaya Anda berjalan sempurna.</p>
            <div class="cta-btns">
                <a href="{{ url('/hubungi-kami') }}" class="btn-dark-round">Hubungi Kami</a>
                <a href="#" class="btn-white-round">Obrolan Langsung</a>
            </div>
        </div>
        <div class="wayang-art">
            <img src="https://images.unsplash.com/photo-1578926375605-eaf7559b1458?q=80&w=1000" alt="Wayang">
        </div>
    </div>
</section>
@endsection

@section('custom-footer')
<!-- Footer with Bantuan, Kebijakan Privasi, Syarat & Ketentuan, Hubungi Kami -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-title">Pentasara</div>
            <p class="footer-copy">© 2024 Pentasara. Menjaga Tradisi dalam Modernitas.</p>
            <div class="footer-social-wrap">
                <p class="social-label">Hubungi Kurator:</p>
                <div class="social-icons">
                    <a href="https://instagram.com" target="_blank" class="social-icon" title="Instagram">
                        <i data-lucide="instagram"></i>
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

@push('scripts')
<script>
    // Category switching
    function switchHelpCategory(catId, btn) {
        // Hide all sections
        document.querySelectorAll('.help-cat-section').forEach(section => {
            section.classList.add('hidden');
        });
        // Show target section
        document.getElementById('cat-' + catId).classList.remove('hidden');

        // Update button active state
        document.querySelectorAll('.cat-btn').forEach(b => {
            b.classList.remove('active');
        });
        btn.classList.add('active');
    }

    // FAQ Accordion Toggle
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.faq-question').forEach(function(question) {
            question.addEventListener('click', function() {
                const faqItem = this.closest('.faq-item');
                const isActive = faqItem.classList.contains('active');
                
                // Close all FAQ items in the same section
                const section = faqItem.closest('.help-cat-section') || faqItem.closest('.content-section') || faqItem.parentElement;
                section.querySelectorAll('.faq-item').forEach(function(item) {
                    item.classList.remove('active');
                });

                // Toggle the clicked one (open if it was closed)
                if (!isActive) {
                    faqItem.classList.add('active');
                }
            });
        });
    });
</script>
@endpush
