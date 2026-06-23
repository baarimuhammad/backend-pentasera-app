<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah status 'pending_approval' ke kolom event_status.
     * Compatible dengan MySQL, PostgreSQL, dan SQLite.
     */
    public function up(): void
    {
        // Untuk PostgreSQL, ubah tipe kolom menggunakan Schema Builder
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite tidak support ALTER COLUMN, tapi string type sudah cukup
            // Tidak perlu modifikasi karena sudah string kompatibel
            // Cukup pastikan nilai 'pending_approval' diperbolehkan di level aplikasi
            return;
        }

        if ($driver === 'pgsql') {
            // PostgreSQL: ubah tipe kolom menggunakan raw SQL yang sesuai
            DB::statement("ALTER TABLE events ALTER COLUMN event_status TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE events ALTER COLUMN event_status SET DEFAULT 'draft'");
        } else {
            // MySQL / MariaDB
            DB::statement("ALTER TABLE events MODIFY COLUMN event_status ENUM('draft','pending_approval','published','ended','cancelled') DEFAULT 'draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // Kembalikan event pending_approval ke draft sebelum revert
        DB::table('events')->where('event_status', 'pending_approval')->update(['event_status' => 'draft']);

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            // Tidak perlu diubah karena sudah VARCHAR
            return;
        } else {
            // MySQL / MariaDB
            DB::statement("ALTER TABLE events MODIFY COLUMN event_status ENUM('draft','published','ended','cancelled') DEFAULT 'draft'");
        }
    }
};
