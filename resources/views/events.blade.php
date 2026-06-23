@extends('layouts.app')

@section('title', 'Events | Pentasera')

@section('content')
<div class="batik-bg min-h-screen">
    <div class="flex-1 max-w-7xl mx-auto w-full px-6 py-8 animate-fade-in">

    {{-- Flash error message (e.g. redirected from ended event) --}}
    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-5 py-4 flex items-center gap-3 text-red-700">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filter -->
        <aside class="w-full md:w-80 space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gold/10">
                <h3 class="font-display font-bold text-ink mb-6 flex items-center gap-2 text-lg">
                    <i data-lucide="filter" class="w-5 h-5 text-rust"></i> Jelajah Event
                </h3>
                <form method="GET" action="{{ url('/events') }}" id="filter-form">
                    <div class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Cari Nama Event</label>
                            <div class="relative">
                                <input type="text" name="q" id="q-filter" value="{{ request('q') }}" placeholder="Ketik nama event..." class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all">
                                <i data-lucide="search" class="absolute right-4 top-1/2 -translate-y-1/2 text-gold w-4 h-4"></i>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Cari Lokasi</label>
                            <div class="relative">
                                <input type="text" name="lokasi" id="lokasi-filter" value="{{ request('lokasi') }}" placeholder="Semua Lokasi" class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all">
                                <i data-lucide="map-pin" class="absolute right-4 top-1/2 -translate-y-1/2 text-gold w-4 h-4"></i>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Kategori</label>
                            <div class="relative">
                                <select name="kategori" id="kategori-filter" class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all appearance-none cursor-pointer">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 text-gold w-4 h-4 pointer-events-none"></i>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 block">Pilih Tanggal</label>
                            <input type="date" name="date" id="date-filter" value="{{ request('date') }}" class="w-full bg-cream/30 border border-gold/20 rounded-lg py-3 px-4 text-sm focus:outline-none focus:border-rust/50 transition-all text-ink">
                            <div class="flex gap-2 mt-2">
                                <button type="submit" class="text-[10px] font-bold text-white bg-rust px-4 py-2 rounded-lg uppercase tracking-widest hover:bg-rust-deep transition-colors flex items-center gap-1">
                                    <i data-lucide="search" class="w-3 h-3"></i> Cari
                                </button>
                                <a href="{{ url('/events') }}" class="text-[10px] font-bold text-rust uppercase tracking-widest hover:text-rust-deep transition-colors flex items-center gap-1 px-4 py-2">
                                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Grid Event -->
        <div class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="events-container">
                @forelse($events as $ev)
                <div onclick="location.href='{{ url('/order/'.$ev->id) }}'" class="event-item bg-white rounded-xl overflow-hidden shadow-md hover-lift transition-all cursor-pointer border border-gold/10 group">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ $ev->image_src }}" alt="{{ $ev->nama_event }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="p-5">
                        <h3 class="font-display font-bold text-base text-ink mb-3 line-clamp-1 group-hover:text-rust transition-colors">{{ $ev->nama_event }}</h3>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-rust"></i> {{ \Carbon\Carbon::parse($ev->event_datetime)->isoFormat('DD MMM YYYY') }}
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
                <div class="col-span-3 py-20 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-cream/50 text-gold mb-4">
                        <i data-lucide="calendar-x" class="w-8 h-8"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-ink mb-2">Tidak Ada Event</h3>
                    <p class="text-gray-500">Maaf, tidak ada event yang ditemukan dengan filter tersebut.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($events->hasPages())
            <div class="mt-16">
                {{ $events->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
    </div>
</div>
@endsection
