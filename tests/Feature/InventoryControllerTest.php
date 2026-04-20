<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\Farm;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryReorderPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
    private Farm $farm;
    private InventoryCategory $category;
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
        $this->category = InventoryCategory::create(["name" => "Seeds", "created_by" => $this->user->id]);
    }
    public function test_list_items_success(): void {
        InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Tomato Seeds",
            "sku" => "TS001",
            "unit" => "kg",
            "quantity" => 50,
            "cost_per_unit" => 100,
            "total_value" => 5000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory");
        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data" => ["*" => ["id", "name", "sku"]]]);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_item_success(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/inventory", [
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Fertilizer A",
            "sku" => "FA001",
            "unit" => "kg",
            "quantity" => 100,
            "cost_per_unit" => 50,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas("inventory_items", ["sku" => "FA001", "quantity" => 100]);
    }
    public function test_create_item_missing_required(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/inventory", [
            "farm_id" => $this->farm->id,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["category_id", "name", "sku", "unit", "cost_per_unit"]);
    }
    public function test_get_item_success(): void {
        $item = InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Rice",
            "sku" => "RC001",
            "unit" => "bag",
            "quantity" => 75,
            "cost_per_unit" => 200,
            "total_value" => 15000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/{$item->id}");
        $response->assertStatus(200);
        $response->assertJsonPath("data.name", "Rice");
    }
    public function test_get_item_not_found(): void {
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/99999");
        $response->assertStatus(404);
    }
    public function test_update_item_success(): void {
        $item = InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Old Item",
            "sku" => "OI001",
            "unit" => "kg",
            "quantity" => 50,
            "cost_per_unit" => 100,
            "total_value" => 5000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->putJson("/api/v1/inventory/{$item->id}", [
            "name" => "Updated Item",
            "quantity" => 75,
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas("inventory_items", ["id" => $item->id, "name" => "Updated Item", "quantity" => 75]);
    }
    public function test_delete_item_success(): void {
        $item = InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Delete Item",
            "sku" => "DI001",
            "unit" => "kg",
            "quantity" => 10,
            "cost_per_unit" => 50,
            "total_value" => 500,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->deleteJson("/api/v1/inventory/{$item->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted("inventory_items", ["id" => $item->id]);
    }
    public function test_get_low_stock_items(): void {
        $item = InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Low Stock Item",
            "sku" => "LS001",
            "unit" => "kg",
            "quantity" => 5,
            "cost_per_unit" => 100,
            "total_value" => 500,
            "created_by" => $this->user->id,
        ]);
        InventoryReorderPoint::create([
            "inventory_item_id" => $item->id,
            "reorder_point" => 10,
            "reorder_quantity" => 50,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/low-stock");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_get_expired_items(): void {
        $item = InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Expired Item",
            "sku" => "EX001",
            "unit" => "kg",
            "quantity" => 20,
            "cost_per_unit" => 100,
            "total_value" => 2000,
            "expiry_date" => now()->subDay(),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/expired");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_get_inventory_value(): void {
        InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Item 1",
            "sku" => "IT1",
            "unit" => "kg",
            "quantity" => 100,
            "cost_per_unit" => 50,
            "total_value" => 5000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/value");
        $response->assertStatus(200);
        $this->assertIsArray($response->json("data"));
    }
}
