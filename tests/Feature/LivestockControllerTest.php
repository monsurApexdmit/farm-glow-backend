<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Livestock;
use App\Models\LivestockType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivestockControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Farm $farm;
    private LivestockType $livestockType;

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
        $this->livestockType = LivestockType::create([
            "name" => "Cattle",
            "code" => "cattle",
            "icon" => "cow",
            "color" => "#8B4513",
        ]);
    }

    public function test_list_livestock_success(): void
    {
        Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $this->livestockType->id,
            "tag_number" => "LIVE001",
            "name" => "Bessie",
            "gender" => "female",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/livestock");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_list_livestock_unauthenticated(): void
    {
        $response = $this->getJson("/api/v1/livestock");
        $response->assertStatus(401);
    }

    public function test_create_livestock_success(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/livestock", [
                "farm_id" => $this->farm->id,
                "livestock_type_id" => $this->livestockType->id,
                "tag_number" => "LIVE002",
                "name" => "Ferdinand",
                "gender" => "male",
                "breed" => "Holstein",
                "weight" => 650.50,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("livestocks", [
            "tag_number" => "LIVE002",
            "name" => "Ferdinand",
        ]);
    }

    public function test_create_livestock_missing_required(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/livestock", [
                "farm_id" => $this->farm->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["livestock_type_id", "tag_number", "gender"]);
    }

    public function test_get_livestock_success(): void
    {
        $livestock = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $this->livestockType->id,
            "tag_number" => "LIVE003",
            "gender" => "female",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/livestock/{$livestock->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.id", $livestock->id);
    }

    public function test_update_livestock_success(): void
    {
        $livestock = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $this->livestockType->id,
            "tag_number" => "LIVE004",
            "name" => "Original",
            "gender" => "male",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/livestock/{$livestock->id}", [
                "name" => "Updated",
                "weight" => 700,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("livestocks", [
            "id" => $livestock->id,
            "name" => "Updated",
        ]);
    }

    public function test_delete_livestock_success(): void
    {
        $livestock = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $this->livestockType->id,
            "tag_number" => "LIVE005",
            "gender" => "female",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/livestock/{$livestock->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("livestocks", ["id" => $livestock->id]);
    }

    public function test_record_health_success(): void
    {
        $livestock = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $this->livestockType->id,
            "tag_number" => "LIVE006",
            "gender" => "male",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/livestock/{$livestock->id}/health", [
                "health_status" => "healthy",
                "temperature" => 38.5,
                "weight" => 680,
                "observations" => "Animal in good condition",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("livestock_health_records", [
            "livestock_id" => $livestock->id,
            "health_status" => "healthy",
        ]);
    }

    public function test_get_health_history(): void
    {
        $livestock = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $this->livestockType->id,
            "tag_number" => "LIVE007",
            "gender" => "female",
            "created_by" => $this->user->id,
        ]);

        $livestock->healthRecords()->create([
            "health_status" => "healthy",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/livestock/{$livestock->id}/health");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
}
