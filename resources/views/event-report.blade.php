@extends('layouts.app')
@section('title', 'Laporan Event | Pentasera')

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
            <img src="{{ asset('assets/logo pentasera.png') }}" alt="Pentasera Logo" class="w-10 h-10 object-contain">
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
    <main class="dashboard-main px-8 py-8">
        <header class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url('/my-events') }}" class="w-10 h-10 bg-white rounded-xl border border-gray-100 flex items-center justify-center text-gray-400 hover:text-rust hover:border-rust/20 transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        @php
                            $statusLabel = match($event->event_status) {
                                'published' => 'Aktif',
                                'draft' => 'Draf',
                                'cancelled' => 'Dibatalkan',
                                default => ucfirst($event->event_status),
                            };
                            $statusColor = match($event->event_status) {
                                'published' => 'bg-green-100 text-green-600',
                                'draft' => 'bg-yellow-100 text-yellow-600',
                                'cancelled' => 'bg-red-100 text-red-600',
                                default => 'bg-gray-200 text-gray-600',
                            };
                        @endphp
                        <span class="{{ $statusColor }} text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">{{ $statusLabel }}</span>
                        <span class="text-gray-400 text-[10px] font-bold">Laporan Event #EVT-{{ str_pad($event->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h1 id="report-event-name" class="font-display text-2xl text-ink font-bold">{{ $event->nama_event }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button class="bg-white border border-gray-200 text-ink px-4 py-2 rounded-lg font-bold text-xs flex items-center gap-2 hover:bg-gray-50 transition-all">
                    <i data-lucide="share-2" class="w-4 h-4"></i>
                    Bagikan
                </button>
                <button id="btn-export-csv" onclick="exportCSV()" class="bg-rust text-white px-5 py-2.5 rounded-lg font-bold text-xs flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export CSV
                </button>
                <button onclick="window.print()" class="bg-rust text-white px-5 py-2.5 rounded-lg font-bold text-xs flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak Laporan
                </button>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12 space-x-0">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Pendapatan</p>
                <h3 class="text-xl font-black text-ink">{{ $stats['revenueFormatted'] }}</h3>
                <p class="text-[10px] text-green-500 font-bold mt-1">
                    @if($stats['capacity'] > 0)
                        {{ number_format(($stats['sold'] / $stats['capacity']) * 100, 1) }}% Target tercapai
                    @else
                        Belum ada tiket
                    @endif
                </p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tiket Terjual</p>
                <h3 class="text-xl font-black text-ink">{{ number_format($stats['sold']) }} / {{ number_format($stats['capacity']) }}</h3>
                <p class="text-[10px] text-rust font-bold mt-1">{{ $stats['occupancy'] }} Terisi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
            <!-- Sales Analytics -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/10">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-base font-black text-ink uppercase tracking-tight">Tren Penjualan Tiket</h4>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400 uppercase">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-rust rounded-full"></span>
                                Tiket Terjual
                            </div>
                        </div>
                    </div>
                    <div class="h-56 w-full" id="report-chart"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/10">
                    <h4 class="text-base font-black text-ink uppercase tracking-tight mb-6">Rincian Per Kategori</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-gray-50">
                                <tr>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Terjual</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Pendapatan</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Okupansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($ticketsBreakdown as $tb)
                                <tr>
                                    <td class="py-3">
                                        <p class="font-bold text-ink text-sm">{{ $tb['kategori'] }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $tb['harga_formatted'] }}</p>
                                    </td>
                                    <td class="py-3 text-center font-bold text-ink text-sm">{{ $tb['terjual'] }}</td>
                                    <td class="py-3 text-right font-bold text-ink text-sm">{{ $tb['revenue_formatted'] }}</td>
                                    <td class="py-3">
                                        <div class="flex items-center justify-end gap-3 font-bold text-[10px] text-rust">
                                            {{ $tb['occupancy'] }}%
                                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-rust rounded-full" style="width: {{ min(100, $tb['occupancy']) }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Demographics & Extras -->
            <div class="space-y-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/10">
                    <h4 class="text-base font-black text-ink uppercase tracking-tight mb-6">Ringkasan Transaksi</h4>
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-ink uppercase tracking-wider">Total Order</span>
                                <span class="text-xs font-black text-rust">{{ $recentTransactions->count() }}</span>
                            </div>
                        </div>
                        @foreach($ticketsBreakdown as $tb)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-ink uppercase tracking-wider">{{ $tb['kategori'] }}</span>
                                <span class="text-xs font-black text-rust">{{ $tb['occupancy'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-rust h-full rounded-full" style="width: {{ min(100, $tb['occupancy']) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Transactions Preview -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-xl shadow-gray-200/10">
                    <h4 class="text-base font-black text-ink uppercase tracking-tight mb-6">Transaksi Terbaru</h4>
                    <div class="space-y-4">
                        @forelse($recentTransactions->take(5) as $tx)
                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                            <div>
                                <p class="text-sm font-bold text-ink">{{ $tx['buyer_name'] }}</p>
                                <p class="text-[10px] text-gray-400">{{ $tx['tickets'] }} &middot; {{ $tx['qty'] }}x</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-ink">{{ $tx['total_formatted'] }}</p>
                                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($tx['date'])->format('d M Y') }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 text-center py-4">Belum ada transaksi</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
    // Inject server data for JS chart and CSV export
    window.__dailySales = @json($dailySales);
    window.__recentTransactions = @json($recentTransactions);
    window.__eventName = @json($event->nama_event);
</script>
<script src="{{ asset('js/event-report.js') }}"></script>
@endpush
