<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\Farm;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTransactionControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
    private Farm $farm;
    private InventoryCategory $category;
    private InventoryItem $item;
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
        $this->item = InventoryItem::create([
            "farm_id" => $this->farm->id,
            "category_id" => $this->category->id,
            "name" => "Test Item",
            "sku" => "TEST001",
            "unit" => "kg",
            "quantity" => 100,
            "cost_per_unit" => 50,
            "total_value" => 5000,
            "created_by" => $this->user->id,
        ]);
    }
    public function test_list_transactions_success(): void {
        $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/use", [
            "inventory_item_id" => $this->item->id,
            "quantity" => 10,
            "transaction_date" => now()->toDateString(),
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/transactions");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_record_use(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/use", [
            "inventory_item_id" => $this->item->id,
            "quantity" => 25,
            "transaction_date" => now()->toDateString(),
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(["message", "data" => ["id", "type", "quantity"]]);
        $this->assertDatabaseHas("inventory_transactions", ["type" => "use", "quantity" => 25]);
        $this->assertDatabaseHas("inventory_items", ["id" => $this->item->id, "quantity" => 75]);
    }
    public function test_record_restock(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/restock", [
            "inventory_item_id" => $this->item->id,
            "quantity" => 50,
            "transaction_date" => now()->toDateString(),
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas("inventory_transactions", ["type" => "restock", "quantity" => 50]);
        $this->assertDatabaseHas("inventory_items", ["id" => $this->item->id, "quantity" => 150]);
    }
    public function test_get_item_transactions(): void {
        $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/use", [
            "inventory_item_id" => $this->item->id,
            "quantity" => 5,
            "transaction_date" => now()->toDateString(),
        ]);
        $this->actingAs($this->user, "api")->postJson("/api/v1/inventory/restock", [
            "inventory_item_id" => $this->item->id,
            "quantity" => 20,
            "transaction_date" => now()->toDateString(),
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/inventory/transactions/{$this->item->id}");
        $response->assertStatus(200);
        $response->assertJsonCount(2, "data");
    }
}
