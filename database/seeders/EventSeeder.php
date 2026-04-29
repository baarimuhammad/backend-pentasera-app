<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organizer;
use App\Models\Event;
use App\Models\Ticket;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Create Organizers
        $organizers = [
            Organizer::create(['organizer_name' => 'Uluwatu Cultural Foundation', 'deskripsi' => 'Yayasan pelestarian budaya Bali di Uluwatu.', 'contact_email' => 'info@uluwatu.org']),
            Organizer::create(['organizer_name' => 'Keraton Heritage', 'deskripsi' => 'Pengelola event budaya di Keraton Yogyakarta.', 'contact_email' => 'event@keratonheritage.id']),
            Organizer::create(['organizer_name' => 'Minang Arts', 'deskripsi' => 'Komunitas seni tradisional Minangkabau.', 'contact_email' => 'hello@minangarts.id']),
            Organizer::create(['organizer_name' => 'Jatim Expo Management', 'deskripsi' => 'Pengelola event di Jawa Timur.', 'contact_email' => 'info@jatimexpo.id']),
            Organizer::create(['organizer_name' => 'Bali Cultural Park', 'deskripsi' => 'Pengelola GWK Cultural Park Bali.', 'contact_email' => 'event@gwk.id']),
            Organizer::create(['organizer_name' => 'Nusantara Budaya Foundation', 'deskripsi' => 'Yayasan pelestarian budaya Nusantara.', 'contact_email' => 'info@nusantarabudaya.org']),
        ];

        // Events data matching mock data in Blade templates
        $events = [
            [
                'organizer_id' => $organizers[0]->id,
                'nama_event' => 'Witness the Fire of Uluwatu',
                'deskripsi' => 'Pertunjukan tari Kecak yang legendaris di atas tebing Uluwatu dengan latar belakang matahari terbenam yang memukau.',
                'lokasi' => 'Pura Luhur Uluwatu, Bali',
                'event_datetime' => '2026-04-03 18:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/kecak.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[1]->id,
                'nama_event' => 'Stories Carved in Tradition',
                'deskripsi' => 'Pagelaran wayang kulit klasik yang membawakan kisah epik Ramayana oleh dalang ternama.',
                'lokasi' => 'Keraton Yogyakarta',
                'event_datetime' => '2026-04-08 20:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/wayang.png',
                'kategori_event' => 'Wayang',
            ],
            [
                'organizer_id' => $organizers[2]->id,
                'nama_event' => 'Feel the Rhythm of Minangkabau',
                'deskripsi' => 'Pertunjukan tari tradisional dari Sumatera Barat yang menampilkan ketangkasan para penari dalam memainkan piring.',
                'lokasi' => 'Padang Cultural Center',
                'event_datetime' => '2026-04-11 16:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/taripiring.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[3]->id,
                'nama_event' => 'Harmony of Javanese Heritage',
                'deskripsi' => 'Pertunjukan gamelan Jawa yang memadukan harmoni musik tradisional dengan sentuhan modern.',
                'lokasi' => 'Jatim Expo Surabaya',
                'event_datetime' => '2026-04-12 19:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/gamelan.png',
                'kategori_event' => 'Musik',
            ],
            [
                'organizer_id' => $organizers[4]->id,
                'nama_event' => 'Experience the Magic of Bali',
                'deskripsi' => 'Pertunjukan Tari Barong yang menampilkan kisah pertarungan antara kebaikan dan kejahatan dalam mitologi Bali.',
                'lokasi' => 'GWK Cultural Park, Bali',
                'event_datetime' => '2026-04-15 17:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/TariBarong.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[5]->id,
                'nama_event' => 'Rhythm in Perfect Harmony',
                'deskripsi' => 'Tari Saman dari Aceh yang menampilkan sinkronisasi gerakan sempurna oleh puluhan penari.',
                'lokasi' => 'Istora Senayan, Jakarta',
                'event_datetime' => '2026-04-20 19:30:00',
                'event_status' => 'published',
                'image_url' => 'assets/tarisaman.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[5]->id,
                'nama_event' => 'Grace in Every Movement',
                'deskripsi' => 'Pertunjukan Tari Jaipong yang memadukan keanggunan dan energi dalam setiap gerakan.',
                'lokasi' => 'Saung Angklung Udjo, Bandung',
                'event_datetime' => '2026-04-25 18:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/jaipong.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[5]->id,
                'nama_event' => 'The Spirit of Batak Heritage',
                'deskripsi' => 'Tari Tor-Tor khas Batak yang menampilkan kekuatan spiritual dan budaya Batak Toba.',
                'lokasi' => 'Danau Toba, Sumatera Utara',
                'event_datetime' => '2026-04-30 16:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/tortor.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[4]->id,
                'nama_event' => 'Elegance of the Royal Court',
                'deskripsi' => 'Tari Legong yang menampilkan keanggunan tari istana Bali dengan gerakan halus dan ekspresif.',
                'lokasi' => 'Puri Saren Agung, Ubud',
                'event_datetime' => '2026-05-05 18:30:00',
                'event_status' => 'published',
                'image_url' => 'assets/legong.png',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[1]->id,
                'nama_event' => 'Stories Carved in Tradition II',
                'deskripsi' => 'Pertunjukan wayang golek Sunda yang membawakan cerita pewayangan dengan boneka kayu tiga dimensi.',
                'lokasi' => 'Gedung Kesenian Jakarta',
                'event_datetime' => '2026-05-10 19:00:00',
                'event_status' => 'published',
                'image_url' => 'assets/wayanggolek.png',
                'kategori_event' => 'Wayang',
            ],
            // Ended events
            [
                'organizer_id' => $organizers[3]->id,
                'nama_event' => 'Reog Ponorogo Festival',
                'deskripsi' => 'Festival Reog Ponorogo yang menampilkan kesenian tradisional khas Ponorogo.',
                'lokasi' => 'Alun-Alun Ponorogo',
                'event_datetime' => '2026-03-18 15:00:00',
                'event_status' => 'ended',
                'image_url' => 'https://images.unsplash.com/photo-1604714628312-d10e6a6e1a00?w=800&h=400&fit=crop',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[4]->id,
                'nama_event' => 'Tari Pendet Massal',
                'deskripsi' => 'Pertunjukan Tari Pendet massal yang menampilkan keindahan tari penyambutan Bali.',
                'lokasi' => 'Ubud Palace, Bali',
                'event_datetime' => '2026-03-10 17:00:00',
                'event_status' => 'ended',
                'image_url' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&h=400&fit=crop',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[5]->id,
                'nama_event' => 'Sasando Music Night',
                'deskripsi' => 'Konser musik sasando dari NTT yang menampilkan keindahan alat musik tradisional.',
                'lokasi' => 'Kupang Arts Center, NTT',
                'event_datetime' => '2026-03-05 19:00:00',
                'event_status' => 'ended',
                'image_url' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=800&h=400&fit=crop',
                'kategori_event' => 'Musik',
            ],
            [
                'organizer_id' => $organizers[2]->id,
                'nama_event' => 'Minang Arts Festival',
                'deskripsi' => 'Festival seni Minangkabau yang menampilkan berbagai kesenian tradisional Sumatera Barat.',
                'lokasi' => 'Bukittinggi, Sumbar',
                'event_datetime' => '2026-03-01 14:00:00',
                'event_status' => 'ended',
                'image_url' => 'https://images.unsplash.com/photo-1590059431737-083651139481?w=800&h=400&fit=crop',
                'kategori_event' => 'Tari',
            ],
            [
                'organizer_id' => $organizers[3]->id,
                'nama_event' => 'Gamelan Heritage Night',
                'deskripsi' => 'Malam gamelan heritage yang menampilkan keindahan musik gamelan Jawa.',
                'lokasi' => 'Solo, Jawa Tengah',
                'event_datetime' => '2026-02-25 19:30:00',
                'event_status' => 'ended',
                'image_url' => 'https://images.unsplash.com/photo-1571019613576-2b22c76fd955?w=800&h=400&fit=crop',
                'kategori_event' => 'Musik',
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::create($eventData);

            // Create tickets for each event
            $ticketSets = [
                ['kategori' => 'REGULAR', 'harga' => 80000, 'kuota' => 200, 'sisa_kuota' => 180],
                ['kategori' => 'VIP', 'harga' => 150000, 'kuota' => 100, 'sisa_kuota' => 85],
                ['kategori' => 'VVIP', 'harga' => 250000, 'kuota' => 50, 'sisa_kuota' => 42],
                ['kategori' => 'EARLY BIRD', 'harga' => 60000, 'kuota' => 75, 'sisa_kuota' => 20],
            ];

            foreach ($ticketSets as $ticket) {
                Ticket::create(array_merge($ticket, ['event_id' => $event->id]));
            }
        }
    }
}
