<!-- Tab Content: Penjualan -->
<div id="manage-penjualan" class="event-tab-content p-12 pb-32">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <h2 class="text-2xl font-black text-ink mb-3 tracking-tight">Statistik Penjualan</h2>
            <p class="text-base text-gray-400 font-medium">Ringkasan performa finansial Pentasara Anda.</p>
        </div>
        <div class="flex items-center gap-4">
            <button class="bg-white border border-gray-100 px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] text-ink flex items-center gap-3 hover:bg-gray-50 transition-all group shadow-sm">
                Bulan Ini
                <i data-lucide="calendar" class="w-4 h-4 text-gray-400 group-hover:text-rust transition-colors"></i>
            </button>
            <button class="bg-rust text-white px-10 py-4 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] flex items-center gap-3 shadow-2xl shadow-rust/30 hover:bg-rust-deep hover:-translate-y-0.5 transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export Report
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-24">
        <div class="stat-card !border-none !shadow-2xl !shadow-gray-200/20 !p-10 transform hover:scale-[1.02] transition-all">
            <div class="stat-header !mb-8">
                <div class="stat-label !text-[10px] !font-black !tracking-[0.2em]">
                    <i data-lucide="trending-up" class="w-5 h-5 !text-green-500"></i>
                    Total Pendapatan
                </div>
                <span class="text-[10px] font-black text-green-500 bg-green-50 px-3 py-1 rounded-full">+12.5%</span>
            </div>
            <div class="stat-value !text-4xl !font-black !tracking-tight !mb-2" id="report-total-revenue">{{ $stats['revenue_formatted'] }}</div>
            <div class="stat-unit !font-bold">IDR (Rupiah)</div>
        </div>
        <div class="stat-card !border-none !shadow-2xl !shadow-gray-200/20 !p-10 transform hover:scale-[1.02] transition-all">
            <div class="stat-header !mb-8">
                <div class="stat-label !text-[10px] !font-black !tracking-[0.2em]">
                    <i data-lucide="ticket" class="w-5 h-5 !text-rust"></i>
                    Tiket Terjual
                </div>
                <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-full">+8.2%</span>
            </div>
            <div class="stat-value !text-4xl !font-black !tracking-tight !mb-2" id="report-total-tickets">{{ number_format($stats['sold'], 0, ',', '.') }}</div>
            <div class="stat-unit !font-bold">Tiket Dipesan</div>
        </div>
        <div class="stat-card !border-none !shadow-2xl !shadow-gray-200/20 !p-10 transform hover:scale-[1.02] transition-all">
            <div class="stat-header !mb-8">
                <div class="stat-label !text-[10px] !font-black !tracking-[0.2em]">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 !text-amber-500"></i>
                    Rata-rata Harian
                </div>
                <span class="text-[10px] font-black text-gray-400 bg-gray-50 px-3 py-1 rounded-full whitespace-nowrap">STABIL</span>
            </div>
            <div class="stat-value !text-4xl !font-black !tracking-tight !mb-2" id="report-daily-avg">Rp {{ number_format($stats['revenue'] / 30, 0, ',', '.') }}</div>
            <div class="stat-unit !font-bold">Penjualan / Hari</div>
        </div>
    </div>

    <!-- Chart & Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mb-24">
        <div class="lg:col-span-2 bg-white p-12 rounded-[2rem] border border-gray-100 shadow-2xl shadow-gray-200/10 relative overflow-hidden">
            <div class="flex items-center justify-between mb-20 px-10 py-4">
                <div>
                    <h3 class="text-xl font-black text-ink mb-2 tracking-tight">Grafik Penjualan</h3>
                    <p class="text-base text-gray-400 font-medium">Tren transaksi 30 hari terakhir</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="w-4 h-4 bg-rust rounded-full shadow-lg shadow-rust/40"></span>
                    <span class="text-[13px] font-black text-ink uppercase tracking-[0.2em]">Penjualan</span>
                </div>
            </div>
            <div class="h-80 w-full relative">
                <div id="chart-container" class="absolute inset-0 w-full h-full"></div>
            </div>
            <div class="flex justify-between items-center mt-10 text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">
                <span>01 JAN</span><span>07 JAN</span><span>14 JAN</span><span>21 JAN</span><span>28 JAN</span><span>30 JAN</span>
            </div>
        </div>

        <!-- Ticket Distribution -->
        <div class="bg-[#F6F5F2] p-12 rounded-[2rem] border border-gray-100 shadow-2xl shadow-gray-200/10 flex flex-col">
            <h3 class="text-2xl font-black text-ink mb-16 tracking-tight px-6">Distribusi Tiket</h3>
            <div class="space-y-16 flex-1 px-6">
                @forelse($tickets as $ticket)
                @php
                    $occupancy = $ticket->kuota > 0 ? round(($ticket->sold_quantity / $ticket->kuota) * 100) : 0;
                @endphp
                <div class="group">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-sm font-black text-ink uppercase tracking-wider">{{ $ticket->kategori }}</span>
                        <span class="text-lg font-black text-rust">{{ $occupancy }}%</span>
                    </div>
                    <div class="w-full bg-ink/5 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-rust h-full rounded-full shadow-xl shadow-rust/30 transition-all group-hover:scale-x-105 origin-left" style="width: {{ $occupancy }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">Belum ada tiket.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-[2rem] border border-gray-100 shadow-2xl shadow-gray-200/10 overflow-hidden">
        <div class="px-24 py-16 border-b border-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-ink mb-2 tracking-tight">Transaksi Terbaru</h3>
                <p class="text-base text-gray-400 font-medium">Daftar transaksi masuk real-time</p>
            </div>
            <button onclick="openModal('modal-transaksi')" class="text-rust font-black text-[13px] uppercase tracking-[0.2em] hover:translate-x-3 transition-transform inline-flex items-center gap-4 py-2 px-4 rounded-xl hover:bg-rust/5">
                Lihat Semua
                <i data-lucide="arrow-right" class="w-6 h-6"></i>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table id="dash-transaction-table" class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-24 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nama Pembeli</th>
                        <th class="px-24 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tipe Tiket</th>
                        <th class="px-24 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-24 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jumlah</th>
                        <th class="px-24 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50/50">
                    @forelse($transactions as $transaction)
                    @php
                        $buyer = $transaction->order?->user;
                        $initials = collect(explode(' ', $buyer?->nama ?? 'NA'))->map(fn ($part) => substr($part, 0, 1))->take(2)->implode('');
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-24 py-8">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 rounded-full bg-rust/10 text-rust font-black text-xs flex items-center justify-center border-2 border-white shadow-lg">{{ $initials }}</div>
                                <div>
                                    <div class="text-sm font-black text-ink">{{ $buyer?->nama ?? 'Pembeli' }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium">{{ $buyer?->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-24 py-8"><span class="px-4 py-1.5 bg-rust/10 text-rust text-[9px] font-black rounded-lg uppercase tracking-widest border border-rust/10">{{ $transaction->ticket?->kategori ?? '-' }}</span></td>
                        <td class="px-24 py-8">
                            <div class="flex items-center gap-3 text-green-600 text-[11px] font-black uppercase tracking-wider">
                                <div class="w-2 h-2 bg-green-500 rounded-full shadow-[0_0_12px_rgba(34,197,94,0.4)]"></div>
                                {{ $transaction->order?->status_order ?? '-' }}
                            </div>
                        </td>
                        <td class="px-24 py-8 text-sm font-black text-ink">Rp {{ number_format((float) $transaction->subtotal, 0, ',', '.') }}</td>
                        <td class="px-24 py-8 text-[11px] text-gray-400 font-bold uppercase tracking-widest">{{ $transaction->created_at?->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-24 py-8 text-sm text-gray-400">Belum ada transaksi untuk event ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
