<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::create([
            "name" => "Test Farm Company",
            "email" => "farm@example.com",
        ]);
        $this->user = User::create([
            "company_id" => $this->company->id,
            "email" => "farmer@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "John",
            "last_name" => "Farmer",
        ]);
    }

    public function test_list_farms_success(): void
    {
        Farm::create([
            "company_id" => $this->company->id,
            "name" => "Farm A",
            "total_area" => 100.5,
            "unit" => "hectares",
            "latitude" => 35.6762,
            "longitude" => 139.6503,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/farms");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => [
                "*" => ["id", "name", "total_area", "unit", "latitude", "longitude"],
            ],
        ]);
        $response->assertJsonCount(1, "data");
    }

    public function test_list_farms_unauthenticated(): void
    {
        $response = $this->getJson("/api/v1/farms");
        $response->assertStatus(401);
    }

    public function test_list_farms_empty(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/farms");

        $response->assertStatus(200);
        $response->assertJsonCount(0, "data");
    }

    public function test_create_farm_success(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/farms", [
                "name" => "New Farm",
                "total_area" => 150.75,
                "unit" => "hectares",
                "latitude" => 35.6762,
                "longitude" => 139.6503,
                "description" => "Test farm description",
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            "message",
            "data" => ["id", "name", "total_area", "unit", "latitude", "longitude"],
        ]);
        $this->assertDatabaseHas("farms", [
            "company_id" => $this->company->id,
            "name" => "New Farm",
            "total_area" => 150.75,
        ]);
    }

    public function test_create_farm_missing_required_fields(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/farms", [
                "total_area" => 100,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["name", "unit"]);
    }

    public function test_create_farm_invalid_area(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/farms", [
                "name" => "Farm",
                "total_area" => -10,
                "unit" => "hectares",
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("total_area");
    }

    public function test_create_farm_invalid_latitude(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/farms", [
                "name" => "Farm",
                "total_area" => 100,
                "unit" => "hectares",
                "latitude" => 95,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("latitude");
    }

    public function test_create_farm_invalid_longitude(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/farms", [
                "name" => "Farm",
                "total_area" => 100,
                "unit" => "hectares",
                "longitude" => 200,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("longitude");
    }

    public function test_get_farm_success(): void
    {
        $farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Test Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/farms/{$farm->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => ["id", "name", "total_area", "unit"],
        ]);
        $response->assertJsonPath("data.id", $farm->id);
    }

    public function test_get_farm_not_found(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/farms/99999");

        $response->assertStatus(404);
    }

    public function test_update_farm_success(): void
    {
        $farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Original Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/farms/{$farm->id}", [
                "name" => "Updated Farm",
                "total_area" => 150.5,
                "unit" => "hectares",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("farms", [
            "id" => $farm->id,
            "name" => "Updated Farm",
            "total_area" => 150.5,
        ]);
    }

    public function test_update_farm_partial(): void
    {
        $farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Original Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->patchJson("/api/v1/farms/{$farm->id}", [
                "name" => "Partially Updated Farm",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("farms", [
            "id" => $farm->id,
            "name" => "Partially Updated Farm",
            "total_area" => 100,
        ]);
    }

    public function test_delete_farm_success(): void
    {
        $farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Farm to Delete",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/farms/{$farm->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("farms", ["id" => $farm->id]);
    }

    public function test_farm_summary_success(): void
    {
        $farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Test Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/farms/{$farm->id}/summary");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => ["total_fields", "total_area", "unit"],
        ]);
    }

    public function test_farm_stats_success(): void
    {
        $farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Test Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/farms/{$farm->id}/stats");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => ["total_fields", "area_by_status"],
        ]);
    }
}
