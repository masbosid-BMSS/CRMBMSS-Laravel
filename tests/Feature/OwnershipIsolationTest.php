<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cs_cannot_update_other_cs_contact(): void
    {
        $cs1 = User::create([
            'id' => 'u_cs1',
            'name' => 'CS Satu',
            'username' => 'cs1',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $cs2 = User::create([
            'id' => 'u_cs2',
            'name' => 'CS Dua',
            'username' => 'cs2',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $contact = Contact::create([
            'id' => 'c_1',
            'niss' => 'NISS-00000001',
            'name' => 'Donatur CS 1',
            'phone' => '08123456789',
            'owner_id' => $cs1->id,
            'status' => 'Aktif',
            'relation_status' => 'Normal',
        ]);

        // CS 2 attempts to edit CS 1's contact
        $response = $this->actingAs($cs2)->put(route('contacts.update', $contact), [
            'name' => 'Hacked Name',
            'phone' => '08123456789',
            'status' => 'Aktif',
            'relation_status' => 'Normal',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('contacts', ['id' => 'c_1', 'name' => 'Donatur CS 1']);
    }

    public function test_master_admin_can_update_any_contact(): void
    {
        $master = User::create([
            'id' => 'u_master',
            'name' => 'Master Admin',
            'username' => 'master',
            'password' => bcrypt('password'),
            'role' => 'master',
            'status' => 'active',
        ]);

        $cs1 = User::create([
            'id' => 'u_cs1',
            'name' => 'CS Satu',
            'username' => 'cs1',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $contact = Contact::create([
            'id' => 'c_1',
            'niss' => 'NISS-00000001',
            'name' => 'Donatur CS 1',
            'phone' => '08123456789',
            'owner_id' => $cs1->id,
            'status' => 'Aktif',
            'relation_status' => 'Normal',
        ]);

        $response = $this->actingAs($master)->put(route('contacts.update', $contact), [
            'name' => 'Updated by Master',
            'phone' => '08123456789',
            'status' => 'Aktif',
            'relation_status' => 'Normal',
        ]);

        $response->assertRedirect(route('contacts.show', $contact));
        $this->assertDatabaseHas('contacts', ['id' => 'c_1', 'name' => 'Updated by Master']);
    }
}
