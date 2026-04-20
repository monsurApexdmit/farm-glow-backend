<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\Farm;
use App\Models\InventoryCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryCategoryControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
    private Farm $farm;
    protected function setUp(): void {
        parent::setUp();
        $this->company = Company::create(["name" => "Test Farm Company", "email" => "farm@example.com"]);
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
    public function test_list_categories_success(): void {
        InventoryCategory::create(["name" => "Seeds", "created_by" => $this->user->id]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/categories");
        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data" => ["*" => ["id", "name"]]]);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_category_success(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/categories", [
            "name" => "Equipment",
            "description" => "Farm equipment",
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(["message", "data" => ["id", "name"]]);
        $this->assertDatabaseHas("inventory_categories", ["name" => "Equipment"]);
    }
    public function test_create_category_missing_required(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/categories", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors("name");
    }
    public function test_get_category_success(): void {
        $category = InventoryCategory::create(["name" => "Fertilizers", "created_by" => $this->user->id]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/categories/{$category->id}");
        $response->assertStatus(200);
        $response->assertJsonPath("data.name", "Fertilizers");
    }
    public function test_update_category(): void {
        $category = InventoryCategory::create(["name" => "Old Name", "created_by" => $this->user->id]);
        $response = $this->actingAs($this->user, "api")->putJson("/api/v1/inventory/categories/{$category->id}", [
            "name" => "Updated Name",
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas("inventory_categories", ["id" => $category->id, "name" => "Updated Name"]);
    }
    public function test_delete_category(): void {
        $category = InventoryCategory::create(["name" => "Delete Me", "created_by" => $this->user->id]);
        $response = $this->actingAs($this->user, "api")->deleteJson("/api/v1/inventory/categories/{$category->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted("inventory_categories", ["id" => $category->id]);
    }
    public function test_list_unauthenticated(): void {
        $response = $this->getJson("/api/v1/inventory/categories");
        $response->assertStatus(401);
    }
}
