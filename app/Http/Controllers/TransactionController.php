<?php

namespace App\Http\Controllers;

use App\Models\DetailOrder;
use App\Models\ETicket;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Ticket;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    /**
     * POST /api/transactions
     * Create a new order with detail_orders and payment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'                    => 'required|array|min:1',
            'items.*.ticket_id'        => 'required|exists:tickets,id',
            'items.*.jumlah'           => 'required|integer|min:1',
            'buyer_info'               => 'required|array',
            'buyer_info.nama'          => 'required|string|max:255',
            'buyer_info.email'         => 'required|email|max:255',
            'buyer_info.no_hp'         => 'required|string|max:20',
            'buyer_info.no_ktp'        => 'required|string|max:20',
            'metode_pembayaran'        => 'required|in:gopay,ovo,dana,shopeepay,qris,bni,bca,mandiri,bri',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                $items = $validated['items'];
                $detailOrdersData = [];

                // 1 & 2. Lock tickets, validate quota
                foreach ($items as $item) {
                    $ticket = Ticket::lockForUpdate()->findOrFail($item['ticket_id']);

                    if ($ticket->sisa_kuota < $item['jumlah']) {
                        abort(422, "Tiket {$ticket->kategori} sudah habis atau kuota tidak mencukupi.");
                    }

                    $detailOrdersData[] = [
                        'ticket'    => $ticket,
                        'ticket_id' => $ticket->id,
                        'jumlah'    => $item['jumlah'],
                        'subtotal'  => $ticket->harga * $item['jumlah'],
                        'kategori'  => $ticket->kategori,
                    ];
                }

                // 3. Create order
                $order = Order::create([
                    'user_id'       => auth()->id(),
                    'tanggal_order' => now(),
                    'status_order'  => 'pending',
                    'total_harga'   => 0,
                    'expired_at'    => now()->addHours(24),
                ]);

                // 4. Create detail_orders, decrement sisa_kuota
                $responseItems = [];
                foreach ($detailOrdersData as $data) {
                    DetailOrder::create([
                        'order_id'  => $order->id,
                        'ticket_id' => $data['ticket_id'],
                        'jumlah'    => $data['jumlah'],
                        'subtotal'  => $data['subtotal'],
                    ]);

                    $data['ticket']->decrement('sisa_kuota', $data['jumlah']);

                    $responseItems[] = [
                        'ticket_id' => $data['ticket_id'],
                        'kategori'  => $data['kategori'],
                        'jumlah'    => $data['jumlah'],
                        'subtotal'  => $data['subtotal'],
                    ];
                }

                // 5. Calculate totals
                $subtotalTotal  = array_sum(array_column($responseItems, 'subtotal'));
                $biayaLayanan   = round($subtotalTotal * 0.10);
                $totalHarga     = $subtotalTotal + $biayaLayanan;

                // 6. Update order
                $orderCode = 'PS-' . date('Ymd') . '-' . $order->id;
                $order->update([
                    'total_harga'    => $totalHarga,
                    'biaya_layanan'  => $biayaLayanan,
                    'order_code'     => $orderCode,
                ]);

                // 7. Create payment
                $metode = $validated['metode_pembayaran'];
                Payment::create([
                    'order_id'          => $order->id,
                    'metode'            => $metode,
                    'jumlah_bayar'      => $totalHarga,
                    'status_pembayaran' => 'pending',
                    'waktu_bayar'       => now(),
                ]);

                // Generate simulated payment info
                $paymentInfo = $this->generatePaymentInfo($metode, $totalHarga);

                return [
                    'order_id'           => $order->id,
                    'order_code'         => $orderCode,
                    'total_harga'        => $totalHarga,
                    'biaya_layanan'      => $biayaLayanan,
                    'expired_at'         => $order->expired_at->toISOString(),
                    'metode_pembayaran'  => $metode,
                    'virtual_account'    => $paymentInfo['virtual_account'] ?? null,
                    'payment_info'       => $paymentInfo,
                    'items'              => $responseItems,
                ];
            });

            return $this->success($result, 'Order berhasil dibuat', 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('Tiket tidak ditemukan', 404);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return $this->error($e->getMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->error('Gagal membuat order: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/orders/{id}/confirm-payment
     * Confirm that an order has been paid.
     */
    public function confirmPayment(int $id)
    {
        $order = Order::with('detailOrders')->find($id);

        if (!$order) {
            return $this->error('Order tidak ditemukan', 404);
        }

        if ($order->user_id !== auth()->id()) {
            return $this->error('Anda tidak memiliki akses ke order ini', 403);
        }

        if ($order->status_order !== 'pending') {
            return $this->error('Order tidak dapat dikonfirmasi. Status saat ini: ' . $order->status_order, 422);
        }

        // Check if expired
        if ($order->expired_at && $order->expired_at->isPast()) {
            return $this->error('Order sudah expired. Silakan pesan ulang.', 422);
        }

        try {
            $result = DB::transaction(function () use ($order) {
                // a. Update payment
                $payment = Payment::where('order_id', $order->id)->first();
                if ($payment) {
                    $payment->update([
                        'status_pembayaran' => 'paid',
                        'waktu_bayar'       => now(),
                    ]);
                }

                // b. Update order
                $order->update(['status_order' => 'paid']);

                // c. Create e-tickets
                $eTickets = [];
                foreach ($order->detailOrders as $detail) {
                    for ($i = 0; $i < $detail->jumlah; $i++) {
                        $eTicket = ETicket::create([
                            'detail_order_id' => $detail->id,
                            'kode_qr'         => Str::uuid()->toString(),
                            'status_validasi' => 'valid',
                        ]);
                        $eTickets[] = $eTicket;
                    }
                }

                return [
                    'order'     => $order->fresh(['detailOrders.ticket', 'payment']),
                    'e_tickets' => $eTickets,
                ];
            });

            return $this->success($result, 'Pembayaran berhasil dikonfirmasi');

        } catch (\Exception $e) {
            return $this->error('Gagal mengkonfirmasi pembayaran: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Generate simulated payment info (no real gateway yet).
     */
    private function generatePaymentInfo(string $metode, float $totalHarga): array
    {
        $vaPrefix = [
            'bni'     => '8277',
            'bca'     => '1234',
            'mandiri' => '9000',
            'bri'     => '4567',
        ];

        $randomDigits = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);

        if (isset($vaPrefix[$metode])) {
            $va = $vaPrefix[$metode] . $randomDigits;
            return [
                'type'            => 'virtual_account',
                'bank'            => strtoupper($metode),
                'virtual_account' => $va,
                'jumlah'          => $totalHarga,
            ];
        }

        // E-wallet / QRIS
        return [
            'type'            => in_array($metode, ['qris']) ? 'qris' : 'ewallet',
            'provider'        => strtoupper($metode),
            'virtual_account' => 'EWALLET-' . strtoupper($metode) . '-' . mt_rand(100000, 999999),
            'jumlah'          => $totalHarga,
        ];
    }
}
