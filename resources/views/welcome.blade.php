@extends('layouts.app')

@section('title', 'Pentasara | Tradisi dalam Genggaman')

@section('content')
<div class="batik-bg pb-10 min-h-screen w-full">
    @php
        $activeEvents = [
            ['id' => 1, 'name' => 'Witness the Fire of Uluwatu', 'img' => 'kecak.png', 'date' => '03 Apr 2026', 'venue' => 'Pura Luhur Uluwatu, Bali', 'price' => 'Rp150.000'],
            ['id' => 2, 'name' => 'Stories Carved in Tradition', 'img' => 'wayang.png', 'date' => '08 Apr 2026', 'venue' => 'Keraton Yogyakarta', 'price' => 'Rp50.000'],
            ['id' => 3, 'name' => 'Feel the Rhythm of Minangkabau', 'img' => 'taripiring.png', 'date' => '11 Apr 2026', 'venue' => 'Padang Cultural Center', 'price' => 'Rp80.000'],
            ['id' => 4, 'name' => 'Harmony of Javanese Heritage', 'img' => 'gamelan.png', 'date' => '12 Apr 2026', 'venue' => 'Jatim Expo Surabaya', 'price' => 'Rp100.000'],
            ['id' => 5, 'name' => 'Experience the Magic of Bali', 'img' => 'TariBarong.png', 'date' => '15 Apr 2026', 'venue' => 'GWK Cultural Park, Bali', 'price' => 'Rp125.000'],
            ['id' => 6, 'name' => 'Rhythm in Perfect Harmony', 'img' => 'tarisaman.png', 'date' => '20 Apr 2026', 'venue' => 'Istora Senayan, Jakarta', 'price' => 'Rp200.000'],
            ['id' => 7, 'name' => 'Grace in Every Movement', 'img' => 'jaipong.png', 'date' => '25 Apr 2026', 'venue' => 'Saung Angklung Udjo, Bandung', 'price' => 'Rp75.000'],
            ['id' => 8, 'name' => 'The Spirit of Batak Heritage', 'img' => 'tortor.png', 'date' => '30 Apr 2026', 'venue' => 'Danau Toba, Sumatera Utara', 'price' => 'Rp50.000'],
            ['id' => 9, 'name' => 'Elegance of the Royal Court', 'img' => 'legong.png', 'date' => '05 Mei 2026', 'venue' => 'Puri Saren Agung, Ubud', 'price' => 'Rp120.000'],
            ['id' => 10, 'name' => 'Stories Carved in Tradition II', 'img' => 'wayanggolek.png', 'date' => '10 Mei 2026', 'venue' => 'Gedung Kesenian Jakarta', 'price' => 'Rp90.000'],
        ];

        $endedEvents = [
            ['name' => 'Reog Ponorogo Festival', 'img' => 'TariBarong.png', 'date' => '18 Mar 2026', 'venue' => 'Alun-Alun Ponorogo', 'price' => 'Rp25.000'],
            ['name' => 'Tari Pendet Massal', 'img' => 'TariLegong.png', 'date' => '10 Mar 2026', 'venue' => 'Ubud Palace, Bali', 'price' => 'Rp100.000'],
            ['name' => 'Sasando Music Night', 'img' => 'gamelan.png', 'date' => '05 Mar 2026', 'venue' => 'Kupang Arts Center, NTT', 'price' => 'Rp60.000'],
            ['name' => 'Minang Arts Festival', 'img' => 'TariPiring.png', 'date' => '01 Mar 2026', 'venue' => 'Bukittinggi, Sumbar', 'price' => 'Rp40.000'],
            ['name' => 'Gamelan Heritage Night', 'img' => 'WayangGolek.png', 'date' => '25 Feb 2026', 'venue' => 'Solo, Jawa Tengah', 'price' => 'Rp30.000'],
        ];

        $topEvents = array_slice($activeEvents, 0, 3);
    @endphp

    <div class="flex-1 max-w-7xl mx-auto w-full">
        <!-- Hero Section -->
        <div class="hero">
            <img src="{{ asset('assets/hero-banner.jpg') }}" alt="Pertunjukan Seni Tradisional Indonesia">
            <div class="hero-overlay">
                <div class="hero-brand">
                    <div class="hero-tagline">Experience the Magic of Traditional Performances!</div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Section -->
        <div class="section">
            <div class="section-title">Event Terdekat</div>
            <div class="carousel-wrapper">
                <button class="carousel-btn prev" onclick="scrollCarousel(this, -1)">&#8249;</button>
                <div class="events-grid" id="carousel-terdekat">
                    @foreach($activeEvents as $event)
                    <div onclick="location.href='{{ url('/order/' . $event['id']) }}'" class="event-card">
                        <img class="event-card-img" src="{{ asset('assets/' . $event['img']) }}" alt="{{ $event['name'] }}">
                        <div class="event-card-body">
                            <div class="event-name">{{ $event['name'] }}</div>
                            <div class="event-date">{{ $event['date'] }}</div>
                            <div class="event-venue">{{ $event['venue'] }}</div>
                            <div class="event-price">{{ $event['price'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-btn next" onclick="scrollCarousel(this, 1)">&#8250;</button>
            </div>
        </div>
    </div>

    <!-- Top Events Section (Full Width) -->
    <div class="top-event-section">
        <div class="max-w-7xl mx-auto px-10">
            <div class="section-title">TOP EVENT</div>
            <div class="top-event-grid">
                @foreach($topEvents as $index => $event)
                <div onclick="location.href='{{ url('/order/' . $event['id']) }}'" class="top-event-item">
                    <span class="top-rank">{{ $index + 1 }}</span>
                    <div class="top-event-card">
                        <img class="top-event-img" src="{{ asset('assets/' . $event['img']) }}" alt="Event {{ $index + 1 }}">
                        <div class="top-event-overlay">
                            <span class="top-event-tag">Featured Event</span>
                            <h3 class="top-event-name">{{ Str::limit($event['name'], 25) }}</h3>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex-1 max-w-7xl mx-auto w-full">
        <!-- Ended Events Section -->
        <div class="section">
            <div class="section-title">Event Berakhir</div>
            <div class="carousel-wrapper">
                <button class="carousel-btn prev" onclick="scrollCarousel(this, -1)">&#8249;</button>
                <div class="events-grid" id="carousel-berakhir">
                    @foreach($endedEvents as $event)
                    <div class="event-card">
                        <div class="ended-badge-wrap">
                            @if(str_starts_with($event['img'], 'http'))
                                <img class="event-card-img" src="{{ $event['img'] }}" alt="{{ $event['name'] }}">
                            @else
                                <img class="event-card-img" src="{{ asset('assets/' . $event['img']) }}" alt="{{ $event['name'] }}">
                            @endif
                            <div class="ended-badge">Berakhir</div>
                        </div>
                        <div class="event-card-body">
                            <div class="event-name">{{ $event['name'] }}</div>
                            <div class="event-date">{{ $event['date'] }}</div>
                            <div class="event-venue">{{ $event['venue'] }}</div>
                            <div class="event-price">{{ $event['price'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button class="carousel-btn next" onclick="scrollCarousel(this, 1)">&#8250;</button>
            </div>
        </div>
    </div>
</div>
@endsection
