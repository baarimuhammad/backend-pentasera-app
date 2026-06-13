<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analitik Pentasera — {{ now()->format('d M Y') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@700&display=swap" rel="stylesheet">
    <style>
        /* ─── Base ─── */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --print-accent: #B84C2B;
            --print-accent-deep: #8B2E12;
            --print-gold: #C8922A;
            --print-text: #2C1A0E;
            --print-text-muted: #7A7A7A;
            --print-text-dim: #A0A0A0;
            --print-border: #E8C285;
            --print-border-light: rgba(232, 194, 133, 0.3);
            --print-bg-subtle: #F9F6F2;
            --print-cream: #F5EDE0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--print-text);
            background: #FDFCFB;
            line-height: 1.6;
            padding: 0;
        }

        /* ─── Print Toolbar (not printed) ─── */
        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: linear-gradient(135deg, var(--print-accent), var(--print-accent-deep));
            color: #fff;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(44, 26, 14, 0.15);
        }

        .print-toolbar-info {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
        }

        .print-toolbar-info svg {
            width: 20px;
            height: 20px;
        }

        .print-toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 10px;
            border: none;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-print-primary {
            background: rgba(255, 255, 255, 0.95);
            color: var(--print-accent-deep);
        }

        .btn-print-primary:hover {
            background: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-print-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-print-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* ─── Report Content ─── */
        .report-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 32px 60px;
        }

        /* ─── Report Header ─── */
        .report-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 28px;
            border-bottom: 3px solid var(--print-border);
        }

        .report-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--print-accent-deep);
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .report-subtitle {
            font-size: 16px;
            font-weight: 600;
            color: var(--print-gold);
            margin-bottom: 8px;
        }

        .report-date {
            font-size: 12px;
            color: var(--print-text-muted);
        }

        /* ─── Section ─── */
        .report-section {
            margin-bottom: 36px;
        }

        .report-section-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--print-accent);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--print-border-light);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .report-section-title::before {
            content: '';
            width: 4px;
            height: 18px;
            background: linear-gradient(to bottom, var(--print-accent), var(--print-gold));
            border-radius: 2px;
        }

        /* ─── Stats Grid ─── */
        .report-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 8px;
        }

        .report-stat-card {
            background: var(--print-bg-subtle);
            border: 1px solid var(--print-border-light);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .report-stat-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--print-text-dim);
            margin-bottom: 8px;
        }

        .report-stat-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--print-text);
        }

        .report-stat-value.revenue {
            color: var(--print-gold);
            font-size: 20px;
        }

        /* ─── Tables ─── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .report-table thead th {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--print-text-dim);
            padding: 12px 16px;
            text-align: left;
            border-bottom: 2px solid var(--print-border-light);
            background: var(--print-bg-subtle);
        }

        .report-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--print-border-light);
            color: var(--print-text);
            vertical-align: middle;
        }

        .report-table tbody tr:last-child td {
            border-bottom: none;
        }

        .report-table .text-right {
            text-align: right;
        }

        .report-table .text-center {
            text-align: center;
        }

        .report-table .font-mono {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 12px;
        }

        .report-table .font-bold {
            font-weight: 700;
        }

        .report-table .text-accent {
            color: var(--print-accent);
        }

        .report-table .text-gold {
            color: var(--print-gold);
        }

        /* ─── Rank Badge ─── */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 11px;
        }

        .rank-badge.gold { background: rgba(200, 146, 42, 0.15); color: var(--print-gold); }
        .rank-badge.silver { background: rgba(148, 163, 184, 0.15); color: #64748b; }
        .rank-badge.bronze { background: rgba(180, 83, 9, 0.1); color: #b45309; }
        .rank-badge.default { background: var(--print-bg-subtle); color: var(--print-text-muted); }

        /* ─── Report Footer ─── */
        .report-footer {
            margin-top: 48px;
            padding-top: 20px;
            border-top: 2px solid var(--print-border-light);
            text-align: center;
            font-size: 11px;
            color: var(--print-text-dim);
        }

        /* ══════════════════════════════════════════
           PRINT STYLES
           ══════════════════════════════════════════ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 16mm 14mm;
            }

            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .print-toolbar {
                display: none !important;
            }

            .report-container {
                max-width: none;
                padding: 0;
                margin: 0;
            }

            .report-header {
                margin-bottom: 24px;
                padding-bottom: 16px;
            }

            .report-section {
                page-break-inside: avoid;
                margin-bottom: 24px;
            }

            .report-table {
                page-break-inside: auto;
            }

            .report-table thead {
                display: table-header-group;
            }

            .report-table tbody tr {
                page-break-inside: avoid;
            }

            .report-stats-grid {
                gap: 8px;
            }

            .report-stat-card {
                padding: 12px;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <!-- Print Toolbar (hidden when printing) -->
    <div class="print-toolbar">
        <div class="print-toolbar-info">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            Pratinjau Laporan — Siap untuk dicetak atau disimpan sebagai PDF
        </div>
        <div class="print-toolbar-actions">
            <button class="btn-print btn-print-secondary" onclick="window.close()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Tutup
            </button>
            <button class="btn-print btn-print-primary" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Report Content -->
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="report-logo">PENTASERA</div>
            <div class="report-subtitle">Laporan Analitik & Ringkasan</div>
            <div class="report-date">Digenerate pada: {{ now()->format('d M Y, H:i') }} WIB</div>
        </div>

        <!-- Overview Stats -->
        <div class="report-section">
            <div class="report-section-title">Ringkasan Overview</div>
            <div class="report-stats-grid">
                <div class="report-stat-card">
                    <div class="report-stat-label">Total Pengguna</div>
                    <div class="report-stat-value">{{ number_format($totalUsers) }}</div>
                </div>
                <div class="report-stat-card">
                    <div class="report-stat-label">Total Event</div>
                    <div class="report-stat-value">{{ number_format($totalEvents) }}</div>
                </div>
                <div class="report-stat-card">
                    <div class="report-stat-label">Total Transaksi</div>
                    <div class="report-stat-value">{{ number_format($totalTransactions) }}</div>
                </div>
                <div class="report-stat-card">
                    <div class="report-stat-label">Total Pendapatan</div>
                    <div class="report-stat-value revenue">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- Revenue Trend -->
        <div class="report-section">
            <div class="report-section-title">Tren Pendapatan (12 Bulan Terakhir)</div>
            @if($revenueTrend->count() > 0)
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th class="text-right">Pendapatan</th>
                            <th class="text-center">Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $months = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
                                       '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
                        @endphp
                        @foreach($revenueTrend as $row)
                            @php
                                $parts = explode('-', $row->month);
                                $label = ($months[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
                            @endphp
                            <tr>
                                <td class="font-bold">{{ $label }}</td>
                                <td class="text-right text-gold font-bold">Rp {{ number_format($row->revenue, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $row->transactions }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--print-text-dim); font-size: 13px; text-align: center; padding: 24px;">Belum ada data pendapatan.</p>
            @endif
        </div>

        <!-- Top 5 Events -->
        <div class="report-section">
            <div class="report-section-title">Top 5 Event Berdasarkan Pendapatan</div>
            @if($topEvents->count() > 0)
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama Event</th>
                            <th>Organizer</th>
                            <th class="text-center">Tiket Terjual</th>
                            <th class="text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topEvents as $i => $ev)
                            @php
                                $rankClass = match($i) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => 'default' };
                            @endphp
                            <tr>
                                <td><span class="rank-badge {{ $rankClass }}">{{ $i + 1 }}</span></td>
                                <td class="font-bold">{{ $ev['nama_event'] }}</td>
                                <td>{{ $ev['organizer'] }}</td>
                                <td class="text-center">{{ number_format($ev['tickets_sold']) }}</td>
                                <td class="text-right text-accent font-bold">Rp {{ number_format($ev['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--print-text-dim); font-size: 13px; text-align: center; padding: 24px;">Belum ada data event.</p>
            @endif
        </div>

        <!-- Recent Transactions -->
        <div class="report-section">
            <div class="report-section-title">Transaksi Terakhir</div>
            @if($recentTransactions->count() > 0)
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Kode Order</th>
                            <th>Pembeli</th>
                            <th>Event</th>
                            <th class="text-right">Total</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $t)
                            <tr>
                                <td class="font-mono text-accent">{{ $t['order_code'] }}</td>
                                <td class="font-bold">{{ $t['buyer_name'] }}</td>
                                <td>{{ $t['event'] }}</td>
                                <td class="text-right font-bold">Rp {{ number_format($t['total'], 0, ',', '.') }}</td>
                                <td>{{ $t['date'] ? \Carbon\Carbon::parse($t['date'])->format('d M Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--print-text-dim); font-size: 13px; text-align: center; padding: 24px;">Belum ada data transaksi.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="report-footer">
            <p>Laporan ini digenerate secara otomatis oleh sistem Pentasera</p>
            <p style="margin-top:4px;">{{ now()->format('d M Y, H:i') }} WIB — Hanya untuk penggunaan internal</p>
        </div>
    </div>

    <script>
        // Auto-trigger print dialog after a short delay for rendering
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>
