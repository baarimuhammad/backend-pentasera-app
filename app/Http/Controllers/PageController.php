<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\DetailOrder;

class PageController extends Controller
{
    /**
     * Home page — published (active + ended) events, top 3 active events.
     */
    public function home()
    {
        $now = now();

        $events = Event::where('event_status', 'published')
            ->with(['organizer', 'tickets'])
            ->orderBy('event_datetime', 'desc')
            ->get();

        $activeEvents = $events->filter(fn($e) => $e->event_datetime >= $now);
        $endedEvents  = $events->filter(fn($e) => $e->event_datetime < $now);

        // Top events: sorted by most tickets sold (best-selling)
        $topEvents = Event::where('event_status', 'published')
            ->where('event_datetime', '>=', $now)
            ->with(['organizer', 'tickets'])
            ->get()
            ->map(function ($event) {
                $ticketIds = $event->tickets->pluck('id');
                $event->total_sold = (int) DetailOrder::whereIn('ticket_id', $ticketIds)
                    ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
                    ->sum('jumlah');
                return $event;
            })
            ->sortByDesc('total_sold')
            ->take(3)
            ->values();

        return view('welcome', compact('activeEvents', 'endedEvents', 'topEvents'));
    }

    /**
     * Dashboard page — just return the view (data loads via API).
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Create Event page — return the form view.
     */
    public function createEvent()
    {
        return view('create-event');
    }

    /**
     * Events listing page — published events + unique categories.
     */
    public function events(Request $request)
    {
        // Get categories from ALL published events (before filtering)
        $allPublished = Event::where('event_status', 'published')->get();
        $categories = $allPublished->pluck('kategori_event')->unique()->filter()->sort()->values();

        // Build query with filters — only show upcoming events (not yet ended)
        $query = Event::where('event_status', 'published')
            ->where('event_datetime', '>=', now())
            ->with(['organizer', 'tickets'])
            ->orderBy('event_datetime', 'asc');

        if ($request->filled('kategori')) {
            $query->where('kategori_event', $request->kategori);
        }
        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('event_datetime', $request->date);
        }

        $events = $query->paginate(12);

        return view('events', compact('events', 'categories'));
    }

    /**
     * Order page — event detail with organizer & tickets sorted by price.
     */
    public function order(Event $event)
    {
        // Block access if event has already ended
        if ($event->event_datetime < now()) {
            return redirect()->route('events.page')
                ->with('error', 'Event ini sudah berakhir dan tidak bisa memesan tiket.');
        }

        $event->load(['organizer', 'tickets' => function ($q) {
            $q->orderBy('harga', 'asc');
        }]);

        return view('order', [
            'event' => $event,
            'tickets' => $event->tickets,
        ]);
    }

    /**
     * Checkout page — retrieve event from query param ?id=
     */
    public function checkout(Request $request)
    {
        $checkoutEvent = Event::with('tickets')->findOrFail($request->query('id'));

        // Block checkout if event has already ended
        if ($checkoutEvent->event_datetime < now()) {
            return redirect()->route('events.page')
                ->with('error', 'Event ini sudah berakhir dan tidak bisa melakukan checkout.');
        }

        return view('checkout', [
            'checkoutEvent' => $this->checkoutEventPayload($checkoutEvent),
            'checkoutTickets' => $checkoutEvent->tickets->map(fn ($ticket) => [
                'id' => (string) $ticket->id,
                'type' => $ticket->kategori,
                'price' => $ticket->formatted_price ?? ('Rp ' . number_format($ticket->harga, 0, ',', '.')),
                'rawPrice' => (int) $ticket->harga,
                'kategori' => $ticket->kategori,
                'harga' => $ticket->harga,
            ])->values(),
        ]);
    }

    /**
     * Payment page — data comes from sessionStorage on the client side.
     */
    public function payment()
    {
        return view('payment');
    }

    /**
     * Manage Event page — event detail + tickets + stats + last 10 paid orders.
     */
    public function manageEvent(Event $event)
    {
        $event->load(['organizer', 'tickets']);

        $stats = $this->calculateStats($event);

        // Tickets for the distribution chart in penjualan tab
        $tickets = $event->tickets;
        $ticketIds = $tickets->pluck('id');

        // Recent orders for the JS window.__recentOrders
        $recentOrders = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', function ($q) use ($ticketIds) {
                $q->whereIn('ticket_id', $ticketIds);
            })
            ->with(['user', 'detailOrders.ticket'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Transactions as DetailOrder items (for the penjualan partial & modals)
        // The template accesses $transaction->order->user, $transaction->ticket, $transaction->subtotal
        $transactions = DetailOrder::whereIn('ticket_id', $ticketIds)
            ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
            ->with(['order.user', 'ticket'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // Daily sales data for the chart (last 30 days)
        $dailySales = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(tanggal_order) as date, COUNT(*) as orders, SUM(total_harga) as revenue')
            ->groupByRaw('DATE(tanggal_order)')
            ->orderBy('date')
            ->get();

        // Fill in missing days with zero values for a smooth chart
        $chartData = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $existing = $dailySales->firstWhere('date', $date);
            $chartData->push([
                'date' => $date,
                'orders' => $existing ? (int) $existing->orders : 0,
                'revenue' => $existing ? (float) $existing->revenue : 0,
            ]);
        }

        return view('manage-event', compact('event', 'stats', 'recentOrders', 'tickets', 'transactions', 'chartData'));
    }

    /**
     * Event Report page — event detail + tickets + stats + daily sales + recent transactions.
     */
    public function eventReport(Event $event)
    {
        $event->load(['organizer', 'tickets']);

        $stats = $this->calculateStats($event);

        $ticketIds = $event->tickets->pluck('id');

        // Tickets breakdown
        $ticketsBreakdown = $event->tickets->map(function ($ticket) {
            $sold = DetailOrder::where('ticket_id', $ticket->id)
                ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
                ->sum('jumlah');

            $revenue = $sold * $ticket->harga;
            $occupancy = $ticket->kuota > 0
                ? number_format(($sold / $ticket->kuota) * 100, 1)
                : '0.0';

            return [
                'kategori'  => $ticket->kategori,
                'harga'     => $ticket->harga,
                'harga_formatted' => 'Rp ' . number_format($ticket->harga, 0, ',', '.'),
                'kuota'     => $ticket->kuota,
                'terjual'   => (int) $sold,
                'sisa'      => $ticket->sisa_kuota,
                'revenue'   => (int) $revenue,
                'revenue_formatted' => 'Rp ' . number_format($revenue, 0, ',', '.'),
                'occupancy' => $occupancy,
            ];
        });

        // Daily sales (last 30 days)
        $dailySalesRaw = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(tanggal_order) as date, COUNT(*) as orders, SUM(total_harga) as revenue')
            ->groupByRaw('DATE(tanggal_order)')
            ->orderBy('date')
            ->get();

        // Fill in missing days with zero values for a smooth chart
        $dailySales = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $existing = $dailySalesRaw->firstWhere('date', $date);
            $dailySales->push([
                'date' => $date,
                'orders' => $existing ? (int) $existing->orders : 0,
                'revenue' => $existing ? (float) $existing->revenue : 0,
            ]);
        }

        // Recent transactions (last 20 paid orders)
        $recentTransactions = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->with(['user', 'detailOrders.ticket'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($order) {
                $ticketNames = $order->detailOrders->map(fn($d) => $d->ticket->kategori ?? '-')->implode(', ');
                $totalQty = $order->detailOrders->sum('jumlah');

                return [
                    'order_code'  => $order->order_code ?? '-',
                    'buyer_name'  => $order->user->name ?? '-',
                    'buyer_email' => $order->user->email ?? '-',
                    'tickets'     => $ticketNames,
                    'qty'         => $totalQty,
                    'total'       => $order->total_harga,
                    'total_formatted' => 'Rp ' . number_format($order->total_harga, 0, ',', '.'),
                    'date'        => $order->tanggal_order,
                ];
            });

        return view('event-report', compact('event', 'stats', 'ticketsBreakdown', 'dailySales', 'recentTransactions'));
    }


    /**
     * My Events page — data loads via API (token-based auth).
     */
    public function myEvents()
    {
        return view('my-events');
    }

    /**
     * My Tickets page — buyer's e-tickets.
     */
    public function myTickets()
    {
        return view('my-tickets');
    }

    /**
     * Profile page — user profile / organizer info.
     */
    public function profile()
    {
        return view('profile');
    }

    // ─── Private Helpers ─────────────────────────────────────────────

    /**
     * Calculate event stats: capacity, sold, remaining, occupancy, revenue.
     */
    private function calculateStats(Event $event): array
    {
        $capacity  = $event->tickets->sum('kuota');
        $ticketIds = $event->tickets->pluck('id');

        // Calculate sold from actual paid orders (accurate source of truth)
        $sold = (int) DetailOrder::whereIn('ticket_id', $ticketIds)
            ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
            ->sum('jumlah');

        $remaining = $capacity - $sold;
        $occupancy = $capacity > 0
            ? number_format(($sold / $capacity) * 100, 1) . '%'
            : '0%';

        // Revenue from paid orders that reference this event's tickets
        $revenue = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', function ($q) use ($ticketIds) {
                $q->whereIn('ticket_id', $ticketIds);
            })
            ->sum('total_harga');

        $revenueFormatted = 'Rp ' . number_format($revenue, 0, ',', '.');

        return compact('capacity', 'sold', 'remaining', 'occupancy', 'revenue', 'revenueFormatted')
            + ['revenue_formatted' => $revenueFormatted];
    }

    /**
     * Build checkout event payload for blade view.
     */
    private function checkoutEventPayload(Event $event): array
    {
        return [
            'id' => (string) $event->id,
            'name' => $event->nama_event,
            'nama_event' => $event->nama_event,
            'date' => optional($event->event_datetime)->format('d M Y') ?? '-',
            'venue' => $event->lokasi,
            'lokasi' => $event->lokasi,
            'image' => $event->image_src,
            'image_url' => $event->image_url,
            'event_datetime' => $event->event_datetime,
        ];
    }
}
