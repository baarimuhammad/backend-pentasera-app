<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('expired_at')->nullable()->after('status_order');
            $table->decimal('biaya_layanan', 10, 2)->default(0)->after('total_harga');
            $table->string('order_code', 50)->nullable()->unique()->after('id');
        });

        // Alter payments.metode to accept specific payment method values
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN metode ENUM('gopay','ovo','dana','shopeepay','qris','bni','bca','mandiri','bri') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['expired_at', 'biaya_layanan', 'order_code']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN metode ENUM('qris','ewallet','virtual_account','bank_transfer') NOT NULL");
        }
    }
};
