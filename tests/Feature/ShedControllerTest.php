<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\LivestockShed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShedControllerTest extends TestCase
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

    public function test_list_sheds_success(): void
    {
        LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Cattle Shed",
            "shed_type" => "barn",
            "capacity" => 50,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/sheds");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_create_shed_success(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/sheds", [
                "farm_id" => $this->farm->id,
                "name" => "Poultry Shed",
                "shed_type" => "coop",
                "capacity" => 200,
                "length" => 20.5,
                "width" => 10.3,
                "area" => 210.65,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("livestock_sheds", [
            "name" => "Poultry Shed",
            "capacity" => 200,
        ]);
    }

    public function test_get_shed_success(): void
    {
        $shed = LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Test Shed",
            "shed_type" => "barn",
            "capacity" => 50,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/sheds/{$shed->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.id", $shed->id);
    }

    public function test_update_shed_success(): void
    {
        $shed = LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Original Shed",
            "shed_type" => "barn",
            "capacity" => 50,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/sheds/{$shed->id}", [
                "name" => "Updated Shed",
                "capacity" => 75,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("livestock_sheds", [
            "id" => $shed->id,
            "name" => "Updated Shed",
        ]);
    }

    public function test_delete_shed_success(): void
    {
        $shed = LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Shed to Delete",
            "shed_type" => "barn",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/sheds/{$shed->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("livestock_sheds", ["id" => $shed->id]);
    }

    public function test_record_cleaning(): void
    {
        $shed = LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Clean Shed",
            "shed_type" => "barn",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/sheds/{$shed->id}/clean");

        $response->assertStatus(200);
        $this->assertNotNull($shed->refresh()->last_cleaned_at);
    }

    public function test_get_shed_grid(): void
    {
        $shed = LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Grid Shed",
            "shed_type" => "barn",
            "capacity" => 50,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/sheds/{$shed->id}/grid");

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data"]);
    }

    public function test_get_shed_stats(): void
    {
        $shed = LivestockShed::create([
            "farm_id" => $this->farm->id,
            "name" => "Stats Shed",
            "shed_type" => "barn",
            "capacity" => 50,
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/sheds/{$shed->id}/stats");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => [
                "total_capacity",
                "current_occupancy",
                "occupancy_percentage",
            ],
        ]);
    }
}
