@extends('layouts.app')
@section('title', 'Kelola Event | Pentasara')

@section('custom-nav'){{-- Dashboard uses sidebar, no top nav --}}@endsection
@section('custom-footer'){{-- No footer on dashboard pages --}}@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
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
                <a href="{{ url('/my-events') }}" class="nav-item active creator-only">
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
                <a href="{{ url('/my-events') }}" class="flex items-center gap-2 text-gray-400 hover:text-rust transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Event Saya
                </a>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <!-- Event Header Info -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ strtoupper($event->event_status) }}</span>
                    <span class="text-gray-400 text-xs">ID Event: #EVT-{{ str_pad($event->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h1 class="font-display text-3xl text-ink mb-2">{{ $event->nama_event }}</h1>
                <p class="text-gray-500 text-sm flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    {{ $event->lokasi }}
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="saveEventChanges()" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card">
                <div class="stat-header"><div class="stat-label"><i data-lucide="ticket"></i> Tiket Terjual</div></div>
                <div class="stat-value" id="stat-tickets-sold">{{ $stats['sold'] }} / {{ $stats['capacity'] }}</div>
                <div class="stat-unit" id="stat-tickets-percent">{{ $stats['occupancy'] }}% Terisi</div>
            </div>
            <div class="stat-card">
                <div class="stat-header"><div class="stat-label"><i data-lucide="trending-up"></i> Total Penjualan</div></div>
                <div class="stat-value" id="stat-total-sales">{{ $stats['revenue_formatted'] }}</div>
                <div class="stat-unit">IDR</div>
            </div>
            <div class="stat-card">
                <div class="stat-header"><div class="stat-label"><i data-lucide="shopping-cart"></i> Total Transaksi</div></div>
                <div class="stat-value" id="stat-total-transactions">{{ $transactions->count() }}</div>
                <div class="stat-unit">Transaksi Berhasil</div>
            </div>
        </div>

        <!-- Management Tabs -->
        <div class="event-tabs">
            <div class="event-tab active" onclick="switchManageTab('info')">Informasi Event</div>
            <div class="event-tab" onclick="switchManageTab('tiket')">Tiket & Harga</div>
            <div class="event-tab" onclick="switchManageTab('penjualan')">Laporan Penjualan</div>
        </div>

        <!-- Tab Content: Info -->
        <div id="manage-info" class="event-tab-content active">
            <div class="profile-section">
                <div class="banner-upload">
                    <img src="{{ $event->image_src }}" alt="Banner {{ $event->nama_event }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                        <button class="bg-white text-ink px-4 py-2 rounded-lg font-bold text-xs">Ganti Banner</button>
                    </div>
                </div>
                <div class="profile-content-padding">
                    <div class="form-grid">
                        <div class="form-group full">
                            <label>Judul Event</label>
                            <input type="text" class="form-input" value="{{ $event->nama_event }}">
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select class="form-input">
                                <option selected>{{ $event->kategori_event ?? 'Tanpa kategori' }}</option>
                                <option>Musik Tradisional</option>
                                <option>Pertunjukan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal & Waktu</label>
                            <input type="datetime-local" class="form-input" value="{{ $event->event_datetime?->format('Y-m-d\TH:i') }}">
                        </div>
                        <div class="form-group full">
                            <label>Lokasi</label>
                            <input type="text" class="form-input" value="{{ $event->lokasi }}">
                        </div>
                        <div class="form-group full">
                            <label>Deskripsi Event</label>
                            <textarea class="form-input form-textarea h-40">{{ $event->deskripsi }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Tiket -->
        <div id="manage-tiket" class="event-tab-content">
            <div class="space-y-8 pb-16">
                <!-- Ticket Table -->
                <div class="bg-white rounded-[1.5rem] border border-gray-100 shadow-xl shadow-gray-200/20 overflow-hidden">
                    <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-rust rounded-2xl flex items-center justify-center text-white shadow-lg shadow-rust/20">
                                <i data-lucide="ticket" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-ink text-lg uppercase tracking-wider">Kategori Tiket</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Daftar tiket yang tersedia untuk event ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-white border-b border-gray-50">
                                    <th class="px-10 py-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Nama Tiket</th>
                                    <th class="px-10 py-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Harga</th>
                                    <th class="px-10 py-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Kapasitas</th>
                                    <th class="px-10 py-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Terjual</th>
                                    <th class="px-10 py-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Sisa</th>
                                    <th class="px-10 py-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-[0.2em]">Status</th>
                                    <th class="px-10 py-6"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($tickets as $ticket)
                                <tr class="hover:bg-rust/[0.02] transition-colors">
                                    <td class="px-10 py-7 font-bold text-ink text-sm">{{ $ticket->kategori }}</td>
                                    <td class="px-10 py-7 text-sm font-medium">{{ $ticket->formatted_price }}</td>
                                    <td class="px-10 py-7 text-sm text-gray-400">{{ $ticket->kuota }}</td>
                                    <td class="px-10 py-7 text-sm font-bold text-rust">{{ $ticket->sold_quantity }}</td>
                                    <td class="px-10 py-7 text-sm text-gray-400 font-medium">{{ $ticket->sisa_kuota }}</td>
                                    <td class="px-10 py-7"><span class="bg-green-50 text-green-600 text-[10px] font-bold px-4 py-1.5 rounded-full border border-green-100 uppercase tracking-wider">Tersedia</span></td>
                                    <td class="px-10 py-7 text-right">
                                        <button onclick="handleTicketAction('edit', 'paid')" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-300 hover:bg-rust/10 hover:text-rust transition-all">
                                            <i data-lucide="edit-3" class="w-5 h-5"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-10 py-7 text-sm text-gray-400">Belum ada kategori tiket.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Add Ticket Button -->
                    <div class="px-10 py-10 bg-gray-50/10 border-t border-gray-50">
                        <button onclick="handleTicketAction('add')" class="w-full py-8 border-2 border-dashed border-gray-200 rounded-[2rem] flex items-center justify-center gap-4 text-gray-400 hover:border-rust hover:text-rust hover:bg-rust/[0.02] transition-all group cursor-pointer">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-gray-100 flex items-center justify-center group-hover:bg-rust group-hover:text-white group-hover:border-rust transition-all shadow-sm">
                                <i data-lucide="plus" class="w-6 h-6"></i>
                            </div>
                            <span class="font-bold text-[10px] uppercase tracking-[0.2em]">Tambah Kategori Tiket</span>
                        </button>
                    </div>
                </div>

                <!-- Dynamic Edit/Add Form Section -->
                <div id="ticket-action-section" class="hidden animate-fade-in-up mt-8">
                    <div class="bg-white rounded-[1.5rem] p-6 mb-4 border border-gray-100 shadow-2xl shadow-gray-200/20">
                        <div class="mb-10">
                            <h3 id="action-title" class="font-bold text-ink text-lg pt-6 mb-6 tracking-tight">Tambah Kategori Tiket</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Paid Option -->
                                <div onclick="setTicketCategory('paid')" id="card-category-paid" class="relative border-2 rounded-[1.5rem] min-h-[56px] px-5 py-6 flex items-center justify-between transition-all cursor-pointer group overflow-hidden border-rust bg-rust/5 shadow-2xl shadow-rust/10">
                                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 border-r-4 border-dotted border-rust/30 h-12"></div>
                                    <div class="flex items-center gap-5 ml-4">
                                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-rust">
                                            <i data-lucide="banknote" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p id="label-tag-paid" class="text-[9px] font-bold text-rust uppercase tracking-[0.2em] mb-1">Pilih</p>
                                            <p class="text-lg font-bold text-ink tracking-tight">Berbayar</p>
                                        </div>
                                    </div>
                                    <div id="status-icon-paid" class="w-12 h-12 rounded-full bg-rust text-white flex items-center justify-center shadow-lg shadow-rust/40">
                                        <i data-lucide="check" class="w-6 h-6"></i>
                                    </div>
                                </div>
                                <!-- Free Option -->
                                <div onclick="setTicketCategory('free')" id="card-category-free" class="relative border-2 border-transparent bg-gray-50/50 rounded-[1.5rem] min-h-[56px] px-5 py-6 flex items-center justify-between transition-all cursor-pointer group overflow-hidden hover:bg-white hover:border-rust/20">
                                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 border-r-4 border-dotted border-gray-200 h-12"></div>
                                    <div class="flex items-center gap-5 ml-4">
                                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-gray-300">
                                            <i data-lucide="gift" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p id="label-tag-free" class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Opsi Lain</p>
                                            <p class="text-lg font-bold text-ink tracking-tight text-gray-300">Gratis</p>
                                        </div>
                                    </div>
                                    <div id="status-icon-free" class="w-12 h-12 rounded-full border-2 border-gray-100 flex items-center justify-center text-gray-300 group-hover:border-rust group-hover:text-rust transition-all">
                                        <i data-lucide="plus" class="w-6 h-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-4 mr-6 mb-12">
                            <button onclick="openModal('modal-tiket', currentCategory)" class="group active:scale-95 bg-rust text-white px-6 py-2.5 rounded-lg font-bold text-[9px] shadow-2xl shadow-rust/40 hover:bg-rust-deep transition-all flex items-center gap-3">
                                <span id="btn-action-text" class="uppercase tracking-widest">Lanjut Atur Detail Tiket</span>
                                <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center group-hover:translate-x-1 transition-transform">
                                    <i data-lucide="arrow-right" class="w-2.5 h-2.5 text-white"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contact & Settings -->
                <div class="bg-white rounded-[1.5rem] p-12 pb-16 border border-gray-100 shadow-xl shadow-gray-200/20">
                    <div class="mb-8">
                        <h3 class="font-bold text-ink text-2xl pt-4 mb-6 tracking-tight flex items-center gap-3">
                            Informasi Kontak <span class="w-2 h-2 bg-rust rounded-full"></span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-12 gap-y-8 mb-8">
                            <div class="space-y-6">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">Nama Narahubung*</label>
                                <input type="text" placeholder="Nama Penyelenggara" value="{{ $event->organizer?->organizer_name }}" class="w-full bg-[#FAFAF8] border-none rounded-2xl px-5 py-4 text-base font-bold text-ink focus:ring-2 focus:ring-rust/20 outline-none transition-all placeholder:text-gray-300 placeholder:font-normal">
                            </div>
                            <div class="space-y-6">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">Email*</label>
                                <input type="email" placeholder="contact@event.com" value="{{ $event->organizer?->contact_email }}" class="w-full bg-[#FAFAF8] border-none rounded-2xl px-5 py-4 text-base font-bold text-ink focus:ring-2 focus:ring-rust/20 outline-none transition-all placeholder:text-gray-300 placeholder:font-normal">
                            </div>
                            <div class="space-y-6">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] ml-1">No. Ponsel*</label>
                                <input type="tel" placeholder="08123456789" value="{{ $event->organizer?->contact_phone }}" class="w-full bg-[#FAFAF8] border-none rounded-2xl px-5 py-4 text-base font-bold text-ink focus:ring-2 focus:ring-rust/20 outline-none transition-all placeholder:text-gray-300 placeholder:font-normal">
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 mt-8 mb-8"></div>
                    <div class="mt-0">
                        <h3 class="font-bold text-ink text-base mb-6 tracking-tight flex items-center gap-3">
                            Pengaturan Tambahan <span class="w-2 h-2 bg-rust rounded-full"></span>
                        </h3>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            <div class="flex items-center justify-between bg-gray-50/50 px-6 py-6 rounded-[2rem] border border-gray-100 group hover:bg-white hover:border-rust/20 transition-all h-auto">
                                <div>
                                    <p class="text-base font-bold text-ink mb-1">Jumlah maks. tiket per transaksi</p>
                                    <p class="text-xs text-gray-400">Batasi jumlah tiket yang dapat dibeli sekali checkout</p>
                                </div>
                                <div class="bg-white p-2 rounded-2xl border border-gray-100 shadow-sm">
                                    <input type="number" value="5" min="1" max="5" class="w-20 py-3 text-center text-xl font-black text-rust outline-none">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between px-6 py-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 h-auto">
                                    <div>
                                        <p class="text-[11px] font-black text-ink mb-2 uppercase tracking-tight">1 akun email – 1 kali transaksi</p>
                                        <p class="text-[10px] text-gray-400">Mencegah pembelian berulang</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-200 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rust shadow-inner"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between px-6 py-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 h-auto">
                                    <div>
                                        <p class="text-[11px] font-black text-ink mb-2 uppercase tracking-tight">1 tiket – 1 data pemesan</p>
                                        <p class="text-[10px] text-gray-400">Identitas berbeda per tiket</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-200 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rust shadow-inner"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.manage-event-penjualan')

    </main>
</div>

@include('partials.manage-event-modals')
@endsection

@push('scripts')
<script src="{{ asset('js/manage-event.js') }}"></script>
@endpush
