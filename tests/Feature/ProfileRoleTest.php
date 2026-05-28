<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_fields(): void
    {
        $user = User::factory()->create([
            'nama' => 'Test User',
            'no_hp' => '081234567890',
            'role' => 'buyer',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile', [
            'nama' => 'Updated User Name',
            'no_hp' => '089999999999',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.nama', 'Updated User Name');
        $response->assertJsonPath('data.no_hp', '089999999999');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nama' => 'Updated User Name',
            'no_hp' => '089999999999',
        ]);
    }

    public function test_user_role_switch_to_creator_creates_organizer_automatically(): void
    {
        $user = User::factory()->create([
            'nama' => 'My Event Creator',
            'role' => 'buyer',
        ]);

        Sanctum::actingAs($user);

        // Verify no organizer exists yet
        $this->assertDatabaseMissing('organizers', [
            'user_id' => $user->id,
        ]);

        $response = $this->patchJson('/api/profile', [
            'role' => 'creator',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.role', 'creator');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'creator',
        ]);

        // Organizer should have been created automatically with default name
        $this->assertDatabaseHas('organizers', [
            'user_id' => $user->id,
            'organizer_name' => 'Organizer My Event Creator',
        ]);
    }

    public function test_user_role_switch_back_to_buyer(): void
    {
        $user = User::factory()->create([
            'nama' => 'Some Creator',
            'role' => 'creator',
        ]);

        // Setup the organizer profile manually first
        Organizer::create([
            'user_id' => $user->id,
            'organizer_name' => 'Original Organizer Name',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile', [
            'role' => 'buyer',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.role', 'buyer');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'buyer',
        ]);

        // Organizer record remains but user role is buyer
        $this->assertDatabaseHas('organizers', [
            'user_id' => $user->id,
            'organizer_name' => 'Original Organizer Name',
        ]);
    }

    public function test_creator_can_update_organizer_fields(): void
    {
        $user = User::factory()->create([
            'nama' => 'Creator User',
            'role' => 'creator',
        ]);

        // Auto-initialized or manually setup
        Organizer::create([
            'user_id' => $user->id,
            'organizer_name' => 'Creator Organizer',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/profile', [
            'organizer_name' => 'Super Cool Events',
            'deskripsi' => 'We create awesome music concerts.',
            'address' => 'Jakarta, Indonesia',
            'contact_phone' => '0215551234',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('organizers', [
            'user_id' => $user->id,
            'organizer_name' => 'Super Cool Events',
            'deskripsi' => 'We create awesome music concerts.',
            'address' => 'Jakarta, Indonesia',
            'contact_phone' => '0215551234',
        ]);
    }
}
