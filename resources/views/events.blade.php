@extends('layouts.app')

@section('title', 'Events | Pentasara')

@section('content')
<div class="batik-bg min-h-screen">
    <div class="flex-1 max-w-7xl mx-auto w-full px-6 py-8 animate-fade-in">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filter -->
        <aside class="w-full md:w-80 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gold/10">
                <h3 class="font-display font-bold text-ink mb-6 flex items-center gap-2 text-lg">
                    <i data-lucide="filter" class="w-5 h-5 text-rust"></i> Jelajah Event
                </h3>
                <div class="space-y-6">
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Cari Lokasi</label>
                        <div class="relative">
                            <input type="text" id="lokasi-filter" placeholder="Semua Lokasi" class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all">
                            <i data-lucide="map-pin" class="absolute right-4 top-1/2 -translate-y-1/2 text-gold w-4 h-4"></i>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Kategori</label>
                        <div class="relative">
                            <select id="kategori-filter" class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all appearance-none cursor-pointer">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 text-gold w-4 h-4 pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Pilih Tanggal</label>
                        <input type="date" id="date-filter" class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all text-ink">
                        <button id="reset-filter" class="mt-2 text-[10px] font-bold text-rust uppercase tracking-widest hover:text-rust-deep transition-colors flex items-center gap-1">
                            <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Grid Event -->
        <div class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="events-container">
                @forelse($events as $ev)
                <div onclick="location.href='{{ url('/order/'.$ev->id) }}'" data-date="{{ $ev->event_datetime?->toDateString() }}" data-kategori="{{ $ev->kategori_event }}" data-venue="{{ strtolower($ev->lokasi) }}" class="event-item bg-white rounded-xl overflow-hidden shadow-md hover-lift transition-all cursor-pointer border border-gold/10 group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $ev->image_src }}" alt="{{ $ev->nama_event }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-5">
                        <h3 class="font-display font-bold text-base text-ink mb-3 line-clamp-1 group-hover:text-rust transition-colors">{{ $ev->nama_event }}</h3>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-rust"></i> {{ $ev->event_datetime?->format('d M Y') }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gold"></i> {{ $ev->lokasi }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-gold/10">
                            <div class="font-bold text-rust-deep text-lg">{{ $ev->formatted_lowest_ticket_price }}</div>
                            <div class="text-[10px] font-bold text-gold uppercase tracking-widest">Detail →</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center text-gray-500">
                    Belum ada event published di database.
                </div>
                @endforelse
            </div>

            <!-- No Events Message -->
            <div id="no-events" class="hidden py-20 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-cream/50 text-gold mb-4">
                    <i data-lucide="calendar-x" class="w-8 h-8"></i>
                </div>
                <h3 class="font-display font-bold text-xl text-ink mb-2">Tidak Ada Event</h3>
                <p class="text-gray-500">Maaf, tidak ada event yang ditemukan dengan filter tersebut.</p>
            </div>

            <!-- Pagination -->
            <div class="mt-16 flex items-center justify-center gap-3">
                <button class="w-10 h-10 rounded-full border border-gold/20 flex items-center justify-center text-gold hover:bg-gold hover:text-white transition-all shadow-sm">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <button class="w-10 h-10 rounded-full text-sm font-bold transition-all shadow-sm bg-gold text-white">1</button>
                <button class="w-10 h-10 rounded-full border border-gold/20 flex items-center justify-center text-gold hover:bg-gold hover:text-white transition-all shadow-sm">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const dateFilter = document.getElementById('date-filter');
    const kategoriFilter = document.getElementById('kategori-filter');
    const lokasiFilter = document.getElementById('lokasi-filter');
    const resetBtn = document.getElementById('reset-filter');
    const eventItems = document.querySelectorAll('.event-item');
    const noEventsMsg = document.getElementById('no-events');

    function filterEvents() {
        const selectedDate = dateFilter.value;
        const selectedKategori = kategoriFilter.value;
        const selectedLokasi = lokasiFilter.value.toLowerCase().trim();
        let found = 0;

        eventItems.forEach(item => {
            const eventDate = item.getAttribute('data-date');
            const eventKategori = item.getAttribute('data-kategori');
            const eventVenue = item.getAttribute('data-venue');

            let show = true;
            if (selectedDate && eventDate !== selectedDate) show = false;
            if (selectedKategori && eventKategori !== selectedKategori) show = false;
            if (selectedLokasi && !eventVenue.includes(selectedLokasi)) show = false;

            if (show) {
                item.classList.remove('hidden');
                found++;
            } else {
                item.classList.add('hidden');
            }
        });
        noEventsMsg.classList.toggle('hidden', found > 0);
    }

    if (dateFilter) dateFilter.addEventListener('change', filterEvents);
    if (kategoriFilter) kategoriFilter.addEventListener('change', filterEvents);
    if (lokasiFilter) lokasiFilter.addEventListener('input', filterEvents);
    if (resetBtn) resetBtn.addEventListener('click', () => {
        dateFilter.value = '';
        kategoriFilter.value = '';
        lokasiFilter.value = '';
        filterEvents();
    });
</script>
@endpush
