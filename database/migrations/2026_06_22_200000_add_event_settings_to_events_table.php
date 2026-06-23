<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom pengaturan tambahan event:
     * - max_ticket_per_transaction: batas tiket per checkout
     * - one_email_one_transaction: cegah pembelian berulang dengan email sama
     * - single_identity_per_ticket: wajib identitas berbeda per tiket
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_ticket_per_transaction')->default(5)->after('kategori_event');
            $table->boolean('one_email_one_transaction')->default(false)->after('max_ticket_per_transaction');
            $table->boolean('single_identity_per_ticket')->default(true)->after('one_email_one_transaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'max_ticket_per_transaction',
                'one_email_one_transaction',
                'single_identity_per_ticket',
            ]);
        });
    }
};
