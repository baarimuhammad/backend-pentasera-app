@extends('layouts.app')

@section('title', 'Beli Tiket | Pentasara')

@section('content')
<main id="app" class="flex-1 max-w-7xl mx-auto w-full px-6 py-8 animate-fade-in">
    @php
        $ALL_EVENTS = [
            1 => ['id' => 1, 'name' => "Witness the Fire of Uluwatu", 'date' => "03 Apr 2026", 'time' => "18:00 WIB", 'venue' => "Pura Luhur Uluwatu, Bali", 'price' => "Rp150.000", 'image' => "assets/kecak.png", 'organizer' => "Uluwatu Cultural Foundation", 'description' => "Pertunjukan tari Kecak yang legendaris di atas tebing Uluwatu dengan latar belakang matahari terbenam yang memukau."],
            2 => ['id' => 2, 'name' => "Stories Carved in Tradition", 'date' => "08 Apr 2026", 'time' => "20:00 WIB", 'venue' => "Keraton Yogyakarta", 'price' => "Rp50.000", 'image' => "assets/wayang.png", 'organizer' => "Keraton Heritage", 'description' => "Pagelaran wayang kulit klasik yang membawakan kisah epik Ramayana oleh dalang ternama."],
            3 => ['id' => 3, 'name' => "Feel the Rhythm of Minangkabau", 'date' => "11 Apr 2026", 'time' => "16:00 WIB", 'venue' => "Padang Cultural Center", 'price' => "Rp80.000", 'image' => "assets/taripiring.png", 'organizer' => "Minang Arts", 'description' => "Pertunjukan tari tradisional dari Sumatera Barat yang menampilkan ketangkasan para penari dalam memainkan piring."],
            4 => ['id' => 4, 'name' => "Harmony of Javanese Heritage", 'date' => "12 Apr 2026", 'time' => "19:00 WIB", 'venue' => "Jatim Expo Surabaya", 'price' => "Rp100.000", 'image' => "assets/gamelan.png", 'organizer' => "Jatim Expo Management", 'description' => "Pertunjukan gamelan Jawa yang memadukan harmoni musik tradisional dengan sentuhan modern."],
            5 => ['id' => 5, 'name' => "Experience the Magic of Bali", 'date' => "15 Apr 2026", 'time' => "17:00 WIB", 'venue' => "GWK Cultural Park, Bali", 'price' => "Rp125.000", 'image' => "assets/TariBarong.png", 'organizer' => "Bali Cultural Park", 'description' => "Pertunjukan Tari Barong yang menampilkan kisah pertarungan antara kebaikan dan kejahatan dalam mitologi Bali."],
            6 => ['id' => 6, 'name' => "Rhythm in Perfect Harmony", 'date' => "20 Apr 2026", 'time' => "19:30 WIB", 'venue' => "Istora Senayan, Jakarta", 'price' => "Rp200.000", 'image' => "assets/tarisaman.png", 'organizer' => "Nusantara Budaya Foundation", 'description' => "Tari Saman dari Aceh yang menampilkan sinkronisasi gerakan sempurna oleh puluhan penari."],
            7 => ['id' => 7, 'name' => "Grace in Every Movement", 'date' => "25 Apr 2026", 'time' => "18:00 WIB", 'venue' => "Saung Angklung Udjo, Bandung", 'price' => "Rp75.000", 'image' => "assets/jaipong.png", 'organizer' => "Nusantara Budaya Foundation", 'description' => "Pertunjukan Tari Jaipong yang memadukan keanggunan dan energi dalam setiap gerakan."],
            8 => ['id' => 8, 'name' => "The Spirit of Batak Heritage", 'date' => "30 Apr 2026", 'time' => "16:00 WIB", 'venue' => "Danau Toba, Sumatera Utara", 'price' => "Rp50.000", 'image' => "assets/tortor.png", 'organizer' => "Nusantara Budaya Foundation", 'description' => "Tari Tor-Tor khas Batak yang menampilkan kekuatan spiritual dan budaya Batak Toba."],
            9 => ['id' => 9, 'name' => "Elegance of the Royal Court", 'date' => "05 Mei 2026", 'time' => "18:30 WIB", 'venue' => "Puri Saren Agung, Ubud", 'price' => "Rp120.000", 'image' => "assets/legong.png", 'organizer' => "Bali Cultural Park", 'description' => "Tari Legong yang menampilkan keanggunan tari istana Bali dengan gerakan halus dan ekspresif."],
            10 => ['id' => 10, 'name' => "Stories Carved in Tradition II", 'date' => "10 Mei 2026", 'time' => "19:00 WIB", 'venue' => "Gedung Kesenian Jakarta", 'price' => "Rp90.000", 'image' => "assets/wayanggolek.png", 'organizer' => "Keraton Heritage", 'description' => "Pertunjukan wayang golek Sunda yang membawakan cerita pewayangan dengan boneka kayu tiga dimensi."],
        ];

        $TICKETS = [
            ['id' => 't1', 'type' => 'REGULAR', 'price' => 'Rp80.000', 'rawPrice' => 80000, 'status' => 'Available', 'description' => 'Tiket masuk standar untuk satu orang.'],
            ['id' => 't2', 'type' => 'VIP', 'price' => 'Rp150.000', 'rawPrice' => 150000, 'status' => 'Available', 'description' => 'Akses baris depan, merchandise, dan snack box.'],
            ['id' => 't3', 'type' => 'VVIP', 'price' => 'Rp250.000', 'rawPrice' => 250000, 'status' => 'Available', 'description' => 'Meet & Greet, baris terdepan, merchandise premium, dan lounge.'],
            ['id' => 't4', 'type' => 'EARLY BIRD', 'price' => 'Rp60.000', 'rawPrice' => 60000, 'status' => 'Limited', 'description' => 'Promo terbatas untuk pembelian awal.'],
        ];

        $id = $id ?? 1;
        $event = $ALL_EVENTS[$id] ?? $ALL_EVENTS[1];
    @endphp

    <div class="flex flex-col lg:flex-row gap-8" id="order-content">
        <div class="flex-1 space-y-8">
            <div class="rounded-2xl overflow-hidden shadow-2xl relative h-[400px]">
                <img src="{{ asset($event['image']) }}" alt="{{ $event['name'] }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-8 left-8">
                    <h1 class="font-display font-bold text-3xl text-white drop-shadow-lg uppercase tracking-widest">{{ $event['name'] }}</h1>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gold/10 overflow-hidden">
                <div class="flex border-b border-gold/10">
                    <button onclick="setTab('tiket')" id="tab-tiket-btn" class="flex-1 py-4 text-sm font-bold text-rust border-b-2 border-rust bg-cream/30">Pilih Tiket</button>
                    <button onclick="setTab('deskripsi')" id="tab-deskripsi-btn" class="flex-1 py-4 text-sm font-bold text-gray-400">Deskripsi Event</button>
                </div>
                
                <div id="content-tiket" class="p-8 space-y-6">
                    @foreach($TICKETS as $t)
                    <div class="ticket-item border border-gold/20 rounded-xl p-6 flex justify-between items-center transition-all">
                        <div class="flex-1 pr-4">
                            <div class="flex items-center gap-2 mb-1">
                                <h4 class="font-bold text-ink">{{ $t['type'] }}</h4>
                                @if($t['status'] === 'Limited')
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-[10px] font-bold rounded uppercase">Limited</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">{{ $t['description'] }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <div class="font-bold text-rust-deep text-lg">{{ $t['price'] }}</div>
                            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                <button onclick="updateQty('{{ $t['id'] }}', -1, {{ $t['rawPrice'] }})" class="qty-btn w-8 h-8 flex items-center justify-center rounded-md hover:bg-white hover:shadow-sm transition-all text-gray-600 font-bold">-</button>
                                <span id="qty-{{ $t['id'] }}" class="w-10 text-center font-bold text-ink">0</span>
                                <button onclick="updateQty('{{ $t['id'] }}', 1, {{ $t['rawPrice'] }})" class="qty-btn w-8 h-8 flex items-center justify-center rounded-md bg-rust text-white hover:bg-rust-deep shadow-sm transition-all font-bold">+</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="content-deskripsi" class="p-8 space-y-6 hidden">
                    <div class="space-y-4">
                        <h3 class="font-display font-bold text-xl text-ink">Tentang Event</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $event['description'] }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4">
                            <div class="bg-cream/20 p-4 rounded-lg border border-gold/10">
                                <span class="text-[10px] font-bold text-gold uppercase tracking-widest block mb-1">Penyelenggara</span>
                                <span class="text-sm font-bold text-ink">{{ $event['organizer'] }}</span>
                            </div>
                            <div class="bg-cream/20 p-4 rounded-lg border border-gold/10">
                                <span class="text-[10px] font-bold text-gold uppercase tracking-widest block mb-1">Waktu</span>
                                <span class="text-sm font-bold text-ink">{{ $event['time'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="w-full lg:w-80 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gold/10 space-y-4">
                <h3 class="font-bold border-b pb-2 text-sm uppercase tracking-wider text-gold">Ringkasan Pesanan</h3>
                <div id="summary-items" class="py-4 text-center">
                    <p class="text-xs text-gray-400 italic">Belum ada tiket dipilih</p>
                </div>
                
                <div id="summary-total" class="hidden space-y-3">
                    <div id="summary-list" class="space-y-2"></div>
                    <div class="flex justify-between text-xs pt-2 border-t border-dashed">
                        <span class="text-gray-500 italic">Biaya Layanan (10%)</span>
                        <span id="service-fee" class="font-medium">Rp 0</span>
                    </div>
                    <div class="border-t pt-3 flex justify-between items-center">
                        <span class="text-sm font-bold">Total Bayar</span>
                        <span id="grand-total" class="font-black text-rust-deep text-lg">Rp 0</span>
                    </div>
                </div>
                
                <button onclick="goToCheckout()" id="checkout-btn" class="w-full py-3 rounded-xl font-bold text-sm transition-all bg-gray-200 text-gray-400 cursor-not-allowed" disabled>
                    Lanjut Pembayaran
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6 border border-gold/10 space-y-4">
                <h3 class="font-bold border-b pb-2 text-sm uppercase tracking-wider text-gold">Informasi Event</h3>
                <div class="space-y-3">
                    <div class="flex gap-3 items-start">
                        <i data-lucide="calendar" class="w-4 h-4 text-rust mt-0.5"></i>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Tanggal</p>
                            <p class="text-xs font-medium">{{ $event['date'] }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 items-start">
                        <i data-lucide="clock" class="w-4 h-4 text-rust mt-0.5"></i>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Waktu</p>
                            <p class="text-xs font-medium">{{ $event['time'] }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 items-start">
                        <i data-lucide="map-pin" class="w-4 h-4 text-rust mt-0.5"></i>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Lokasi</p>
                            <p class="text-xs font-medium">{{ $event['venue'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</main>
@endsection

@push('scripts')
<script>
    let quantities = {};
    let prices = {};
    let ticketNames = {
        't1': 'REGULAR',
        't2': 'VIP',
        't3': 'VVIP',
        't4': 'EARLY BIRD'
    };

    function setTab(tab) {
        const isTiket = tab === 'tiket';
        document.getElementById('content-tiket').classList.toggle('hidden', !isTiket);
        document.getElementById('content-deskripsi').classList.toggle('hidden', isTiket);
        
        document.getElementById('tab-tiket-btn').className = isTiket ? 'flex-1 py-4 text-sm font-bold text-rust border-b-2 border-rust bg-cream/30' : 'flex-1 py-4 text-sm font-bold text-gray-400';
        document.getElementById('tab-deskripsi-btn').className = !isTiket ? 'flex-1 py-4 text-sm font-bold text-rust border-b-2 border-rust bg-cream/30' : 'flex-1 py-4 text-sm font-bold text-gray-400';
    }

    function updateQty(tid, delta, price) {
        if (!quantities[tid]) quantities[tid] = 0;
        quantities[tid] = Math.max(0, quantities[tid] + delta);
        prices[tid] = price;
        
        document.getElementById(`qty-${tid}`).innerText = quantities[tid];
        updateSummary();
    }

    function updateSummary() {
        const summaryItems = document.getElementById('summary-items');
        const summaryTotal = document.getElementById('summary-total');
        const summaryList = document.getElementById('summary-list');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        let subtotal = 0;
        let selectedItems = [];

        for (let tid in quantities) {
            if (quantities[tid] > 0) {
                let itemTotal = quantities[tid] * prices[tid];
                subtotal += itemTotal;
                selectedItems.push({
                    name: ticketNames[tid],
                    qty: quantities[tid],
                    total: itemTotal
                });
            }
        }

        if (selectedItems.length > 0) {
            summaryItems.classList.add('hidden');
            summaryTotal.classList.remove('hidden');
            
            summaryList.innerHTML = selectedItems.map(item => `
                <div class="flex justify-between text-xs">
                    <span class="text-gray-600">${item.qty}x ${item.name}</span>
                    <span class="font-medium">Rp ${item.total.toLocaleString('id-ID')}</span>
                </div>
            `).join('');

            const serviceFee = Math.round(subtotal * 0.1);
            const total = subtotal + serviceFee;

            document.getElementById('service-fee').innerText = `Rp ${serviceFee.toLocaleString('id-ID')}`;
            document.getElementById('grand-total').innerText = `Rp ${total.toLocaleString('id-ID')}`;
            
            checkoutBtn.disabled = false;
            checkoutBtn.className = "w-full py-3 rounded-xl font-bold text-sm transition-all bg-rust text-white hover:bg-rust-deep shadow-lg shadow-rust/20";
        } else {
            summaryItems.classList.remove('hidden');
            summaryTotal.classList.add('hidden');
            checkoutBtn.disabled = true;
            checkoutBtn.className = "w-full py-3 rounded-xl font-bold text-sm transition-all bg-gray-200 text-gray-400 cursor-not-allowed";
        }
    }

    function goToCheckout() {
        const ticketParts = [];
        for (let tid in quantities) {
            if (quantities[tid] > 0) ticketParts.push(`${tid}:${quantities[tid]}`);
        }
        const eventId = {{ $event['id'] }};
        window.location.href = `{{ url('/checkout') }}?id=${eventId}&tickets=${ticketParts.join(',')}`;
    }
</script>
@endpush
