<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Farm $farm;

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
        $this->farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Test Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);
    }

    public function test_list_fields_success(): void
    {
        Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Field A",
            "area" => 25.5,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/fields");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => [
                "*" => ["id", "farm_id", "name", "area", "unit", "status"],
            ],
        ]);
        $response->assertJsonCount(1, "data");
    }

    public function test_list_fields_unauthenticated(): void
    {
        $response = $this->getJson("/api/v1/fields");
        $response->assertStatus(401);
    }

    public function test_list_fields_by_farm(): void
    {
        Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Field 1",
            "area" => 25,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/fields?farm_id={$this->farm->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_list_fields_empty(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/fields");

        $response->assertStatus(200);
        $response->assertJsonCount(0, "data");
    }

    public function test_create_field_success(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/fields", [
                "farm_id" => $this->farm->id,
                "name" => "New Field",
                "area" => 50.25,
                "unit" => "hectares",
                "status" => "available",
                "latitude" => 35.6762,
                "longitude" => 139.6503,
                "soil_type" => "loam",
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            "message",
            "data" => ["id", "farm_id", "name", "area", "unit", "status"],
        ]);
        $this->assertDatabaseHas("fields", [
            "farm_id" => $this->farm->id,
            "name" => "New Field",
            "area" => 50.25,
        ]);
    }

    public function test_create_field_missing_required_fields(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/fields", [
                "farm_id" => $this->farm->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["name", "area", "unit", "status"]);
    }

    public function test_create_field_invalid_farm(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/fields", [
                "farm_id" => 99999,
                "name" => "Field",
                "area" => 50,
                "unit" => "hectares",
                "status" => "available",
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("farm_id");
    }

    public function test_create_field_invalid_area(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/fields", [
                "farm_id" => $this->farm->id,
                "name" => "Field",
                "area" => -10,
                "unit" => "hectares",
                "status" => "available",
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("area");
    }

    public function test_create_field_invalid_status(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/fields", [
                "farm_id" => $this->farm->id,
                "name" => "Field",
                "area" => 50,
                "unit" => "hectares",
                "status" => "invalid_status",
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("status");
    }

    public function test_get_field_success(): void
    {
        $field = Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Test Field",
            "area" => 50,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/fields/{$field->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => ["id", "name", "area", "unit", "status"],
        ]);
        $response->assertJsonPath("data.id", $field->id);
    }

    public function test_get_field_not_found(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/fields/99999");

        $response->assertStatus(404);
    }

    public function test_update_field_success(): void
    {
        $field = Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Original Field",
            "area" => 50,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/fields/{$field->id}", [
                "name" => "Updated Field",
                "area" => 75.5,
                "unit" => "hectares",
                "status" => "in_use",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("fields", [
            "id" => $field->id,
            "name" => "Updated Field",
            "area" => 75.5,
            "status" => "in_use",
        ]);
    }

    public function test_update_field_partial(): void
    {
        $field = Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Original Field",
            "area" => 50,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->patchJson("/api/v1/fields/{$field->id}", [
                "status" => "fallow",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("fields", [
            "id" => $field->id,
            "name" => "Original Field",
            "status" => "fallow",
        ]);
    }

    public function test_delete_field_success(): void
    {
        $field = Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Field to Delete",
            "area" => 50,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/fields/{$field->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("fields", ["id" => $field->id]);
    }

    public function test_field_map_success(): void
    {
        $field = Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Test Field",
            "area" => 50,
            "unit" => "hectares",
            "status" => "available",
            "latitude" => 35.6762,
            "longitude" => 139.6503,
            "elevation" => 50.5,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/fields/{$field->id}/map");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => ["latitude", "longitude", "elevation"],
        ]);
    }
}
