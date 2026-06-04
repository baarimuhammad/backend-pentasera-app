{{-- Modal: Semua Transaksi --}}
<div id="modal-transaksi" class="modal-overlay">
    <div class="modal-container !max-w-5xl">
        <div class="modal-header !p-8 border-b border-gray-100">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rust/10 rounded-2xl flex items-center justify-center text-rust">
                    <i data-lucide="receipt-text" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-ink text-xl">Riwayat Transaksi</h3>
                    <p class="text-xs text-gray-400">Kelola dan pantau semua penjualan tiket Anda</p>
                </div>
            </div>
            <button onclick="closeModal('modal-transaksi')" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-ink transition-all">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="modal-body !p-0">
            <div class="p-8 bg-gray-50/50 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <div class="search-input-wrapper !w-full md:!w-80">
                        <i data-lucide="search"></i>
                        <input type="text" class="search-input" placeholder="Cari nama, email, atau ID...">
                    </div>
                    <select class="bg-white border border-gray-200 px-4 py-2.5 rounded-xl text-xs font-bold text-ink outline-none focus:border-rust transition-colors">
                        <option>Semua Status</option>
                        <option>Berhasil</option>
                        <option>Pending</option>
                        <option>Gagal</option>
                    </select>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button class="flex-1 md:flex-none bg-white border border-gray-200 px-6 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        Filter
                    </button>
                    <button class="flex-1 md:flex-none bg-ink text-white px-6 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 hover:bg-black transition-colors shadow-lg shadow-ink/10">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        Export CSV
                    </button>
                </div>
            </div>
            <div class="max-h-[55vh] overflow-y-auto px-8 pb-8">
                <table class="w-full text-left border-collapse">
                    <thead class="sticky top-0 bg-white z-10">
                        <tr class="border-b border-gray-100">
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID Transaksi</th>
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pembeli</th>
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori</th>
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Qty</th>
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Bayar</th>
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu</th>
                            <th class="py-5 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 text-[10px] font-mono text-gray-400">#TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-4">
                                <div class="text-xs font-bold text-ink">{{ $transaction->order?->user?->nama ?? 'Pembeli' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $transaction->order?->user?->email ?? '-' }}</div>
                            </td>
                            <td class="py-4">
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-600">{{ $transaction->ticket?->kategori ?? '-' }}</span>
                            </td>
                            <td class="py-4 text-xs text-center font-bold text-ink">{{ $transaction->jumlah }}</td>
                            <td class="py-4 text-xs font-bold text-ink">Rp {{ number_format((float) $transaction->subtotal, 0, ',', '.') }}</td>
                            <td class="py-4">
                                <div class="text-[10px] text-ink font-medium">{{ $transaction->created_at?->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $transaction->created_at?->format('H:i') }} WIB</div>
                            </td>
                            <td class="py-4"><span class="status-badge active">{{ strtoupper($transaction->order?->status_order ?? '-') }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-4 text-xs text-gray-400">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer !p-8 border-t border-gray-100">
            <div class="flex items-center justify-between w-full">
                <p class="text-[10px] text-gray-400">Menampilkan {{ $transactions->count() }} transaksi</p>
                <button onclick="closeModal('modal-transaksi')" class="bg-gray-100 text-ink px-8 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Tambah/Edit Tiket --}}
<div id="modal-tiket" class="modal-overlay">
    <div class="modal-container !max-w-lg !p-0 overflow-hidden flex flex-col max-h-[90vh] !rounded-[2rem]">
        <div class="flex items-center justify-between p-4 border-b border-gray-50 bg-white shrink-0">
            <div class="flex-1 flex gap-6 px-2">
                <button onclick="switchModalTab('detail')" id="modal-tab-detail" class="py-3 text-[10px] font-bold uppercase tracking-[0.2em] border-b-2 border-rust text-rust transition-all">Detail Tiket</button>
                <button onclick="switchModalTab('sales')" id="modal-tab-sales" class="py-3 text-[10px] font-bold uppercase tracking-[0.2em] border-b-2 border-transparent text-gray-400 transition-all">Tanggal Penjualan</button>
            </div>
            <button onclick="closeModal('modal-tiket')" class="w-8 h-8 flex items-center justify-center hover:bg-gray-50 rounded-full text-gray-400 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 custom-scrollbar bg-white">
            {{-- Modal Content: Detail --}}
            <div id="modal-content-detail" class="p-8">
                <div class="space-y-5">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Tiket <span class="text-rust">*</span></label>
                        <div class="relative group">
                            <input id="ticket-name" type="text" placeholder="Contoh: Early Bird" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-2.5 px-1 text-sm focus:border-rust outline-none transition-all font-bold text-ink placeholder:text-gray-300 placeholder:font-normal">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jumlah Tiket <span class="text-rust">*</span></label>
                        <div class="relative group">
                            <input id="ticket-qty" type="number" value="0" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-2.5 px-1 text-sm focus:border-rust outline-none transition-all font-bold text-ink">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                        <div class="flex justify-end mt-1">
                            <span class="text-[10px] text-gray-400 font-medium">0/100 <i data-lucide="info" class="inline w-3 h-3 ml-1"></i></span>
                        </div>
                    </div>
                    <div id="price-field" class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Harga <span class="text-rust">*</span></label>
                        <div class="relative group">
                            <input id="ticket-price" type="text" value="Rp" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-2.5 px-1 text-sm focus:border-rust outline-none transition-all font-bold text-ink">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Deskripsi Tiket</label>
                        <div class="relative group">
                            <textarea id="ticket-desc" placeholder="Berikan info tambahan tentang tiket ini..." class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-2.5 px-1 text-sm focus:border-rust outline-none transition-all font-bold text-ink h-20 resize-none placeholder:text-gray-300 placeholder:font-normal"></textarea>
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                        <div class="flex justify-end mt-1">
                            <span class="text-[10px] text-gray-400 font-medium">0/140</span>
                        </div>
                    </div>
                </div>
                <button id="btn-next-tab" onclick="switchModalTab('sales')" class="w-full bg-rust text-white font-bold py-3.5 rounded-lg mt-6 hover:bg-rust-deep transition-all uppercase tracking-wider text-sm opacity-50 cursor-not-allowed" disabled>Selanjutnya</button>
            </div>

            {{-- Modal Content: Sales Date --}}
            <div id="modal-content-sales" class="p-8 hidden">
                <div class="space-y-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Mulai <span class="text-rust">*</span></label>
                        <div class="flex gap-3">
                            <div class="flex-1 relative group">
                                <input id="ticket-start-date" type="date" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-2.5 px-1 text-sm focus:border-rust outline-none transition-all font-bold text-ink">
                                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                            </div>
                            <div class="w-32 relative group/select">
                                <div class="w-full bg-gray-50/50 border border-gray-100 rounded-xl px-4 py-2.5 flex items-center justify-between text-ink font-bold text-sm cursor-pointer hover:bg-white transition-all">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        <span>09:00</span>
                                    </div>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-300 group-hover/select:text-rust transition-colors"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Berakhir <span class="text-rust">*</span></label>
                        <div class="flex gap-3">
                            <div class="flex-1 relative group">
                                <input id="ticket-end-date" type="date" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-2.5 px-1 text-sm focus:border-rust outline-none transition-all font-bold text-ink">
                                <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                            </div>
                            <div class="w-32 relative group/select">
                                <div class="w-full bg-gray-50/50 border border-gray-100 rounded-xl px-4 py-2.5 flex items-center justify-between text-ink font-bold text-sm cursor-pointer hover:bg-white transition-all">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        <span>18:00</span>
                                    </div>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-300 group-hover/select:text-rust transition-colors"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 bg-rust/5 rounded-[1.25rem] border border-rust/10 mt-6">
                        <i data-lucide="info" class="w-5 h-5 text-rust shrink-0 mt-0.5"></i>
                        <p class="text-[11px] leading-relaxed text-rust/70 font-medium tracking-tight">Tanggal maksimal penjualan tiket bergantung pada tanggal berakhirnya event yang telah diatur sebelumnya. Pastikan jadwal penjualan selaras dengan waktu pelaksanaan event.</p>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button onclick="switchModalTab('detail')" class="w-12 h-12 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center hover:bg-rust hover:text-white transition-all shrink-0">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <button id="btn-create-ticket" onclick="saveTicket()" class="flex-1 bg-rust text-white font-black py-3 rounded-[1rem] transition-all uppercase tracking-[0.2em] text-xs shadow-xl shadow-rust/40">Simpan Tiket</button>
                </div>
            </div>
        </div>
    </div>
</div>
