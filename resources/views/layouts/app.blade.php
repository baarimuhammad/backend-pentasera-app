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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/style-support.css') }}?v={{ time() }}">

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
        <div class="search-container" id="search-container">
            <form action="{{ url('/events') }}" method="GET" class="search-bar" id="navbar-search-form">
                <input type="text" name="q" placeholder="Cari event, lokasi, atau kategori..." value="{{ request('q') }}" autocomplete="off" id="navbar-search-input">
                <button type="submit" class="search-btn">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </button>
            </form>
            <div class="live-search-dropdown" id="live-search-dropdown"></div>
        </div>
        <ul class="nav-links">
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
                    <div class="profile-img-wrap" title="" id="nav-profile-wrap">
                        <div id="nav-avatar-letter" class="w-full h-full flex items-center justify-center bg-rust text-white font-bold text-sm rounded-full">U</div>
                    </div>
                    <div class="dropdown-menu">
                        <div class="dropdown-user-info px-4 py-2 border-b border-gray-100">
                            <div class="font-bold text-ink text-sm" id="nav-user-name">User</div>
                            <div class="text-xs text-gray-400" id="nav-user-email">-</div>
                        </div>
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

                            <a href="{{ url('/my-tickets') }}" class="dropdown-item user-only">
                                <i data-lucide="ticket"></i> Tiket Saya
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

        <!-- Hamburger Menu Button (mobile only) -->
        <button class="hamburger-btn" id="hamburger-btn" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </nav>

    <!-- Mobile Nav Overlay -->
    <div class="mobile-nav-overlay" id="mobile-nav-overlay"></div>

    <!-- Mobile Nav Drawer -->
    <div class="mobile-nav-drawer" id="mobile-nav-drawer">
        <div class="mobile-nav-header">
            <span class="logo-text">PENTASARA</span>
            <button class="mobile-nav-close" id="mobile-nav-close" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <ul class="mobile-nav-links">
            <li><a href="{{ url('/') }}"><i data-lucide="home"></i> Beranda</a></li>
            <li><a href="{{ url('/tentang-kami') }}"><i data-lucide="info"></i> About Us</a></li>
            <li><a href="{{ url('/events') }}"><i data-lucide="calendar"></i> Events</a></li>
            <li><a href="{{ url('/hubungi-kami') }}"><i data-lucide="mail"></i> Contact</a></li>
            <div class="mobile-nav-divider"></div>
            {{-- Logged-in actions --}}
            <li class="logged-in-only creator-only"><a href="{{ url('/create-event') }}"><i data-lucide="plus-circle"></i> Buat Event</a></li>
            <li class="logged-in-only user-only"><a href="{{ url('/my-tickets') }}"><i data-lucide="ticket"></i> Tiket Saya</a></li>
            <li class="logged-in-only creator-only"><a href="{{ url('/dashboard') }}"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
            <li class="logged-in-only creator-only"><a href="{{ url('/my-events') }}"><i data-lucide="calendar-check"></i> Event Saya</a></li>
            <div class="mobile-nav-divider logged-in-only"></div>
            <li class="logged-in-only"><a href="{{ url('/profile') }}"><i data-lucide="user"></i> Profil</a></li>
            <li class="logged-in-only"><a href="{{ url('/settings') }}"><i data-lucide="settings"></i> Pengaturan</a></li>
            <li class="logged-in-only"><a href="#" onclick="logout(); return false;"><i data-lucide="log-out"></i> Keluar</a></li>
        </ul>

        <div class="mobile-nav-footer logged-out-only">
            <button class="btn-daftar" onclick="location.href='{{ url('/auth?tab=signup') }}'">Daftar</button>
            <button class="btn-masuk" onclick="location.href='{{ url('/auth') }}'">Masuk</button>
        </div>
    </div>
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
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
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

    <!-- API Helper (must load before app.js and page scripts) -->
    <script src="{{ asset('js/api-helper.js') }}?v={{ time() }}"></script>

    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Sync navbar auth state from api-helper --}}
    <script>
    (function() {
        function syncNavbar() {
            const loggedIn = isLoggedIn();
            const user = getUser();

            // Toggle body classes used by CSS to show/hide elements
            document.body.classList.toggle('is-logged-in', loggedIn);

            if (loggedIn && user) {
                const isCreator = user.role === 'creator';
                const isAdmin = user.role === 'admin';
                document.body.classList.toggle('is-creator', isCreator);
                document.body.classList.toggle('is-admin', isAdmin);

                // User name & avatar
                const nameEl = document.getElementById('nav-user-name');
                const emailEl = document.getElementById('nav-user-email');
                const avatarEl = document.getElementById('nav-avatar-letter');
                const profileWrap = document.getElementById('nav-profile-wrap');

                if (nameEl) nameEl.textContent = user.nama || 'User';
                if (emailEl) emailEl.textContent = user.email || '';
                if (avatarEl) avatarEl.textContent = (user.nama || 'U').charAt(0).toUpperCase();
                if (profileWrap) profileWrap.title = user.nama || '';

                // Role label
                const roleLabel = document.getElementById('dropdown-role-label');
                if (roleLabel) {
                    if (isAdmin) {
                        roleLabel.innerText = 'Admin';
                    } else {
                        roleLabel.innerText = isCreator ? 'Pembeli' : 'Penyelenggara';
                    }
                }

                // Hide role-switch header for admin
                if (isAdmin) {
                    const dropdownHeader = document.querySelector('.dropdown-header');
                    if (dropdownHeader) dropdownHeader.style.display = 'none';
                }
            } else {
                document.body.classList.remove('is-creator', 'is-admin');
            }
        }

        // Run immediately and also on DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncNavbar);
        } else {
            syncNavbar();
        }
    })();
    </script>

    {{-- Mobile hamburger menu toggle --}}
    <script>
    (function() {
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const mobileOverlay = document.getElementById('mobile-nav-overlay');
        const mobileDrawer = document.getElementById('mobile-nav-drawer');
        const mobileClose = document.getElementById('mobile-nav-close');

        function openMobileNav() {
            if (mobileOverlay) mobileOverlay.classList.add('active');
            if (mobileDrawer) mobileDrawer.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileNav() {
            if (mobileOverlay) mobileOverlay.classList.remove('active');
            if (mobileDrawer) mobileDrawer.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (hamburgerBtn) hamburgerBtn.addEventListener('click', openMobileNav);
        if (mobileClose) mobileClose.addEventListener('click', closeMobileNav);
        if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileNav);

        // Re-init Lucide icons for drawer
        if (typeof lucide !== 'undefined') {
            setTimeout(() => lucide.createIcons(), 100);
        }
    })();
    </script>

    @stack('scripts')
</body>
</html>
