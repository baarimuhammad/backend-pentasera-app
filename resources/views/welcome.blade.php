@extends('layouts.app')

@section('title', 'Pentasera | Tradisi dalam Genggaman')

@section('content')
<div class="batik-bg pb-10 min-h-screen w-full">
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
                    @forelse($activeEvents as $event)
                    <div onclick="location.href='{{ url('/order/' . $event->id) }}'" class="event-card">
                        <img class="event-card-img" src="{{ $event->image_src }}" alt="{{ $event->nama_event }}">
                        <div class="event-card-body">
                            <div class="event-name">{{ $event->nama_event }}</div>
                            <div class="event-date">{{ $event->event_datetime?->isoFormat('DD MMM YYYY') }}</div>
                            <div class="event-venue">{{ $event->lokasi }}</div>
                            <div class="event-price">{{ $event->formatted_lowest_ticket_price }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-500 w-full">Belum ada event aktif saat ini.</div>
                    @endforelse
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
                <div onclick="location.href='{{ url('/order/' . $event->id) }}'" class="top-event-item">
                    <span class="top-rank">{{ $index + 1 }}</span>
                    <div class="top-event-card">
                        <img class="top-event-img" src="{{ $event->image_src }}" alt="Event {{ $index + 1 }}">
                        <div class="top-event-overlay">
                            <span class="top-event-tag">Featured Event</span>
                            <h3 class="top-event-name">{{ Str::limit($event->nama_event, 25) }}</h3>
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
                    @forelse($endedEvents as $event)
                    <div class="event-card">
                        <div class="ended-badge-wrap">
                            <img class="event-card-img" src="{{ $event->image_src }}" alt="{{ $event->nama_event }}">
                            <div class="ended-badge">Berakhir</div>
                        </div>
                        <div class="event-card-body">
                            <div class="event-name">{{ $event->nama_event }}</div>
                            <div class="event-date">{{ $event->event_datetime?->isoFormat('DD MMM YYYY') }}</div>
                            <div class="event-venue">{{ $event->lokasi }}</div>
                            <div class="event-price">{{ $event->formatted_lowest_ticket_price }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-500 w-full">Belum ada event yang berakhir.</div>
                    @endforelse
                </div>
                <button class="carousel-btn next" onclick="scrollCarousel(this, 1)">&#8250;</button>
            </div>
        </div>
    </div>
</div>
@endsection
