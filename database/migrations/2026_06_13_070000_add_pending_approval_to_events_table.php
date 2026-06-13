<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah status 'pending_approval' ke enum event_status.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE events MODIFY COLUMN event_status ENUM('draft','pending_approval','published','ended','cancelled') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan event pending_approval ke draft sebelum revert enum
        DB::table('events')->where('event_status', 'pending_approval')->update(['event_status' => 'draft']);
        DB::statement("ALTER TABLE events MODIFY COLUMN event_status ENUM('draft','published','ended','cancelled') DEFAULT 'draft'");
    }
};
