<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\DetailOrder;

class DashboardController extends Controller
{
    use ApiResponseTrait;

    /**
     * GET /api/dashboard/stats
     * Return aggregate stats for the logged-in creator's organizer.
     */
    public function stats(Request $request)
    {
        $organizer = Organizer::where('user_id', $request->user()->id)->first();

        if (!$organizer) {
            return $this->success([
                'total_events'        => 0,
                'total_events_active' => 0,
                'total_tickets_sold'  => 0,
                'total_transactions'  => 0,
                'total_revenue'       => 0,
                'revenue_formatted'   => 'Rp 0',
                'events'              => [],
            ], 'Dashboard stats (belum ada organizer)');
        }

        $events = Event::where('organizer_id', $organizer->id)
            ->with('tickets')
            ->orderBy('event_datetime', 'desc')
            ->get();

        $now = now();
        $totalEvents       = $events->count();
        $totalEventsActive = $events->filter(fn($e) => $e->event_status === 'published' && $e->event_datetime >= $now)->count();

        // Collect all ticket IDs owned by this organizer
        $ticketIds = $events->flatMap(fn($e) => $e->tickets->pluck('id'));

        // Total tickets sold = sum of detail_orders.jumlah for paid orders referencing these tickets
        $totalTicketsSold = DetailOrder::whereIn('ticket_id', $ticketIds)
            ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
            ->sum('jumlah');

        // Total transactions = count of distinct paid orders referencing these tickets
        $totalTransactions = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->count();

        // Total revenue = sum of orders.total_harga for paid orders that reference these tickets
        $totalRevenue = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->sum('total_harga');

        $revenueFormatted = 'Rp ' . number_format($totalRevenue, 0, ',', '.');

        return $this->success([
            'total_events'        => $totalEvents,
            'total_events_active' => $totalEventsActive,
            'total_tickets_sold'  => $totalTicketsSold,
            'total_transactions'  => $totalTransactions,
            'total_revenue'       => $totalRevenue,
            'revenue_formatted'   => $revenueFormatted,
        ], 'Dashboard stats berhasil diambil');
    }

    /**
     * GET /api/my-events
     * Return events belonging to the logged-in creator's organizer.
     */
    public function myEvents(Request $request)
    {
        $organizer = Organizer::where('user_id', $request->user()->id)->first();

        if (!$organizer) {
            return $this->success([], 'Belum ada event');
        }

        $events = Event::where('organizer_id', $organizer->id)
            ->with('tickets')
            ->orderBy('event_datetime', 'desc')
            ->get();

        $result = $events->map(function ($event) {
            $ticketIds = $event->tickets->pluck('id');

            $tiketTerjual = DetailOrder::whereIn('ticket_id', $ticketIds)
                ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
                ->sum('jumlah');

            $totalPendapatan = Order::where('status_order', 'paid')
                ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
                ->sum('total_harga');

            $sisaKuotaTotal = $event->tickets->sum('sisa_kuota');

            return [
                'id'                => $event->id,
                'nama_event'        => $event->nama_event,
                'event_status'      => $event->event_status,
                'event_datetime'    => $event->event_datetime,
                'lokasi'            => $event->lokasi,
                'kategori_event'    => $event->kategori_event,
                'image_src'         => $event->image_src,
                'tiket_terjual'     => (int) $tiketTerjual,
                'total_pendapatan'  => (int) $totalPendapatan,
                'pendapatan_formatted' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
                'sisa_kuota_total'  => (int) $sisaKuotaTotal,
            ];
        });

        return $this->success($result->values(), 'Daftar event berhasil diambil');
    }

    /**
     * GET /api/events/{id}/report
     * Return detailed report for a single event (ownership check).
     */
    public function eventReport(Request $request, $id)
    {
        $event = Event::with(['organizer', 'tickets'])->findOrFail($id);

        // Ownership check
        if ($request->user()->role !== 'admin' && $event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses ke laporan event ini', 403);
        }

        $ticketIds = $event->tickets->pluck('id');

        // Stats
        $tiketTerjual = DetailOrder::whereIn('ticket_id', $ticketIds)
            ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
            ->sum('jumlah');

        $totalRevenue = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->sum('total_harga');

        $capacity = $event->tickets->sum('kuota');
        $occupancy = $capacity > 0
            ? number_format(($tiketTerjual / $capacity) * 100, 1)
            : '0.0';

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
                'id'        => $ticket->id,
                'kategori'  => $ticket->kategori,
                'harga'     => $ticket->harga,
                'kuota'     => $ticket->kuota,
                'terjual'   => (int) $sold,
                'sisa'      => $ticket->sisa_kuota,
                'revenue'   => (int) $revenue,
                'revenue_formatted' => 'Rp ' . number_format($revenue, 0, ',', '.'),
                'occupancy' => $occupancy . '%',
            ];
        });

        // Daily sales (last 30 days)
        $dailySales = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(tanggal_order) as date, COUNT(*) as orders, SUM(total_harga) as revenue')
            ->groupByRaw('DATE(tanggal_order)')
            ->orderBy('date')
            ->get();

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
                    'order_id'    => $order->id,
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

        return $this->success([
            'event' => [
                'id'             => $event->id,
                'nama_event'     => $event->nama_event,
                'event_status'   => $event->event_status,
                'event_datetime' => $event->event_datetime,
                'lokasi'         => $event->lokasi,
                'kategori_event' => $event->kategori_event,
                'image_src'      => $event->image_src,
            ],
            'stats' => [
                'tiket_terjual'    => (int) $tiketTerjual,
                'capacity'         => $capacity,
                'total_revenue'    => (int) $totalRevenue,
                'revenue_formatted'=> 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                'occupancy'        => $occupancy . '%',
            ],
            'tickets_breakdown'    => $ticketsBreakdown,
            'daily_sales'          => $dailySales,
            'recent_transactions'  => $recentTransactions,
        ], 'Laporan event berhasil diambil');
    }

    /**
     * GET /api/events/{id}/stats
     * Return stats summary for a single event (ownership check).
     */
    public function eventStats(Request $request, $id)
    {
        $event = Event::with(['organizer', 'tickets'])->findOrFail($id);

        if ($request->user()->role !== 'admin' && $event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses ke statistik event ini', 403);
        }

        $capacity  = $event->tickets->sum('kuota');
        $sold      = $event->tickets->sum(fn($t) => $t->kuota - $t->sisa_kuota);
        $remaining = $capacity - $sold;
        $occupancy = $capacity > 0
            ? number_format(($sold / $capacity) * 100, 1) . '%'
            : '0%';

        $ticketIds = $event->tickets->pluck('id');
        $revenue = Order::where('status_order', 'paid')
            ->whereHas('detailOrders', function ($q) use ($ticketIds) {
                $q->whereIn('ticket_id', $ticketIds);
            })
            ->sum('total_harga');

        $revenue_formatted = 'Rp ' . number_format($revenue, 0, ',', '.');

        return $this->success([
            'capacity'          => $capacity,
            'sold'              => $sold,
            'remaining'         => $remaining,
            'occupancy'         => $occupancy,
            'revenue'           => $revenue,
            'revenue_formatted' => $revenue_formatted,
        ], 'Statistik event berhasil diambil');
    }
}
