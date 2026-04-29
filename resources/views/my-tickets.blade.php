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

        <!-- Tickets Grid -->
        <div class="max-w-5xl" id="tickets-list">
            <!-- Ticket item 1: LUNAS & AKTIF -->
            <div class="ticket-card" data-status="aktif">
                <div class="w-full md:w-56 h-48 md:h-auto overflow-hidden relative group" style="min-width:224px;">
                    <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=2070" alt="Event" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-3 left-3">
                        <span class="status-badge-ticket status-success shadow-sm">E-Tiket Siap</span>
                    </div>
                </div>

                <div class="flex-grow p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="font-display text-xl text-ink leading-tight">Gamelan Jawa: Kidung Malam</h2>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded">#PS-99210</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 mt-4">
                            <div class="flex items-center gap-2 text-gray-500 text-sm">
                                <i data-lucide="calendar" class="w-4 h-4 text-rust/70"></i>
                                <span>Sabtu, 24 Mei 2024</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 text-sm">
                                <i data-lucide="clock" class="w-4 h-4 text-rust/70"></i>
                                <span>19:00 WIB</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 text-sm">
                                <i data-lucide="map-pin" class="w-4 h-4 text-rust/70"></i>
                                <span>Gedung Kesenian Jakarta</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500 text-sm">
                                <i data-lucide="ticket" class="w-4 h-4 text-rust/70"></i>
                                <span>2 Tiket (Reguler)</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <span class="text-sm font-medium text-ink">Pembayaran Lunas</span>
                        </div>
                        <span class="text-lg font-bold text-rust">Rp 300.000</span>
                    </div>
                </div>

                <div class="ticket-divider hidden md:block"></div>

                <div class="p-6 flex flex-col items-center justify-center bg-gray-50/30 md:w-56 text-center border-t md:border-t-0 md:border-l border-gray-100" style="min-width:224px;">
                    <!-- Barcode Area -->
                    <div class="mb-6 w-full">
                        <div class="flex flex-col items-center gap-3">
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=PS-99210&code=Code128&scale=1" class="h-12 w-full object-contain opacity-80" alt="Barcode">
                            <span class="text-[10px] font-bold text-gray-400 font-mono tracking-widest">PS-99210</span>
                        </div>
                    </div>
                    <button onclick="showTicket('Gamelan Jawa: Kidung Malam', '#PS-99210')" class="w-full bg-rust text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-rust-deep transition-all shadow-md shadow-rust/10 cursor-pointer">Lihat Tiket</button>
                </div>
            </div>

            <!-- Ticket item 2: MENUNGGU PEMBAYARAN -->
            <div class="ticket-card" data-status="aktif">
                <div class="w-full md:w-56 h-48 md:h-auto overflow-hidden relative group" style="min-width:224px;">
                    <img src="https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?q=80&w=2069" alt="Event" class="w-full h-full object-cover grayscale opacity-60">
                    <div class="absolute top-3 left-3">
                        <span class="status-badge-ticket status-pending shadow-sm">Menunggu Bayar</span>
                    </div>
                </div>

                <div class="flex-grow p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="font-display text-xl text-ink leading-tight">Festival Wayang Internasional</h2>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded">#PS-10492</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 mt-4">
                            <div class="flex items-center gap-2 text-gray-400 text-sm italic">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>Minggu, 15 Juni 2024</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-400 text-sm italic">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                <span>Museum Wayang Jakarta</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center">
                                <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <span class="text-sm font-medium text-amber-700">Batas bayar: <span class="font-bold">23:59:00</span></span>
                        </div>
                        <span class="text-lg font-bold text-gray-400">Rp 150.000</span>
                    </div>
                </div>

                <div class="ticket-divider hidden md:block"></div>

                <div class="p-6 flex flex-col items-center justify-center bg-gray-50/30 md:w-56 text-center border-t md:border-t-0 md:border-l border-gray-100" style="min-width:224px;">
                    <div class="mb-5 flex flex-col items-center text-gray-300">
                        <i data-lucide="lock" class="w-10 h-10 mb-2 opacity-20"></i>
                        <p class="text-[10px] leading-relaxed">Selesaikan pembayaran untuk mengaktifkan kode tiket</p>
                    </div>
                    <button onclick="location.href='{{ url('/payment') }}'" class="w-full bg-amber-500 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-amber-600 transition-all shadow-md shadow-amber-500/20 cursor-pointer">Bayar Sekarang</button>
                </div>
            </div>

            <!-- Ticket item 3: FISIK DIAMBIL / TERPAKAI -->
            <div class="ticket-card" data-status="selesai">
                <div class="w-full md:w-56 h-48 md:h-auto overflow-hidden relative group" style="min-width:224px;">
                    <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=2070" alt="Event" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-ink/60 flex items-center justify-center">
                        <span class="status-badge-ticket status-used scale-125">Check-in Berhasil</span>
                    </div>
                </div>

                <div class="flex-grow p-6 flex flex-col justify-between bg-gray-50/50">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="font-display text-xl text-gray-400 leading-tight">Sendratari Ramayana Prambanan</h2>
                            <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest bg-gray-200/50 px-2 py-1 rounded">#PS-99345</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 mt-4">
                            <div class="flex items-center gap-2 text-gray-400 text-sm">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                <span>Selesai (12 April 2024)</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-400 text-sm">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                <span>Pintu Masuk VIP B</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center">
                                <i data-lucide="package" class="w-4 h-4 text-blue-600"></i>
                            </div>
                            <span class="text-sm font-medium text-blue-600">Tiket Fisik Sudah Diambil</span>
                        </div>
                        <div class="flex items-center gap-1 text-gray-400">
                            <i data-lucide="star" class="w-3 h-3 fill-amber-400 text-amber-400"></i>
                            <span class="text-xs">Ulasan Terkirim</span>
                        </div>
                    </div>
                </div>

                <div class="ticket-divider hidden md:block"></div>

                <div class="p-6 flex flex-col items-center justify-center bg-gray-100/30 md:w-56 text-center border-t md:border-t-0 md:border-l border-gray-100" style="min-width:224px;">
                    <div class="mb-6 w-full opacity-30 grayscale">
                        <div class="flex flex-col items-center gap-3">
                            <img src="https://barcode.tec-it.com/barcode.ashx?data=PS-99345&code=Code128&scale=1" class="h-12 w-full object-contain" alt="Barcode">
                            <span class="text-[10px] font-bold font-mono tracking-widest">PS-99345</span>
                        </div>
                    </div>
                    <button class="w-full border border-gray-200 text-gray-400 px-6 py-2.5 rounded-xl text-xs font-bold cursor-not-allowed">Sudah Terpakai</button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Ticket View Modal -->
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
                <div id="modalBarcode" class="w-full min-h-[120px] bg-white p-6 rounded-2xl shadow-xl shadow-rust/5 flex items-center justify-center mb-6">
                    <!-- Barcode here -->
                </div>
                <div class="text-center">
                    <p class="text-sm font-semibold text-ink">Tunjukkan Barcode ini ke Petugas</p>
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
<script>
    // Re-init Lucide icons for this page
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Show Ticket Modal
    function showTicket(name, id) {
        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalId').innerText = id;

        // Clean the ID for barcode (remove # if exist)
        const cleanId = id.replace('#', '');

        document.getElementById('modalBarcode').innerHTML = `<img src="https://barcode.tec-it.com/barcode.ashx?data=${cleanId}&code=Code128&translate-esc=true" class="h-24 w-full object-contain" alt="Barcode ${id}">`;

        document.getElementById('ticketModal').style.display = 'flex';
    }

    // Close Ticket Modal
    function closeTicket() {
        document.getElementById('ticketModal').style.display = 'none';
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeTicket();
    });

    // Close on backdrop click
    document.getElementById('ticketModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('ticketModal')) closeTicket();
    });

    // Tab Filtering
    const tabs = document.querySelectorAll('.ticket-tab-btn');
    const tickets = document.querySelectorAll('.ticket-card');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active classes
            tabs.forEach(t => t.classList.remove('active'));
            // Add active class
            tab.classList.add('active');

            const filter = tab.getAttribute('data-filter');
            tickets.forEach(ticket => {
                const status = ticket.getAttribute('data-status');
                if (status === filter) {
                    ticket.style.display = 'flex';
                } else {
                    ticket.style.display = 'none';
                }
            });
        });
    });

    // Search functionality
    document.getElementById('ticketSearch').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        tickets.forEach(ticket => {
            const name = ticket.querySelector('h2')?.innerText.toLowerCase() || '';
            if (name.includes(query)) {
                ticket.style.display = 'flex';
            } else {
                ticket.style.display = 'none';
            }
        });
    });
</script>
@endpush
