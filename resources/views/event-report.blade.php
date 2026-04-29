@extends('layouts.app')
@section('title', 'Laporan Event | Pentasara')

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
                <a href="{{ url('/dashboard') }}" class="nav-item">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                <a href="{{ url('/my-events') }}" class="nav-item active">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Event Saya
                </a>
                <a href="{{ url('/manage-access') }}" class="nav-item">
                    <i data-lucide="users" class="w-5 h-5"></i> Kelola Akses
                </a>
                <a href="{{ url('/my-tickets') }}" class="nav-item user-only">
                    <i data-lucide="ticket" class="w-5 h-5"></i> Tiket Saya
                </a>
            </div>
        </nav>
        <div class="sidebar-footer">
            <button onclick="toggleRole()" class="switch-mode-btn">
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i> Beralih ke Pembeli
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main px-8 py-10">
        <header class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-6">
                <a href="{{ url('/my-events') }}" class="w-12 h-12 bg-white rounded-2xl border border-gray-100 flex items-center justify-center text-gray-400 hover:text-rust hover:border-rust/20 transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="bg-gray-200 text-gray-600 text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest">Selesai</span>
                        <span class="text-gray-400 text-[10px] font-bold">Laporan Event #EVT-2026-042</span>
                    </div>
                    <h1 id="report-event-name" class="font-display text-3xl text-ink font-bold">Witness the Fire of Uluwatu</h1>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <button class="bg-white border border-gray-200 text-ink px-6 py-3 rounded-xl font-bold text-xs flex items-center gap-2 hover:bg-gray-50 transition-all">
                    <i data-lucide="share-2" class="w-4 h-4"></i>
                    Bagikan
                </button>
                <button class="bg-rust text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-rust/20 hover:bg-rust-deep transition-all">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak Laporan
                </button>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-50 space-x-0">
            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Total Pendapatan</p>
                <h3 class="text-2xl font-black text-ink">Rp 75.320.000</h3>
                <p class="text-[10px] text-green-500 font-bold mt-2">Target tercapai 105%</p>
            </div>
            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-200/10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Tiket Terjual</p>
                <h3 class="text-2xl font-black text-ink">482 / 500</h3>
                <p class="text-[10px] text-rust font-bold mt-2">96.4% Terisi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-20">
            <!-- Sales Analytics -->
            <div class="lg:col-span-2 space-y-12">
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/10">
                    <div class="flex items-center justify-between mb-12">
                        <h4 class="text-lg font-black text-ink uppercase tracking-tight">Tren Penjualan Tiket</h4>
                        <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400 uppercase">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-rust rounded-full"></span>
                                Tiket Terjual
                            </div>
                        </div>
                    </div>
                    <div class="h-64 w-full" id="report-chart"></div>
                </div>

                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/10">
                    <h4 class="text-lg font-black text-ink uppercase tracking-tight mb-8">Rincian Per Kategori</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-gray-50">
                                <tr>
                                    <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori</th>
                                    <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Terjual</th>
                                    <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Pendapatan</th>
                                    <th class="pb-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Okupansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr>
                                    <td class="py-5">
                                        <p class="font-bold text-ink text-sm">Reguler (Domestik)</p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Rp 150.000</p>
                                    </td>
                                    <td class="py-5 text-center font-bold text-ink text-sm">382</td>
                                    <td class="py-5 text-right font-bold text-ink text-sm">Rp 57.300.000</td>
                                    <td class="py-5">
                                        <div class="flex items-center justify-end gap-3 font-bold text-[10px] text-rust">
                                            95.5%
                                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-rust rounded-full" style="width: 95.5%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-5">
                                        <p class="font-bold text-ink text-sm">VIP Front Row</p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">Rp 300.000</p>
                                    </td>
                                    <td class="py-5 text-center font-bold text-ink text-sm">100</td>
                                    <td class="py-5 text-right font-bold text-ink text-sm">Rp 30.000.000</td>
                                    <td class="py-5">
                                        <div class="flex items-center justify-end gap-3 font-bold text-[10px] text-rust">
                                            100%
                                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-rust rounded-full" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Demographics & Extras -->
            <div class="space-y-12">
                <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/10">
                    <h4 class="text-lg font-black text-ink uppercase tracking-tight mb-8">Demografi Pengunjung</h4>
                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-ink uppercase tracking-wider">Domestik (Bali)</span>
                                <span class="text-xs font-black text-rust">65%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-rust h-full rounded-full" style="width: 65%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-ink uppercase tracking-wider">Luar Kota</span>
                                <span class="text-xs font-black text-rust">25%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-rust/60 h-full rounded-full" style="width: 25%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-ink uppercase tracking-wider">Mancanegara</span>
                                <span class="text-xs font-black text-rust">10%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-rust/30 h-full rounded-full" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/event-report.js') }}"></script>
@endpush
