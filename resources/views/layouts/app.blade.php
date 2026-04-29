<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pentasara | Tradisi dalam Genggaman')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN (same as original) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Cinzel', 'serif'],
                    },
                    colors: {
                        gold: '#C8922A',
                        'gold-light': '#E5B96A',
                        rust: '#B84C2B',
                        'rust-deep': '#8B2E12',
                        cream: '#F5EDE0',
                        dark: '#1A0F0A',
                        ink: '#2C1A0E',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Custom CSS (same files as original) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style-support.css') }}">

    @stack('styles')
</head>
<body class="bg-cream/20 text-ink font-sans antialiased min-h-screen flex flex-col">

    @if(empty($hideChrome) && !View::hasSection('custom-nav'))
    <!-- Announcement Bar -->
    <div class="announcement">
        Buat event cuma 3 langkah. <a href="{{ url('/create-event') }}">Buat Event sekarang</a>
    </div>

    <!-- Navbar -->
    <nav class="main-nav sticky top-0 z-50">
        <a href="{{ url('/') }}" class="logo flex items-center gap-2">
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasara Logo" class="logo-img">
            <span class="logo-text">PENTASARA</span>
        </a>
        <div class="search-bar">
            <input type="text" placeholder="Cari nama event....">
            <button class="search-btn">
                <i data-lucide="search" class="w-4 h-4"></i>
            </button>
        </div>
        <ul class="nav-links" style="display:flex">
            <li><a href="{{ url('/tentang-kami') }}">About Us</a></li>
            <li><a href="{{ url('/events') }}">Events</a></li>
            <li><a href="{{ url('/hubungi-kami') }}">Contact</a></li>
        </ul>
        <div class="nav-btns">
            <!-- Logged Out View -->
            <div class="logged-out-only flex gap-3">
                <button class="btn-daftar" onclick="location.href='{{ url('/auth?tab=signup') }}'">Daftar</button>
                <button class="btn-masuk" onclick="location.href='{{ url('/auth') }}'">Masuk</button>
            </div>

            <!-- Logged In View -->
            <div class="logged-in-only flex items-center gap-4">
                <button class="btn-ticket user-only" onclick="location.href='{{ url('/my-tickets') }}'">
                    <i data-lucide="ticket" class="w-4 h-4"></i> Tiket Saya
                </button>
                <button class="btn-ticket creator-only" onclick="location.href='{{ url('/create-event') }}'">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Buat Event
                </button>

                <!-- Profile Dropdown -->
                <div class="profile-container">
                    <div class="profile-img-wrap">
                        <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&h=100&fit=crop" alt="User Profile">
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-header" onclick="toggleRole()">
                            <div class="switch-icon">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </div>
                            <div class="dropdown-header-text">
                                <div class="dropdown-header-title">Beralih ke akun</div>
                                <div class="dropdown-header-role" id="dropdown-role-label">Penyelenggara</div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <div class="dropdown-list">
                            <a href="{{ url('/dashboard') }}" class="dropdown-item creator-only">
                                <i data-lucide="layout-dashboard"></i> Dashboard
                            </a>
                            <a href="{{ url('/my-events') }}" class="dropdown-item creator-only">
                                <i data-lucide="calendar"></i> Event Saya
                            </a>
                            <a href="{{ url('/manage-access') }}" class="dropdown-item creator-only">
                                <i data-lucide="users"></i> Kelola Akses
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
    @endif

    @yield('custom-nav')

    <!-- Main Content -->
    <main id="app" class="flex-1">
        @yield('content')
    </main>

    @if(empty($hideChrome) && !View::hasSection('custom-footer'))
    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-brand">
                <div class="logo flex items-center gap-2 mb-4">
                    <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasara Logo" class="footer-logo-img">
                    <span class="logo-text">PENTASARA</span>
                </div>
                <div class="footer-tagline">Melestarikan Budaya, Menghubungkan Jiwa.</div>
                <div class="footer-social-wrap mt-6">
                    <p class="social-label">Hubungi Kurator Kami:</p>
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
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Beranda</a></li>
                    <li><a href="{{ url('/events') }}">Cari Event</a></li>
                    <li><a href="{{ url('/tentang-kami') }}">Tentang Kami</a></li>
                    <li><a href="{{ url('/pusat-bantuan') }}">Bantuan</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4>Legal</h4>
                <ul>
                    <li><a href="{{ url('/kebijakan-privasi') }}">Kebijakan Privasi</a></li>
                    <li><a href="{{ url('/syarat-ketentuan') }}">Syarat & Ketentuan</a></li>
                    <li><a href="{{ url('/hubungi-kami') }}">Hubungi Kami</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Pentasera Cultural Curator. All rights reserved.
        </div>
    </footer>
    @endif

    @yield('custom-footer')

    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}" type="module"></script>
    @stack('scripts')
</body>
</html>
