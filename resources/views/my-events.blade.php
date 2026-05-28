@extends('layouts.app')
@section('title', 'Event Saya | Pentasera')

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
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera" class="w-10 h-10 object-contain">
            <span class="logo-text text-sm">PENTASERA</span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <p class="nav-label">Main Menu</p>
                <a href="{{ url('/dashboard') }}" class="nav-item creator-only">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item active creator-only">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                    <span class="ml-auto bg-white/20 text-white text-[10px] px-2 py-0.5 rounded-full">{{ count($activeEvents) + count($draftEvents) }}</span>
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
                <a href="{{ url('/dashboard') }}">Pentasera</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span>Event Saya</span>
            </div>
            <div class="header-actions">
                <a href="{{ url('/create-event') }}" class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Buat Event
                </a>
            </div>
        </header>

        <div class="mb-8">
            <h1 class="font-display text-3xl text-ink mb-2">Event</h1>
            <p class="text-gray-500 text-sm">Manajemen daftar pementasan budaya Anda</p>
        </div>

        <div class="event-tabs">
            <div class="event-tab active" onclick="switchMyEventTab('aktif')">Event Aktif</div>
            <div class="event-tab" onclick="switchMyEventTab('draft')">Event Draft</div>
            <div class="event-tab" onclick="switchMyEventTab('lalu')">Event Lalu</div>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div class="search-input-wrapper">
                <i data-lucide="search"></i>
                <input type="text" class="search-input" placeholder="Cari event...">
            </div>
            <button class="w-10 h-10 rounded-lg bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-rust transition-colors">
                <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Tab Content: Aktif -->
        <div id="tab-aktif" class="event-tab-content active">
            <div class="event-dashboard-grid">
                @forelse($activeEvents as $event)
                @php
                    $capacity = $event->tickets->sum('kuota');
                    $sold = $event->tickets->sum(fn($t) => $t->kuota - $t->sisa_kuota);
                @endphp
                <div class="event-dashboard-card">
                    <div class="event-card-img">
                        <img src="{{ $event->image_src }}" alt="{{ $event->nama_event }}">
                        <span class="event-status-badge">Aktif</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-card-title">{{ $event->nama_event }}</h3>
                        <div class="event-card-meta">
                            <div class="meta-item">
                                <label>Tiket</label>
                                <span>{{ $sold }} / {{ $capacity }}</span>
                            </div>
                            <div class="meta-item">
                                <label>Tanggal Event</label>
                                <span>{{ \Carbon\Carbon::parse($event->event_datetime)->isoFormat('DD MMMM YYYY') }}</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <div class="status-indicator">
                                <div class="status-dot"></div>
                                <span>AKTIF</span>
                            </div>
                            <a href="{{ url('/manage-event/' . $event->id) }}" class="kelola-link">
                                Kelola
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Belum ada event aktif</p>
                    <p class="text-sm mt-1">Buat event baru untuk memulai</p>
                </div>
                @endforelse

                <!-- Create Event Placeholder -->
                <div onclick="location.href='{{ url('/create-event') }}'" class="create-event-placeholder">
                    <div class="placeholder-icon">
                        <i data-lucide="plus" class="w-8 h-8"></i>
                    </div>
                    <h4 class="font-bold text-ink mb-2">Buat Event Baru</h4>
                    <p class="text-gray-400 text-xs leading-relaxed">Mulai daftarkan pementasan seni budaya Anda hari ini</p>
                </div>
            </div>
        </div>

        <!-- Tab Content: Draft -->
        <div id="tab-draft" class="event-tab-content">
            <div class="event-dashboard-grid">
                @forelse($draftEvents as $event)
                @php
                    $capacity = $event->tickets->sum('kuota');
                @endphp
                <div class="event-dashboard-card opacity-90 cursor-pointer" onclick="location.href='{{ url('/manage-event/' . $event->id) }}'">
                    <div class="event-card-img" style="filter: grayscale(0.5);">
                        <img src="{{ $event->image_src }}" alt="{{ $event->nama_event }}">
                        <span class="event-status-badge" style="background: #9ca3af;">Draft</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-card-title">{{ $event->nama_event }}</h3>
                        <div class="event-card-meta">
                            <div class="meta-item">
                                <label>Kapasitas</label>
                                <span>{{ $capacity }}</span>
                            </div>
                            <div class="meta-item">
                                <label>Status</label>
                                <span>Draft</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <div class="status-indicator text-gray-400">
                                <div class="status-dot bg-gray-400"></div>
                                <span>DRAFT</span>
                            </div>
                            <a href="{{ url('/manage-event/' . $event->id) }}" class="kelola-link">
                                Edit Draft
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Belum ada event draft</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Tab Content: Lalu -->
        <div id="tab-lalu" class="event-tab-content">
            <div class="event-dashboard-grid">
                @forelse($pastEvents as $event)
                @php
                    $sold = $event->tickets->sum(fn($t) => $t->kuota - $t->sisa_kuota);
                @endphp
                <div class="event-dashboard-card opacity-75 cursor-pointer" onclick="location.href='{{ url('/event-report/' . $event->id) }}'">
                    <div class="event-card-img" style="filter: grayscale(1);">
                        <img src="{{ $event->image_src }}" alt="{{ $event->nama_event }}">
                        <span class="event-status-badge" style="background: #2C1A0E;">Selesai</span>
                    </div>
                    <div class="event-card-body">
                        <h3 class="event-card-title">{{ $event->nama_event }}</h3>
                        <div class="event-card-meta">
                            <div class="meta-item">
                                <label>Total Terjual</label>
                                <span>{{ $sold }}</span>
                            </div>
                            <div class="meta-item">
                                <label>Tanggal</label>
                                <span>{{ \Carbon\Carbon::parse($event->event_datetime)->isoFormat('DD MMM YYYY') }}</span>
                            </div>
                        </div>
                        <div class="event-card-footer">
                            <div class="status-indicator text-ink">
                                <div class="status-dot bg-ink"></div>
                                <span>SELESAI</span>
                            </div>
                            <a href="{{ url('/event-report/' . $event->id) }}" class="kelola-link">
                                Laporan
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i data-lucide="archive" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                    <p class="font-bold">Belum ada event yang selesai</p>
                </div>
                @endforelse
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/my-events.js') }}"></script>
@endpush
