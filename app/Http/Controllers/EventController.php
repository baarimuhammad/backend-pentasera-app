<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;
use App\Models\Organizer;

class EventController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $status = $request->query('status', 'published');
        $kategori = $request->query('kategori');
        $lokasi = $request->query('lokasi');
        $date = $request->query('date');
        $limit = $request->query('limit');

        $query = Event::with(['organizer', 'tickets']);

        if ($status) {
            $query->where('event_status', $status);
        }

        // By default, only show upcoming events (not yet ended)
        if (!$request->query('include_past')) {
            $query->where('event_datetime', '>=', now());
        }

        if ($kategori) {
            $query->where('kategori_event', $kategori);
        }

        if ($lokasi) {
            $query->where('lokasi', 'LIKE', '%' . $lokasi . '%');
        }

        if ($date) {
            $query->whereDate('event_datetime', $date);
        }

        $query->orderBy('event_datetime');

        if ($limit) {
            $events = $query->limit((int) $limit)->get();
        } else {
            $events = $query->paginate(12);
        }

        return $this->success($events, 'Daftar event berhasil diambil');
    }

    /**
     * Live search — returns grouped results (events, locations, categories)
     * for the navbar live-search dropdown.
     */
    public function search(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return $this->success([
                'events' => [],
                'locations' => [],
                'categories' => [],
            ], 'Query terlalu pendek');
        }

        $baseQuery = Event::where('event_status', 'published')
            ->where('event_datetime', '>=', now())
            ->with(['organizer', 'tickets']);

        // 1. Events matched by name
        $eventMatches = (clone $baseQuery)
            ->where('nama_event', 'LIKE', "%{$q}%")
            ->orderBy('event_datetime')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id'    => $e->id,
                'nama'  => $e->nama_event,
                'lokasi'=> $e->lokasi,
                'tanggal' => $e->event_datetime->isoFormat('DD MMM YYYY'),
                'image' => $e->image_src,
                'harga' => $e->formatted_lowest_ticket_price,
            ]);

        // 2. Unique locations that match
        $locationMatches = Event::where('event_status', 'published')
            ->where('event_datetime', '>=', now())
            ->where('lokasi', 'LIKE', "%{$q}%")
            ->select('lokasi')
            ->distinct()
            ->limit(4)
            ->pluck('lokasi')
            ->map(fn($loc) => [
                'lokasi' => $loc,
                'count'  => Event::where('event_status', 'published')
                    ->where('event_datetime', '>=', now())
                    ->where('lokasi', 'LIKE', "%{$loc}%")
                    ->count(),
            ]);

        // 3. Unique categories that match
        $categoryMatches = Event::where('event_status', 'published')
            ->where('event_datetime', '>=', now())
            ->where('kategori_event', 'LIKE', "%{$q}%")
            ->select('kategori_event')
            ->distinct()
            ->limit(4)
            ->pluck('kategori_event')
            ->filter()
            ->map(fn($cat) => [
                'kategori' => $cat,
                'count'    => Event::where('event_status', 'published')
                    ->where('event_datetime', '>=', now())
                    ->where('kategori_event', $cat)
                    ->count(),
            ])
            ->values();

        return $this->success([
            'events'     => $eventMatches,
            'locations'  => $locationMatches,
            'categories' => $categoryMatches,
        ], 'Hasil pencarian');
    }

    public function store(Request $request)
    {
        $request->validate([
            'organizer_id'              => 'required|exists:organizers,id',
            'nama_event'                => 'required|string|max:150',
            'deskripsi'                 => 'nullable|string',
            'lokasi'                    => 'required|string|max:150',
            'event_datetime'            => 'required|date',
            'kategori_event'            => 'nullable|string|max:100',
            'image'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'event_status'              => 'sometimes|in:draft,published,pending_approval',
            'max_ticket_per_transaction'=> 'sometimes|integer|min:1|max:5',
            'one_email_one_transaction' => 'sometimes|in:0,1,true,false',
            'single_identity_per_ticket'=> 'sometimes|in:0,1,true,false',
        ]);

        // Verify ownership: organizer must belong to this creator
        $organizer = Organizer::findOrFail($request->organizer_id);
        if ($request->user()->role !== 'admin' && $organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses ke organizer ini', 403);
        }

        // Tentukan status event
        $status = $request->input('event_status', 'draft');

        // Non-admin: jika ingin publish, otomatis masuk pending_approval
        if ($request->user()->role !== 'admin' && $status === 'published') {
            $status = 'pending_approval';
        }

        $data = [
            'organizer_id'              => $request->organizer_id,
            'nama_event'                => $request->nama_event,
            'deskripsi'                 => $request->deskripsi,
            'lokasi'                    => $request->lokasi,
            'event_datetime'            => $request->event_datetime,
            'event_status'              => $status,
            'kategori_event'            => $request->kategori_event,
            'max_ticket_per_transaction'=> (int) $request->input('max_ticket_per_transaction', 5),
            'one_email_one_transaction' => filter_var($request->input('one_email_one_transaction', false), FILTER_VALIDATE_BOOLEAN),
            'single_identity_per_ticket'=> filter_var($request->input('single_identity_per_ticket', true), FILTER_VALIDATE_BOOLEAN),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('event-banners', 'public');
            $data['image_url'] = $path;
        }

        $event = Event::create($data);

        $message = $status === 'pending_approval'
            ? 'Event berhasil dibuat dan menunggu persetujuan admin'
            : 'Event berhasil dibuat';

        return $this->success($event->load(['organizer', 'tickets']), $message, 201);
    }

    public function show($id)
    {
        $event = Event::with(['organizer', 'tickets'])->findOrFail($id);
        return $this->success($event, 'Detail event berhasil diambil');
    }

    public function update(Request $request, $id)
    {
        $event = Event::with('organizer')->findOrFail($id);

        // Ownership check: admin bypass, creator must own via organizer
        if ($request->user()->role !== 'admin' && $event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses untuk mengubah event ini', 403);
        }

        $request->validate([
            'nama_event'                => 'sometimes|string|max:150',
            'deskripsi'                 => 'nullable|string',
            'lokasi'                    => 'sometimes|string|max:150',
            'event_datetime'            => 'sometimes|date',
            'event_status'              => 'sometimes|in:draft,pending_approval,published,cancelled',
            'kategori_event'            => 'nullable|string|max:100',
            'image'                     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'max_ticket_per_transaction'=> 'sometimes|integer|min:1|max:5',
            'one_email_one_transaction' => 'sometimes|in:0,1,true,false',
            'single_identity_per_ticket'=> 'sometimes|in:0,1,true,false',
        ]);

        $data = $request->only([
            'nama_event', 'deskripsi', 'lokasi', 'event_datetime', 'event_status', 'kategori_event',
            'max_ticket_per_transaction', 'one_email_one_transaction', 'single_identity_per_ticket',
        ]);

        // Cast boolean fields
        if (isset($data['one_email_one_transaction'])) {
            $data['one_email_one_transaction'] = filter_var($data['one_email_one_transaction'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($data['single_identity_per_ticket'])) {
            $data['single_identity_per_ticket'] = filter_var($data['single_identity_per_ticket'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($data['max_ticket_per_transaction'])) {
            $data['max_ticket_per_transaction'] = (int) $data['max_ticket_per_transaction'];
        }

        // Non-admin: jika mencoba set published, otomatis ke pending_approval
        if ($request->user()->role !== 'admin' && isset($data['event_status']) && $data['event_status'] === 'published') {
            $data['event_status'] = 'pending_approval';
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image_url) {
                Storage::disk('public')->delete($event->image_url);
            }
            $path = $request->file('image')->store('event-banners', 'public');
            $data['image_url'] = $path;
        }

        $event->update($data);

        return $this->success($event->fresh(), 'Event berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $event = Event::with('organizer')->findOrFail($id);

        // Ownership check: admin bypass, creator must own via organizer
        if ($request->user()->role !== 'admin' && $event->organizer->user_id !== $request->user()->id) {
            return $this->error('Anda tidak memiliki akses untuk menghapus event ini', 403);
        }

        $event->delete();

        return $this->success(null, 'Event berhasil dihapus');
    }
}
