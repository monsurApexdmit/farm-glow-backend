<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Farm;
use App\Models\Livestock;
use App\Models\LivestockBreedingRecord;
use App\Models\LivestockType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreedingControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Farm $farm;
    private Livestock $maleAnimal;
    private Livestock $femaleAnimal;

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

        $livestockType = LivestockType::create([
            "name" => "Cattle",
            "code" => "cattle",
        ]);

        $this->maleAnimal = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $livestockType->id,
            "tag_number" => "BULL001",
            "gender" => "male",
            "created_by" => $this->user->id,
        ]);

        $this->femaleAnimal = Livestock::create([
            "farm_id" => $this->farm->id,
            "livestock_type_id" => $livestockType->id,
            "tag_number" => "COW001",
            "gender" => "female",
            "created_by" => $this->user->id,
        ]);
    }

    public function test_list_breeding_records(): void
    {
        LivestockBreedingRecord::create([
            "farm_id" => $this->farm->id,
            "male_id" => $this->maleAnimal->id,
            "female_id" => $this->femaleAnimal->id,
            "breeding_date" => now()->subMonths(3),
            "status" => "planned",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/breeding");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_create_breeding_record(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/breeding", [
                "farm_id" => $this->farm->id,
                "male_id" => $this->maleAnimal->id,
                "female_id" => $this->femaleAnimal->id,
                "breeding_date" => now()->toDateString(),
                "expected_birth_date" => now()->addDays(280)->toDateString(),
                "observations" => "Planned breeding",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("livestock_breeding_records", [
            "male_id" => $this->maleAnimal->id,
            "female_id" => $this->femaleAnimal->id,
            "status" => "planned",
        ]);
    }

    public function test_create_invalid_breeding(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/breeding", [
                "farm_id" => $this->farm->id,
                "male_id" => $this->maleAnimal->id,
                "female_id" => $this->maleAnimal->id,
                "breeding_date" => now()->toDateString(),
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors("female_id");
    }

    public function test_get_breeding_record(): void
    {
        $record = LivestockBreedingRecord::create([
            "farm_id" => $this->farm->id,
            "male_id" => $this->maleAnimal->id,
            "female_id" => $this->femaleAnimal->id,
            "breeding_date" => now()->subMonths(2),
            "status" => "planned",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/breeding/{$record->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.id", $record->id);
    }

    public function test_record_birth(): void
    {
        $record = LivestockBreedingRecord::create([
            "farm_id" => $this->farm->id,
            "male_id" => $this->maleAnimal->id,
            "female_id" => $this->femaleAnimal->id,
            "breeding_date" => now()->subMonths(9),
            "expected_birth_date" => now()->subDays(5),
            "status" => "planned",
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/breeding/{$record->id}/birth", [
                "actual_birth_date" => now()->toDateString(),
                "offspring_count" => 2,
                "observations" => "Successful birth of twins",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("livestock_breeding_records", [
            "id" => $record->id,
            "offspring_count" => 2,
            "status" => "completed",
        ]);
    }

    public function test_delete_breeding_record(): void
    {
        $record = LivestockBreedingRecord::create([
            "farm_id" => $this->farm->id,
            "male_id" => $this->maleAnimal->id,
            "female_id" => $this->femaleAnimal->id,
            "breeding_date" => now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/breeding/{$record->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("livestock_breeding_records", ["id" => $record->id]);
    }
}
