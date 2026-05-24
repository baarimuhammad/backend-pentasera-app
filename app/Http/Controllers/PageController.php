<?php

namespace App\Http\Controllers;

use App\Models\DetailOrder;
use App\Models\Event;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $events = $this->eventQuery()->get();

        $activeEvents = $events->where('event_status', 'published')->values();
        $endedEvents = $events->where('event_status', 'ended')->values();
        $topEvents = $activeEvents->take(3);

        return view('welcome', compact('activeEvents', 'endedEvents', 'topEvents'));
    }

    public function events()
    {
        $events = $this->eventQuery()
            ->where('event_status', 'published')
            ->get();

        $categories = $events
            ->pluck('kategori_event')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('events', compact('events', 'categories'));
    }

    public function order(Event $event)
    {
        $event->load(['organizer', 'tickets' => fn ($query) => $query->orderBy('harga')]);

        return view('order', [
            'event' => $event,
            'tickets' => $event->tickets,
        ]);
    }

    public function checkout(Request $request)
    {
        $eventId = $request->integer('id');

        $event = $this->eventQuery()
            ->when($eventId > 0, fn ($query) => $query->whereKey($eventId))
            ->firstOrFail();

        return view('checkout', [
            'checkoutEvent' => $this->checkoutEventPayload($event),
            'checkoutTickets' => $event->tickets->map(fn ($ticket) => [
                'id' => (string) $ticket->id,
                'type' => $ticket->kategori,
                'price' => $ticket->formatted_price,
                'rawPrice' => (int) $ticket->harga,
            ])->values(),
        ]);
    }

    public function myEvents()
    {
        $events = $this->eventQuery()->get();

        return view('my-events', [
            'activeEvents' => $events->where('event_status', 'published')->values(),
            'draftEvents' => $events->where('event_status', 'draft')->values(),
            'pastEvents' => $events->where('event_status', 'ended')->values(),
        ]);
    }

    public function manageAccess()
    {
        return view('manage-access', [
            'events' => $this->eventQuery()->get(),
        ]);
    }

    public function manageEvent(Event $event)
    {
        $event->load(['organizer', 'tickets' => fn ($query) => $query->orderBy('harga')]);

        return view('manage-event', [
            'event' => $event,
            'tickets' => $event->tickets,
            'stats' => $this->eventStats($event),
            'transactions' => $this->transactionsForEvent($event),
        ]);
    }

    public function eventReport(Event $event)
    {
        $event->load(['organizer', 'tickets' => fn ($query) => $query->orderBy('harga')]);

        return view('event-report', [
            'event' => $event,
            'tickets' => $event->tickets,
            'stats' => $this->eventStats($event),
        ]);
    }

    private function eventQuery()
    {
        return Event::with([
            'organizer',
            'tickets' => fn ($query) => $query->orderBy('harga'),
        ])->orderBy('event_datetime');
    }

    private function checkoutEventPayload(Event $event): array
    {
        return [
            'id' => (string) $event->id,
            'name' => $event->nama_event,
            'date' => optional($event->event_datetime)->format('d M Y') ?? '-',
            'venue' => $event->lokasi,
            'image' => $event->image_src,
        ];
    }

    private function eventStats(Event $event): array
    {
        $totalCapacity = $event->total_capacity;
        $sold = $event->sold_tickets;
        $revenue = $event->tickets->sum(fn ($ticket) => $ticket->sold_quantity * (float) $ticket->harga);

        return [
            'capacity' => $totalCapacity,
            'sold' => $sold,
            'remaining' => max(0, $totalCapacity - $sold),
            'occupancy' => $totalCapacity > 0 ? round(($sold / $totalCapacity) * 100, 1) : 0,
            'revenue' => $revenue,
            'revenue_formatted' => 'Rp ' . number_format($revenue, 0, ',', '.'),
        ];
    }

    private function transactionsForEvent(Event $event)
    {
        return DetailOrder::with(['order.user', 'ticket'])
            ->whereHas('ticket', fn ($query) => $query->where('event_id', $event->id))
            ->latest()
            ->limit(10)
            ->get();
    }
}
