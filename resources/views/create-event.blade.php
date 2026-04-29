@extends('layouts.app')
@section('title', 'Buat Event Baru - Pentasera')
@section('content')
<main class="max-w-5xl mx-auto py-12 px-4">
    <div class="mb-10">
        <h1 class="font-['Cinzel'] text-4xl font-bold text-dark mb-2">Buat Event Baru</h1>
        <p class="text-gray-600">Bagikan pesona budaya dan seni Anda kepada dunia dalam beberapa langkah mudah.</p>
    </div>

    <!-- Banner Upload -->
    <section class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-4">Banner Event*</label>
        <div class="border-2 border-dashed border-gray-200 rounded-xl p-12 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer group">
            <div class="w-12 h-12 rounded-full bg-gold-light/20 flex items-center justify-center text-gold mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide="image"></i>
            </div>
            <p class="text-sm text-gray-500 font-medium">Klik untuk unggah atau seret gambar ke sini</p>
            <p class="text-[10px] text-gray-400 mt-1">Rasio 16:9 recommended, Maks. 5MB (JPG, PNG)</p>
        </div>
    </section>

    <!-- Basic Info -->
    <section class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Event*</label>
                <input type="text" placeholder="Contoh: Pentas Tari Kecak Uluwatu" class="w-full bg-gray-50 border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih Kategori*</label>
                <select class="w-full bg-gray-50 border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none appearance-none">
                    <option>Seni Pertunjukan</option>
                    <option>Festival Budaya</option>
                    <option>Pameran Seni</option>
                    <option>Workshop</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Execution Details -->
    <section class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 mb-8">
        <h2 class="font-['Cinzel'] text-xl font-bold text-dark mb-6 pb-4 border-bottom border-gray-100">Detail Pelaksanaan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                    <i data-lucide="user" class="w-3 h-3 text-rust"></i> Penyelenggara
                </label>
                <input type="text" placeholder="Nama Komunitas/EO" class="w-full bg-gray-50 border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
            </div>
            <div>
                <label class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                    <i data-lucide="calendar" class="w-3 h-3 text-rust"></i> Tanggal & Waktu
                </label>
                <input type="datetime-local" class="w-full bg-gray-50 border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
            </div>
            <div>
                <label class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                    <i data-lucide="map-pin" class="w-3 h-3 text-rust"></i> Lokasi
                </label>
                <input type="text" placeholder="Nama Tempat/Gedung" class="w-full bg-gray-50 border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
            </div>
        </div>
    </section>

    <!-- Tabs Section -->
    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
        <div class="flex border-b border-gray-100">
            <button onclick="switchTab('tickets')" id="tab-tickets" class="flex-1 py-4 text-[11px] font-bold uppercase tracking-wider border-b-2 border-rust text-rust transition-all">Pengaturan Tiket & Kontak</button>
            <button onclick="switchTab('description')" id="tab-description" class="flex-1 py-4 text-[11px] font-bold uppercase tracking-wider border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-all">Deskripsi Event</button>
        </div>

        <!-- Tickets & Contact Tab -->
        <div id="content-tickets" class="p-8">
            <h3 class="font-bold text-lg mb-6">Pilih Kategori Tiket</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <!-- Paid Ticket Card -->
                <div onclick="openTicketModal('paid', event)" class="group relative bg-gray-50/50 border border-gray-100 rounded-[2rem] p-8 hover:bg-white hover:border-rust hover:shadow-2xl hover:shadow-rust/10 transition-all duration-500 cursor-pointer overflow-hidden">
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-rust group-hover:bg-rust group-hover:text-white transition-all duration-500">
                                <i data-lucide="banknote" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-ink mb-1">Berbayar</p>
                                <p class="text-xs text-gray-400">Jual tiket harga tetap</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:border-rust group-hover:text-rust transition-all">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rust/5 rounded-full blur-2xl group-hover:bg-rust/10 transition-all"></div>
                </div>

                <!-- Free Ticket Card -->
                <div onclick="openTicketModal('free', event)" class="group relative bg-gray-50/50 border border-gray-100 rounded-[2rem] p-8 hover:bg-white hover:border-rust hover:shadow-2xl hover:shadow-rust/10 transition-all duration-500 cursor-pointer overflow-hidden">
                    <div class="relative z-10 flex items-center justify-between">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-white rounded-2xl shadow-sm flex items-center justify-center text-rust group-hover:bg-rust group-hover:text-white transition-all duration-500">
                                <i data-lucide="gift" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-xl font-bold text-ink mb-1">Gratis</p>
                                <p class="text-xs text-gray-400">Tanpa biaya masuk</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 group-hover:border-rust group-hover:text-rust transition-all">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-rust/5 rounded-full blur-2xl group-hover:bg-rust/10 transition-all"></div>
                </div>
            </div>

            <!-- Added Tickets List -->
            <div id="added-tickets-container" class="hidden mb-12">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-8 h-8 bg-rust/10 rounded-lg flex items-center justify-center text-rust">
                        <i data-lucide="list" class="w-4 h-4"></i>
                    </div>
                    <h4 class="font-bold text-ink uppercase tracking-wider text-xs">Tiket Yang Ditambahkan</h4>
                </div>
                <div id="added-tickets-list" class="space-y-4"></div>
            </div>

            <!-- Informasi Kontak -->
            <div class="border-t border-gray-100 pt-12 mb-12">
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-2 h-2 bg-rust rounded-full"></div>
                    <h3 class="text-2xl font-bold text-ink tracking-tight">Informasi Kontak</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Nama Narahubung*</label>
                        <div class="relative group">
                            <input type="text" placeholder="Nama narahubung" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-4 px-1 text-base focus:border-rust outline-none transition-all font-bold text-ink placeholder:text-gray-300 placeholder:font-normal">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">Email Aktif*</label>
                        <div class="relative group">
                            <input type="email" placeholder="Email narahubung" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-4 px-1 text-base focus:border-rust outline-none transition-all font-bold text-ink placeholder:text-gray-300 placeholder:font-normal">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] ml-1">No. WhatsApp*</label>
                        <div class="relative group">
                            <input type="tel" placeholder="8123456789" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-4 px-1 text-base focus:border-rust outline-none transition-all font-bold text-ink placeholder:text-gray-300 placeholder:font-normal">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengaturan Tambahan -->
            <div class="border-t border-gray-100 pt-12">
                <div class="flex items-center gap-2 mb-8">
                    <div class="w-2 h-2 bg-rust rounded-full"></div>
                    <h3 class="text-2xl font-bold text-ink tracking-tight">Pengaturan Tambahan</h3>
                </div>
                <div class="space-y-8">
                    <div class="flex items-center justify-between p-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 group hover:bg-white hover:border-rust/20 transition-all">
                        <div>
                            <p class="text-base font-bold text-ink">Maks. Tiket / Transaksi</p>
                            <p class="text-xs text-gray-400 mt-1">Batasi jumlah tiket sekali checkout (Maksimal 5)</p>
                        </div>
                        <div class="flex items-center gap-4 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm">
                            <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-rust/10 hover:text-rust rounded-xl transition-all"><i data-lucide="minus" class="w-4 h-4"></i></button>
                            <span class="text-lg font-bold text-ink w-6 text-center">5</span>
                            <button class="w-10 h-10 flex items-center justify-center text-gray-400 hover:bg-rust/10 hover:text-rust rounded-xl transition-all"><i data-lucide="plus" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 group hover:bg-white hover:border-rust/20 transition-all">
                        <div>
                            <p class="text-base font-bold text-ink">Satu Email, Satu Transaksi</p>
                            <p class="text-xs text-gray-400 mt-1">Cegah pembelian berulang dengan email yang sama</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rust shadow-inner"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-6 bg-gray-50/50 rounded-[2rem] border border-gray-100 group hover:bg-white hover:border-rust/20 transition-all">
                        <div>
                            <p class="text-base font-bold text-ink">Satu Tiket, Satu Identitas</p>
                            <p class="text-xs text-gray-400 mt-1">Wajib mengisi identitas berbeda untuk setiap tiket</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rust shadow-inner"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Tab -->
        <div id="content-description" class="p-8 hidden">
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Deskripsi Event</h3>
                    <span class="text-[10px] text-gray-400 uppercase font-bold">0 Karakter</span>
                </div>
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 border-b border-gray-200 p-2 flex gap-2">
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="bold" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="italic" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="underline" class="w-4 h-4"></i></button>
                        <div class="w-px bg-gray-200 mx-1"></div>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="list" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="list-ordered" class="w-4 h-4"></i></button>
                        <div class="w-px bg-gray-200 mx-1"></div>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="link" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="image" class="w-4 h-4"></i></button>
                    </div>
                    <textarea class="w-full h-64 p-4 outline-none resize-none text-sm" placeholder="Ceritakan detail event Anda..."></textarea>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <span class="text-gray-400 text-sm">−</span> Syarat & Ketentuan
                    </h3>
                    <span class="text-[10px] text-gray-400 uppercase font-bold">0 Karakter</span>
                </div>
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 border-b border-gray-200 p-2 flex gap-2">
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="bold" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="italic" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="underline" class="w-4 h-4"></i></button>
                        <div class="w-px bg-gray-200 mx-1"></div>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="list" class="w-4 h-4"></i></button>
                        <button class="p-1.5 hover:bg-white rounded text-gray-600"><i data-lucide="list-ordered" class="w-4 h-4"></i></button>
                    </div>
                    <textarea class="w-full h-48 p-4 outline-none resize-none text-sm" placeholder="Sebutkan aturan atau syarat bagi pengunjung..."></textarea>
                </div>
            </div>
        </div>
    </section>

    <!-- Action Buttons -->
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center justify-end gap-4">
        <button class="px-8 py-3 rounded-full border border-rust text-rust font-bold text-sm hover:bg-rust/5 transition-all">Simpan Draf</button>
        <button class="px-8 py-3 rounded-full bg-rust text-white font-bold text-sm hover:bg-rust-deep transition-all shadow-lg shadow-rust/20">Buat Event Sekarang</button>
    </div>
</main>

<!-- Ticket Modal -->
<div id="ticket-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-ink/60 backdrop-blur-md" onclick="closeTicketModal()"></div>
    <div class="relative w-full max-w-2xl bg-white rounded-[2.5rem] overflow-hidden shadow-2xl animate-fade-in-up flex flex-col max-h-[85vh] border border-gray-100">
        <div class="flex items-center justify-between p-6 border-b border-gray-50 shrink-0">
            <div class="flex-1 flex gap-8 px-4">
                <button onclick="switchModalTab('detail')" id="modal-tab-detail" class="py-4 text-[10px] font-bold uppercase tracking-[0.2em] border-b-2 border-rust text-rust transition-all">Detail Tiket</button>
                <button onclick="switchModalTab('sales')" id="modal-tab-sales" class="py-4 text-[10px] font-bold uppercase tracking-[0.2em] border-b-2 border-transparent text-gray-400 transition-all opacity-40 cursor-not-allowed" disabled title="Isi detail tiket terlebih dahulu">Tanggal Penjualan</button>
            </div>
            <button onclick="closeTicketModal()" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 rounded-full text-gray-400 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="overflow-y-auto flex-1 custom-scrollbar">
            <!-- Modal Content: Detail -->
            <div id="modal-content-detail" class="p-10">
                <div class="space-y-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Tiket <span class="text-rust">*</span></label>
                        <div class="relative group">
                            <input type="text" placeholder="Contoh: Early Bird" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-3 px-1 text-base focus:border-rust outline-none transition-all font-bold text-ink placeholder:text-gray-300 placeholder:font-normal">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jumlah Tiket <span class="text-rust">*</span></label>
                        <div class="relative group">
                            <input type="number" value="0" class="w-full bg-gray-50/50 border-b-2 border-gray-100 py-3 px-1 text-base focus:border-rust outline-none transition-all font-bold text-ink">
                            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-rust group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                        <div class="flex justify-end mt-1">
                            <span class="text-[10px] text-gray-400 font-medium">0/100 <i data-lucide="info" class="inline w-3 h-3 ml-1"></i></span>
                        </div>
                    </div>
                    <div id="price-field">
                        <label class="block text-xs font-bold text-gray-500 mb-2">Harga <span class="text-red-500">*</span></label>
                        <input type="text" value="Rp" class="w-full border-b border-gray-200 py-2 text-sm focus:border-rust outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">Deskripsi Tiket</label>
                        <textarea class="w-full border-b border-gray-200 py-2 text-sm focus:border-rust outline-none transition-all h-20 resize-none"></textarea>
                        <div class="flex justify-end mt-1">
                            <span class="text-[10px] text-gray-400">0/140</span>
                        </div>
                    </div>
                </div>
                <button onclick="switchModalTab('sales')" class="w-full bg-rust text-white font-bold py-4 rounded-lg mt-8 hover:bg-rust-deep transition-all uppercase tracking-wider text-sm opacity-50 cursor-not-allowed" disabled>Selanjutnya</button>
            </div>

            <!-- Modal Content: Sales Date -->
            <div id="modal-content-sales" class="p-8 hidden">
                <div class="space-y-8">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-4">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <input type="date" class="flex-1 border-b border-gray-200 py-2 text-sm focus:border-rust outline-none">
                            <input type="time" class="w-32 border border-gray-200 rounded px-3 py-2 text-sm text-gray-600 focus:border-rust outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-4">Tanggal Berakhir <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <input type="date" class="flex-1 border-b border-gray-200 py-2 text-sm focus:border-rust outline-none">
                            <input type="time" class="w-32 border border-gray-200 rounded px-3 py-2 text-sm text-gray-600 focus:border-rust outline-none transition-all">
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400">Tanggal maksimal penjualan bergantung pada tanggal berakhirnya event.</p>
                </div>

                <div class="flex gap-4 mt-12">
                    <button onclick="switchModalTab('detail')" class="w-14 h-14 rounded-lg bg-rust text-white flex items-center justify-center hover:bg-rust-deep transition-all shrink-0">
                        <i data-lucide="chevron-left"></i>
                    </button>
                    <button id="btn-create-ticket" class="flex-1 bg-rust/20 text-white font-bold py-4 rounded-lg transition-all uppercase tracking-wider text-sm cursor-not-allowed" disabled>Buat Tiket Berbayar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/create-event.js') }}"></script>
@endpush
