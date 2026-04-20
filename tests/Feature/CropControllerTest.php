<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Field;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CropControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Farm $farm;
    private Field $field;

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
        $this->field = Field::create([
            "farm_id" => $this->farm->id,
            "name" => "Test Field",
            "area" => 25,
            "unit" => "hectares",
            "status" => "available",
            "created_by" => $this->user->id,
        ]);
    }

    public function test_list_crops_success(): void
    {
        Crop::create([
            "field_id" => $this->field->id,
            "name" => "Wheat",
            "type" => "Grain",
            "planting_date" => now()->subDays(30),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/crops");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => [
                "*" => ["id", "name", "type", "field_id", "status"],
            ],
        ]);
        $response->assertJsonCount(1, "data");
    }

    public function test_list_crops_unauthenticated(): void
    {
        $response = $this->getJson("/api/v1/crops");
        $response->assertStatus(401);
    }

    public function test_list_crops_by_field(): void
    {
        Crop::create([
            "field_id" => $this->field->id,
            "name" => "Maize",
            "type" => "Grain",
            "planting_date" => now()->subDays(20),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/crops?field_id={$this->field->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_create_crop_success(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/crops", [
                "field_id" => $this->field->id,
                "name" => "Rice",
                "type" => "Grain",
                "variety" => "Basmati",
                "planting_date" => now()->toDateString(),
                "expected_harvest_date" => now()->addMonths(3)->toDateString(),
                "estimated_yield" => 5000,
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            "message",
            "data" => ["id", "name", "type", "field_id"],
        ]);
        $this->assertDatabaseHas("crops", [
            "field_id" => $this->field->id,
            "name" => "Rice",
        ]);
    }

    public function test_create_crop_missing_required_fields(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/crops", [
                "field_id" => $this->field->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["name", "type", "planting_date"]);
    }

    public function test_create_crop_invalid_field(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/crops", [
                "field_id" => 99999,
                "name" => "Wheat",
                "type" => "Grain",
                "planting_date" => now()->toDateString(),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("field_id");
    }

    public function test_get_crop_success(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Barley",
            "type" => "Grain",
            "planting_date" => now()->subDays(10),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/crops/{$crop->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.id", $crop->id);
        $response->assertJsonPath("data.name", "Barley");
    }

    public function test_get_crop_not_found(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/crops/99999");

        $response->assertStatus(404);
    }

    public function test_update_crop_success(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Original Crop",
            "type" => "Vegetable",
            "planting_date" => now()->subDays(5),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/crops/{$crop->id}", [
                "name" => "Updated Crop",
                "type" => "Fruit",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("crops", [
            "id" => $crop->id,
            "name" => "Updated Crop",
            "type" => "Fruit",
        ]);
    }

    public function test_delete_crop_success(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Crop to Delete",
            "type" => "Grain",
            "planting_date" => now()->subDays(2),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/crops/{$crop->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("crops", ["id" => $crop->id]);
    }

    public function test_record_health_success(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Healthy Crop",
            "type" => "Grain",
            "planting_date" => now()->subDays(15),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/crops/{$crop->id}/health", [
                "health_status" => "healthy",
                "moisture_level" => 65.5,
                "nitrogen_level" => 150,
                "temperature" => 28.5,
                "humidity" => 70.0,
                "observations" => "Crop looking good",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("crop_health_records", [
            "crop_id" => $crop->id,
            "health_status" => "healthy",
        ]);
    }

    public function test_record_health_invalid_status(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Test Crop",
            "type" => "Grain",
            "planting_date" => now()->subDays(10),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/crops/{$crop->id}/health", [
                "health_status" => "invalid_status",
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("health_status");
    }

    public function test_get_health_history(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Monitored Crop",
            "type" => "Grain",
            "planting_date" => now()->subDays(20),
            "created_by" => $this->user->id,
        ]);

        $crop->healthRecords()->create([
            "health_status" => "healthy",
            "moisture_level" => 60.0,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/crops/{$crop->id}/health");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => [
                "*" => ["id", "health_status", "crop_id"],
            ],
        ]);
    }

    public function test_record_harvest_success(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Harvest Ready",
            "type" => "Grain",
            "planting_date" => now()->subMonths(3),
            "expected_harvest_date" => now()->subDays(5),
            "created_by" => $this->user->id,
            "status" => "growing",
        ]);

        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/crops/{$crop->id}/harvest", [
                "actual_harvest_date" => now()->toDateString(),
                "actual_yield" => 5500,
                "yield_unit" => "kg",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("crops", [
            "id" => $crop->id,
            "status" => "harvested",
            "actual_yield" => 5500,
        ]);
    }

    public function test_get_yield_info(): void
    {
        $crop = Crop::create([
            "field_id" => $this->field->id,
            "name" => "Yield Crop",
            "type" => "Grain",
            "planting_date" => now()->subMonths(2),
            "expected_harvest_date" => now()->addDays(30),
            "estimated_yield" => 5000,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/crops/{$crop->id}/yield");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => [
                "estimated_yield",
                "actual_yield",
                "yield_unit",
                "planting_date",
                "status",
            ],
        ]);
    }
}
