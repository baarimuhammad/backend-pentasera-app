@extends('layouts.app')
@section('title', 'Laporan & Analitik | Pentasera Admin')

@section('custom-nav'){{-- Admin uses its own sidebar --}}@endsection
@section('custom-footer'){{-- No footer on admin pages --}}@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="admin-dashboard-container">
    <!-- Admin Sidebar -->
    @include('admin.partials.sidebar', ['activePage' => 'analytics'])

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <div>
                <div class="admin-breadcrumb"><a href="{{ url('/') }}">Home</a> / <a href="{{ url('/admin/dashboard') }}">Admin</a> / <span>Laporan & Analitik</span></div>
                <h1>Laporan & Analitik</h1>
            </div>
            <div class="export-dropdown-wrap" id="export-dropdown-wrap">
                <button class="btn-admin-primary" id="btn-export-toggle" type="button">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Ekspor Laporan
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
                <div class="export-dropdown-menu" id="export-dropdown-menu">
                    <a href="{{ url('/admin/export/csv') }}" class="export-dropdown-item" id="export-csv-link">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        <div>
                            <div class="export-item-title">Ekspor ke CSV</div>
                            <div class="export-item-desc">Download data dalam format spreadsheet</div>
                        </div>
                    </a>
                    <a href="{{ url('/admin/export/pdf') }}" target="_blank" class="export-dropdown-item" id="export-pdf-link">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <div>
                            <div class="export-item-title">Ekspor ke PDF</div>
                            <div class="export-item-desc">Buka halaman cetak untuk print / save PDF</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="stat-icon users">
                    <i data-lucide="users" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Pengguna</div>
                <div class="stat-value" id="analytics-total-users">—</div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-icon events">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Event</div>
                <div class="stat-value" id="analytics-total-events">—</div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-icon pending">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value" id="analytics-total-transactions">—</div>
            </div>
            <div class="admin-stat-card">
                <div class="stat-icon revenue">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value" id="analytics-total-revenue">—</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="admin-charts-grid">
            <!-- Revenue Trend -->
            <div class="admin-chart-card full-width">
                <h3>Tren Pendapatan</h3>
                <div class="chart-subtitle">Pendapatan bulanan dalam 12 bulan terakhir</div>
                <div style="position:relative; height:280px;">
                    <canvas id="chart-revenue-trend"></canvas>
                </div>
            </div>

            <!-- User Growth -->
            <div class="admin-chart-card">
                <h3>Pertumbuhan Pengguna</h3>
                <div class="chart-subtitle">Pengguna baru per bulan</div>
                <div style="position:relative; height:260px;">
                    <canvas id="chart-user-growth"></canvas>
                </div>
            </div>

            <!-- Events by Category -->
            <div class="admin-chart-card">
                <h3>Distribusi Event</h3>
                <div class="chart-subtitle">Berdasarkan kategori</div>
                <div style="position:relative; height:260px;">
                    <canvas id="chart-events-category"></canvas>
                </div>
            </div>

            <!-- Events by Status -->
            <div class="admin-chart-card">
                <h3>Status Event</h3>
                <div class="chart-subtitle">Distribusi status event saat ini</div>
                <div style="position:relative; height:260px;">
                    <canvas id="chart-events-status"></canvas>
                </div>
            </div>

            <!-- Top Events -->
            <div class="admin-chart-card">
                <h3>Top 5 Event</h3>
                <div class="chart-subtitle">Berdasarkan pendapatan tertinggi</div>
                <div id="top-events-list">
                    <div class="admin-loading" id="top-events-loading">
                        <div class="spinner"></div>
                        <p>Memuat...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="admin-section">
            <div class="admin-section-header">
                <h2>
                    <i data-lucide="receipt" class="w-5 h-5" style="color: var(--admin-accent);"></i>
                    Transaksi Terakhir
                </h2>
            </div>

            <div class="admin-loading" id="transactions-loading">
                <div class="spinner"></div>
                <p>Memuat data transaksi...</p>
            </div>

            <table class="admin-table" id="transactions-table" style="display: none;">
                <thead>
                    <tr>
                        <th>Kode Order</th>
                        <th>Pembeli</th>
                        <th>Event</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody id="transactions-body">
                    <!-- Rows inserted by JS -->
                </tbody>
            </table>

            <div class="admin-empty-state" id="transactions-empty" style="display: none;">
                <div class="empty-icon">
                    <i data-lucide="receipt" class="w-8 h-8"></i>
                </div>
                <h3>Belum Ada Transaksi</h3>
                <p>Belum ada transaksi yang tercatat.</p>
            </div>
        </div>
    </main>
</div>

<!-- Toast Container -->
<div id="admin-toast-container"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/admin-analytics.js') }}?v={{ time() }}"></script>
@endpush
