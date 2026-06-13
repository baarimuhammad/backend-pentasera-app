<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default Admin user
        if (!User::where('email', 'admin@pentasera.com')->exists()) {
            User::create([
                'nama' => 'Admin Pentasera',
                'email' => 'admin@pentasera.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'status' => 'aktif',
                'no_hp' => '081234567890',
                'email_verified_at' => now(),
            ]);
        }

        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'nama' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call(EventSeeder::class);
    }
}
