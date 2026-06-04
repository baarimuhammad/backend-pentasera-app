<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $event = $this->seedEvent();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($event->nama_event);
    }

    public function test_event_order_and_checkout_pages_receive_database_data(): void
    {
        $event = $this->seedEvent();

        $this->get('/events')
            ->assertOk()
            ->assertSee($event->nama_event);

        $this->get('/order/' . $event->id)
            ->assertOk()
            ->assertSee($event->nama_event)
            ->assertSee('REGULAR');

        $this->get('/checkout?id=' . $event->id . '&tickets=' . $event->tickets->first()->id . ':2')
            ->assertOk()
            ->assertSee('PENTASARA_CHECKOUT');
    }

    private function seedEvent(): Event
    {
        $organizer = Organizer::create([
            'organizer_name' => 'Pentasara Test Organizer',
            'contact_email' => 'test@example.com',
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'nama_event' => 'Database Routed Event',
            'deskripsi' => 'Event dari database untuk test blade.',
            'lokasi' => 'Jakarta',
            'event_datetime' => '2026-06-01 19:00:00',
            'event_status' => 'published',
            'image_url' => 'assets/kecak.png',
            'kategori_event' => 'Tari',
        ]);

        Ticket::create([
            'event_id' => $event->id,
            'kategori' => 'REGULAR',
            'harga' => 100000,
            'kuota' => 100,
            'sisa_kuota' => 90,
        ]);

        return $event->load('tickets');
    }
}
