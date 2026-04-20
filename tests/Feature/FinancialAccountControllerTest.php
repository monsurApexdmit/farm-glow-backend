<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\FinancialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAccountControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
    protected function setUp(): void {
        parent::setUp();
        $this->company = Company::create(["name" => "Test Company", "email" => "test@example.com"]);
        $this->user = User::create([
            "company_id" => $this->company->id,
            "email" => "user@example.com",
            "password" => bcrypt("password"),
            "first_name" => "Test",
            "last_name" => "User",
        ]);
    }
    public function test_list_accounts(): void {
        FinancialAccount::create([
            "company_id" => $this->company->id,
            "name" => "Main Bank",
            "type" => "bank",
            "opening_balance" => 10000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/accounts");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_account(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/accounts", [
            "name" => "Savings Account",
            "type" => "savings",
            "opening_balance" => 5000,
            "currency" => "USD",
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas("financial_accounts", ["name" => "Savings Account"]);
    }
    public function test_get_account(): void {
        $account = FinancialAccount::create([
            "company_id" => $this->company->id,
            "name" => "Test Account",
            "type" => "cash",
            "opening_balance" => 1000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/accounts/{$account->id}");
        $response->assertStatus(200);
        $response->assertJsonPath("data.name", "Test Account");
    }
    public function test_update_account(): void {
        $account = FinancialAccount::create([
            "company_id" => $this->company->id,
            "name" => "Old Name",
            "type" => "bank",
            "opening_balance" => 1000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->putJson("/api/v1/accounts/{$account->id}", [
            "name" => "Updated Name",
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas("financial_accounts", ["name" => "Updated Name"]);
    }
    public function test_delete_account(): void {
        $account = FinancialAccount::create([
            "company_id" => $this->company->id,
            "name" => "Delete Me",
            "type" => "bank",
            "opening_balance" => 1000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->deleteJson("/api/v1/accounts/{$account->id}");
        $response->assertStatus(200);
        $this->assertSoftDeleted("financial_accounts", ["id" => $account->id]);
    }
    public function test_get_account_balance(): void {
        $account = FinancialAccount::create([
            "company_id" => $this->company->id,
            "name" => "Test",
            "type" => "bank",
            "opening_balance" => 1000,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/accounts/{$account->id}/balance");
        $response->assertStatus(200);
        $response->assertJsonPath("data.balance", 1000);
    }
}
