<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\InventorySupplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySupplierControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
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
    }
    public function test_list_suppliers_success(): void {
        InventorySupplier::create([
            "company_id" => $this->company->id,
            "name" => "Supplier A",
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/suppliers");
        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data" => ["*" => ["id", "name"]]]);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_supplier_success(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/suppliers", [
            "name" => "New Supplier",
            "email" => "supplier@example.com",
            "phone" => "123456789",
            "city" => "Test City",
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(["message", "data" => ["id", "name"]]);
        $this->assertDatabaseHas("inventory_suppliers", ["name" => "New Supplier"]);
    }
    public function test_create_supplier_missing_required(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/suppliers", []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors("name");
    }
    public function test_get_supplier_success(): void {
        $supplier = InventorySupplier::create([
            "company_id" => $this->company->id,
            "name" => "Test Supplier",
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/suppliers/{$supplier->id}");
        $response->assertStatus(200);
        $response->assertJsonPath("data.name", "Test Supplier");
    }
    public function test_update_supplier(): void {
        $supplier = InventorySupplier::create([
            "company_id" => $this->company->id,
            "name" => "Old Name",
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->putJson("/api/v1/suppliers/{$supplier->id}", [
            "name" => "Updated Supplier",
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas("inventory_suppliers", ["id" => $supplier->id, "name" => "Updated Supplier"]);
    }
    public function test_delete_supplier(): void {
        $supplier = InventorySupplier::create([
            "company_id" => $this->company->id,
            "name" => "Delete Supplier",
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->deleteJson("/api/v1/suppliers/{$supplier->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted("inventory_suppliers", ["id" => $supplier->id]);
    }
    public function test_get_supplier_items(): void {
        $supplier = InventorySupplier::create([
            "company_id" => $this->company->id,
            "name" => "Supplier B",
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/suppliers/{$supplier->id}/items");
        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data" => ["*" => ["id", "name"]]]);
    }
}
