<?php

namespace Tests\Feature\Api;

use App\Models\Department;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DirectoryEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_departments_index_requires_authentication(): void
    {
        $this->getJson('/api/departments')->assertStatus(401);
    }

    public function test_departments_index_returns_id_and_name_ordered_by_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Department::create(['name' => 'Zeta']);
        Department::create(['name' => 'Alpha']);

        $this->getJson('/api/departments')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Alpha')
            ->assertJsonPath('data.1.name', 'Zeta')
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }

    public function test_locations_index_requires_authentication(): void
    {
        $this->getJson('/api/locations')->assertStatus(401);
    }

    public function test_locations_index_returns_id_and_name_ordered_by_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Location::create(['name' => 'Warehouse B']);
        Location::create(['name' => 'Head Office']);

        $this->getJson('/api/locations')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Head Office')
            ->assertJsonPath('data.1.name', 'Warehouse B')
            ->assertJsonStructure(['data' => [['id', 'name']]]);
    }
}
