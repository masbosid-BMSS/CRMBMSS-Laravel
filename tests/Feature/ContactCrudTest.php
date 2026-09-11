<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use App\Services\NissGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_niss_generator_service_creates_atomic_unique_niss(): void
    {
        $service = new NissGeneratorService();
        $niss1 = $service->generate();
        $niss2 = $service->generate();

        $this->assertEquals('NISS-00000001', $niss1);
        $this->assertEquals('NISS-00000002', $niss2);
    }

    public function test_cs_can_create_contact(): void
    {
        $cs = User::create([
            'id' => 'u_cs1',
            'name' => 'CS Satu',
            'username' => 'cs1',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($cs)->post(route('contacts.store'), [
            'name' => 'Donatur Test',
            'phone' => '08123456789',
            'city' => 'Bandung',
            'status' => 'Baru',
            'relation_status' => 'Normal',
        ]);

        $this->assertDatabaseHas('contacts', [
            'name' => 'Donatur Test',
            'phone' => '08123456789',
            'owner_id' => 'u_cs1',
        ]);
    }
}
