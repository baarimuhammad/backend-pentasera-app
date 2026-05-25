<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     */
    protected $description = 'Cancel orders that have passed their expiration time and restore ticket quotas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredOrders = Order::where('status_order', 'pending')
            ->where('expired_at', '<', now())
            ->with('detailOrders.ticket')
            ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            // a. Restore ticket quotas
            foreach ($order->detailOrders as $detail) {
                if ($detail->ticket) {
                    $detail->ticket->increment('sisa_kuota', $detail->jumlah);
                }
            }

            // b. Update order status
            $order->update(['status_order' => 'expired']);
            $count++;
        }

        $message = "Expired {$count} orders";
        $this->info($message);
        Log::info($message);

        return Command::SUCCESS;
    }
}
