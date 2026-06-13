<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\DetailOrder;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use ApiResponseTrait;

    // ═══════════════════════════════════════════
    // WEB PAGES
    // ═══════════════════════════════════════════

    /**
     * Render admin dashboard page (web).
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Render admin manage users page (web).
     */
    public function manageUsersPage()
    {
        return view('admin.users');
    }

    /**
     * Render admin analytics page (web).
     */
    public function analyticsPage()
    {
        return view('admin.analytics');
    }

    // ═══════════════════════════════════════════
    // API: DASHBOARD STATS
    // ═══════════════════════════════════════════

    /**
     * GET /api/admin/stats
     * Statistik global untuk admin dashboard.
     */
    public function stats(Request $request)
    {
        $totalUsers = User::count();
        $totalCreators = User::where('role', 'creator')->count();
        $totalBuyers = User::where('role', 'buyer')->count();
        $totalEvents = Event::count();
        $pendingApproval = Event::where('event_status', 'pending_approval')->count();
        $publishedEvents = Event::where('event_status', 'published')->count();

        $totalRevenue = Order::where('status_order', 'paid')->sum('total_harga');
        $totalTransactions = Order::where('status_order', 'paid')->count();

        return $this->success([
            'total_users'       => $totalUsers,
            'total_creators'    => $totalCreators,
            'total_buyers'      => $totalBuyers,
            'total_events'      => $totalEvents,
            'pending_approval'  => $pendingApproval,
            'published_events'  => $publishedEvents,
            'total_revenue'     => $totalRevenue,
            'revenue_formatted' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'total_transactions'=> $totalTransactions,
        ], 'Admin stats berhasil diambil');
    }

    // ═══════════════════════════════════════════
    // API: EVENT APPROVAL
    // ═══════════════════════════════════════════

    /**
     * GET /api/admin/pending-events
     * List semua event berstatus pending_approval.
     */
    public function pendingEvents(Request $request)
    {
        $events = Event::where('event_status', 'pending_approval')
            ->with(['organizer.user', 'tickets'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id'              => $event->id,
                    'nama_event'      => $event->nama_event,
                    'deskripsi'       => $event->deskripsi,
                    'lokasi'          => $event->lokasi,
                    'event_datetime'  => $event->event_datetime,
                    'kategori_event'  => $event->kategori_event,
                    'image_src'       => $event->image_src,
                    'created_at'      => $event->created_at,
                    'organizer_name'  => $event->organizer->nama_organizer ?? '-',
                    'creator_name'    => $event->organizer->user->nama ?? '-',
                    'creator_email'   => $event->organizer->user->email ?? '-',
                    'total_tickets'   => $event->tickets->count(),
                    'total_capacity'  => $event->tickets->sum('kuota'),
                ];
            });

        return $this->success($events, 'Daftar event pending berhasil diambil');
    }

    /**
     * POST /api/admin/events/{id}/approve
     * Approve event — ubah status ke published.
     */
    public function approveEvent(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if ($event->event_status !== 'pending_approval') {
            return $this->error('Event ini tidak dalam status pending approval', 422);
        }

        $event->update(['event_status' => 'published']);

        return $this->success($event->fresh(), 'Event berhasil di-approve dan dipublikasikan');
    }

    /**
     * POST /api/admin/events/{id}/reject
     * Reject event — kembalikan status ke draft.
     */
    public function rejectEvent(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        $event = Event::findOrFail($id);

        if ($event->event_status !== 'pending_approval') {
            return $this->error('Event ini tidak dalam status pending approval', 422);
        }

        $event->update(['event_status' => 'draft']);

        return $this->success([
            'event'  => $event->fresh(),
            'alasan' => $request->alasan ?? 'Tidak ada alasan diberikan',
        ], 'Event ditolak dan dikembalikan ke draft');
    }

    /**
     * GET /api/admin/events
     * List semua event (semua status) untuk monitoring admin.
     */
    public function allEvents(Request $request)
    {
        $query = Event::with(['organizer.user', 'tickets'])
            ->orderBy('created_at', 'desc');

        // Optional filter by status
        if ($request->query('status')) {
            $query->where('event_status', $request->query('status'));
        }

        $events = $query->paginate(20);

        return $this->success($events, 'Daftar semua event berhasil diambil');
    }

    // ═══════════════════════════════════════════
    // API: MANAGE USERS
    // ═══════════════════════════════════════════

    /**
     * GET /api/admin/users
     * List semua user dengan search, filter, dan pagination.
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search by nama or email
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min((int) ($request->query('per_page', 15)), 50);

        $users = $query->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->success($users, 'Daftar user berhasil diambil');
    }

    /**
     * GET /api/admin/users/{id}
     * Detail satu user.
     */
    public function showUser(Request $request, $id)
    {
        $user = User::withCount('orders')
            ->with('organizer')
            ->findOrFail($id);

        $totalSpent = Order::where('user_id', $id)
            ->where('status_order', 'paid')
            ->sum('total_harga');

        return $this->success([
            'user' => $user,
            'total_spent' => $totalSpent,
            'total_spent_formatted' => 'Rp ' . number_format($totalSpent, 0, ',', '.'),
        ], 'Detail user berhasil diambil');
    }

    /**
     * PATCH /api/admin/users/{id}
     * Update role atau status user.
     */
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'role'   => 'sometimes|in:buyer,creator,admin',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $currentUser = $request->user();
        $targetUser = User::findOrFail($id);

        // Tidak boleh nonaktifkan diri sendiri
        if ($currentUser->id == $id && $request->has('status') && $request->status === 'nonaktif') {
            return $this->error('Anda tidak dapat menonaktifkan akun Anda sendiri', 422);
        }

        // Tidak boleh mengubah role diri sendiri dari admin
        if ($currentUser->id == $id && $request->has('role') && $request->role !== 'admin') {
            return $this->error('Anda tidak dapat mengubah role Anda sendiri', 422);
        }

        // Cek apakah ini admin terakhir
        if ($targetUser->role === 'admin' && $request->has('role') && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return $this->error('Tidak dapat mengubah role admin terakhir', 422);
            }
        }

        $updateData = [];
        if ($request->has('role')) $updateData['role'] = $request->role;
        if ($request->has('status')) $updateData['status'] = $request->status;

        if (!empty($updateData)) {
            $targetUser->update($updateData);

            // Jika user dinonaktifkan, hapus semua token
            if (isset($updateData['status']) && $updateData['status'] === 'nonaktif') {
                $targetUser->tokens()->delete();
            }
        }

        return $this->success($targetUser->fresh(), 'User berhasil diperbarui');
    }

    /**
     * GET /api/admin/users/{id}/transactions
     * Ambil profil lengkap dan histori transaksi user.
     */
    public function userTransactions(Request $request, $id)
    {
        $user = User::withCount('orders')->findOrFail($id);

        $orders = Order::where('user_id', $id)
            ->with(['detailOrders.ticket.event'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                $eventNames = $order->detailOrders
                    ->map(fn($d) => $d->ticket->event->nama_event ?? '-')
                    ->unique()
                    ->implode(', ');

                $totalTickets = $order->detailOrders->sum('jumlah');

                return [
                    'id'              => $order->id,
                    'order_code'      => $order->order_code ?? '-',
                    'event_name'      => $eventNames,
                    'tanggal_order'   => $order->tanggal_order,
                    'jumlah_tiket'    => $totalTickets,
                    'total_harga'     => $order->total_harga,
                    'total_formatted' => 'Rp ' . number_format($order->total_harga, 0, ',', '.'),
                    'status_order'    => $order->status_order,
                ];
            });

        $totalSpent = Order::where('user_id', $id)
            ->where('status_order', 'paid')
            ->sum('total_harga');

        return $this->success([
            'user' => [
                'id'         => $user->id,
                'nama'       => $user->nama,
                'email'      => $user->email,
                'no_hp'      => $user->no_hp,
                'role'       => $user->role,
                'status'     => $user->status,
                'avatar_url' => $user->avatar_url,
                'created_at' => $user->created_at,
                'orders_count' => $user->orders_count,
            ],
            'transactions'          => $orders,
            'total_spent'           => $totalSpent,
            'total_spent_formatted' => 'Rp ' . number_format($totalSpent, 0, ',', '.'),
        ], 'Detail user dan histori transaksi berhasil diambil');
    }

    /**
     * DELETE /api/admin/users/{id}
     * Hapus user.
     */
    public function deleteUser(Request $request, $id)
    {
        $currentUser = $request->user();

        // Tidak boleh hapus diri sendiri
        if ($currentUser->id == $id) {
            return $this->error('Anda tidak dapat menghapus akun Anda sendiri', 422);
        }

        $targetUser = User::findOrFail($id);

        // Tidak boleh hapus admin terakhir
        if ($targetUser->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return $this->error('Tidak dapat menghapus admin terakhir', 422);
            }
        }

        // Hapus token
        $targetUser->tokens()->delete();
        $targetUser->delete();

        return $this->success(null, 'User berhasil dihapus');
    }

    // ═══════════════════════════════════════════
    // API: ANALYTICS
    // ═══════════════════════════════════════════

    /**
     * GET /api/admin/analytics
     * Data analytics lengkap untuk admin.
     */
    public function analytics(Request $request)
    {
        // ── Overview ──
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalTransactions = Order::where('status_order', 'paid')->count();
        $totalRevenue = Order::where('status_order', 'paid')->sum('total_harga');

        // ── User Growth (last 12 months) ──
        $userGrowth = User::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Revenue Trend (last 12 months) ──
        $revenueTrend = Order::select(
                DB::raw("DATE_FORMAT(tanggal_order, '%Y-%m') as month"),
                DB::raw('SUM(total_harga) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('status_order', 'paid')
            ->where('tanggal_order', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Events by Category ──
        $eventsByCategory = Event::select('kategori_event', DB::raw('COUNT(*) as count'))
            ->whereNotNull('kategori_event')
            ->groupBy('kategori_event')
            ->orderByDesc('count')
            ->get();

        // ── Events by Status ──
        $eventsByStatus = Event::select('event_status', DB::raw('COUNT(*) as count'))
            ->groupBy('event_status')
            ->get()
            ->pluck('count', 'event_status');

        // ── Top 5 Events by Revenue ──
        $topEvents = Event::with(['organizer.user', 'tickets'])
            ->get()
            ->map(function ($event) {
                $ticketIds = $event->tickets->pluck('id');
                $revenue = Order::where('status_order', 'paid')
                    ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
                    ->sum('total_harga');
                $sold = DetailOrder::whereIn('ticket_id', $ticketIds)
                    ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
                    ->sum('jumlah');

                return [
                    'id'            => $event->id,
                    'nama_event'    => $event->nama_event,
                    'kategori'      => $event->kategori_event,
                    'organizer'     => $event->organizer->nama_organizer ?? '-',
                    'revenue'       => (int) $revenue,
                    'revenue_formatted' => 'Rp ' . number_format($revenue, 0, ',', '.'),
                    'tickets_sold'  => (int) $sold,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // ── Recent Transactions (last 10) ──
        $recentTransactions = Order::where('status_order', 'paid')
            ->with(['user', 'detailOrders.ticket.event'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($order) {
                $eventNames = $order->detailOrders
                    ->map(fn($d) => $d->ticket->event->nama_event ?? '-')
                    ->unique()
                    ->implode(', ');

                return [
                    'order_id'      => $order->id,
                    'order_code'    => $order->order_code ?? '-',
                    'buyer_name'    => $order->user->nama ?? '-',
                    'buyer_email'   => $order->user->email ?? '-',
                    'event'         => $eventNames,
                    'total'         => $order->total_harga,
                    'total_formatted' => 'Rp ' . number_format($order->total_harga, 0, ',', '.'),
                    'date'          => $order->tanggal_order,
                ];
            });

        return $this->success([
            'overview' => [
                'total_users'        => $totalUsers,
                'total_events'       => $totalEvents,
                'total_transactions' => $totalTransactions,
                'total_revenue'      => $totalRevenue,
                'revenue_formatted'  => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            ],
            'user_growth'         => $userGrowth,
            'revenue_trend'       => $revenueTrend,
            'events_by_category'  => $eventsByCategory,
            'events_by_status'    => $eventsByStatus,
            'top_events'          => $topEvents,
            'recent_transactions' => $recentTransactions,
        ], 'Analytics data berhasil diambil');
    }

    // ═══════════════════════════════════════════
    // EXPORT: CSV
    // ═══════════════════════════════════════════

    /**
     * GET /admin/export/csv
     * Generate and download a CSV report with transaction summary and revenue trend.
     */
    public function exportCsv()
    {
        $filename = 'laporan-pentasera-' . now()->format('Y-m-d_His') . '.csv';

        // Gather data
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalTransactions = Order::where('status_order', 'paid')->count();
        $totalRevenue = Order::where('status_order', 'paid')->sum('total_harga');

        $revenueTrend = Order::select(
                DB::raw("DATE_FORMAT(tanggal_order, '%Y-%m') as month"),
                DB::raw('SUM(total_harga) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('status_order', 'paid')
            ->where('tanggal_order', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $recentTransactions = Order::where('status_order', 'paid')
            ->with(['user', 'detailOrders.ticket.event'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        // Build CSV with PHP stream
        $callback = function () use (
            $totalUsers, $totalEvents, $totalTransactions, $totalRevenue,
            $revenueTrend, $recentTransactions
        ) {
            $handle = fopen('php://output', 'w');

            // BOM for UTF-8 compatibility in Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ── Section: Ringkasan ──
            fputcsv($handle, ['LAPORAN PENTASERA - ' . now()->format('d M Y H:i')]);
            fputcsv($handle, []);
            fputcsv($handle, ['=== RINGKASAN OVERVIEW ===']);
            fputcsv($handle, ['Metrik', 'Nilai']);
            fputcsv($handle, ['Total Pengguna', $totalUsers]);
            fputcsv($handle, ['Total Event', $totalEvents]);
            fputcsv($handle, ['Total Transaksi', $totalTransactions]);
            fputcsv($handle, ['Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.')]);
            fputcsv($handle, []);

            // ── Section: Tren Pendapatan ──
            fputcsv($handle, ['=== TREN PENDAPATAN (12 BULAN TERAKHIR) ===']);
            fputcsv($handle, ['Bulan', 'Pendapatan (Rp)', 'Jumlah Transaksi']);
            foreach ($revenueTrend as $row) {
                fputcsv($handle, [
                    $row->month,
                    number_format($row->revenue, 0, ',', '.'),
                    $row->transactions,
                ]);
            }
            fputcsv($handle, []);

            // ── Section: Transaksi Terakhir ──
            fputcsv($handle, ['=== TRANSAKSI TERAKHIR ===']);
            fputcsv($handle, ['Kode Order', 'Pembeli', 'Email', 'Event', 'Total (Rp)', 'Tanggal']);
            foreach ($recentTransactions as $order) {
                $eventNames = $order->detailOrders
                    ->map(fn($d) => $d->ticket->event->nama_event ?? '-')
                    ->unique()
                    ->implode(', ');

                fputcsv($handle, [
                    $order->order_code ?? '-',
                    $order->user->nama ?? '-',
                    $order->user->email ?? '-',
                    $eventNames,
                    number_format($order->total_harga, 0, ',', '.'),
                    $order->tanggal_order ? \Carbon\Carbon::parse($order->tanggal_order)->format('d M Y') : '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ]);
    }

    // ═══════════════════════════════════════════
    // EXPORT: PRINT / PDF VIEW
    // ═══════════════════════════════════════════

    /**
     * GET /admin/export/pdf
     * Render a print-friendly page that can be printed to PDF via window.print().
     */
    public function analyticsReport()
    {
        // ── Overview ──
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalTransactions = Order::where('status_order', 'paid')->count();
        $totalRevenue = Order::where('status_order', 'paid')->sum('total_harga');

        // ── Revenue Trend ──
        $revenueTrend = Order::select(
                DB::raw("DATE_FORMAT(tanggal_order, '%Y-%m') as month"),
                DB::raw('SUM(total_harga) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('status_order', 'paid')
            ->where('tanggal_order', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Top 5 Events ──
        $topEvents = Event::with(['organizer.user', 'tickets'])
            ->get()
            ->map(function ($event) {
                $ticketIds = $event->tickets->pluck('id');
                $revenue = Order::where('status_order', 'paid')
                    ->whereHas('detailOrders', fn($q) => $q->whereIn('ticket_id', $ticketIds))
                    ->sum('total_harga');
                $sold = DetailOrder::whereIn('ticket_id', $ticketIds)
                    ->whereHas('order', fn($q) => $q->where('status_order', 'paid'))
                    ->sum('jumlah');

                return [
                    'nama_event'    => $event->nama_event,
                    'organizer'     => $event->organizer->nama_organizer ?? '-',
                    'revenue'       => (int) $revenue,
                    'tickets_sold'  => (int) $sold,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // ── Recent Transactions ──
        $recentTransactions = Order::where('status_order', 'paid')
            ->with(['user', 'detailOrders.ticket.event'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($order) {
                $eventNames = $order->detailOrders
                    ->map(fn($d) => $d->ticket->event->nama_event ?? '-')
                    ->unique()
                    ->implode(', ');

                return [
                    'order_code'  => $order->order_code ?? '-',
                    'buyer_name'  => $order->user->nama ?? '-',
                    'event'       => $eventNames,
                    'total'       => $order->total_harga,
                    'date'        => $order->tanggal_order,
                ];
            });

        return view('admin.analytics-print', compact(
            'totalUsers', 'totalEvents', 'totalTransactions', 'totalRevenue',
            'revenueTrend', 'topEvents', 'recentTransactions'
        ));
    }
}
