<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\Farm;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialTransactionControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
    private Farm $farm;
    private FinancialAccount $account;
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
        $this->farm = Farm::create([
            "company_id" => $this->company->id,
            "name" => "Test Farm",
            "total_area" => 100,
            "unit" => "hectares",
            "created_by" => $this->user->id,
        ]);
        $this->account = FinancialAccount::create([
            "company_id" => $this->company->id,
            "name" => "Main Account",
            "type" => "bank",
            "opening_balance" => 10000,
            "created_by" => $this->user->id,
        ]);
    }
    public function test_list_transactions(): void {
        FinancialTransaction::create([
            "account_id" => $this->account->id,
            "farm_id" => $this->farm->id,
            "type" => "income",
            "category" => "Sales",
            "amount" => 500,
            "transaction_date" => now(),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/transactions");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_transaction(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/transactions", [
            "account_id" => $this->account->id,
            "farm_id" => $this->farm->id,
            "type" => "expense",
            "category" => "Feed",
            "amount" => 250,
            "transaction_date" => now()->toDateString(),
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas("financial_transactions", ["category" => "Feed"]);
    }
    public function test_get_transaction(): void {
        $transaction = FinancialTransaction::create([
            "account_id" => $this->account->id,
            "farm_id" => $this->farm->id,
            "type" => "income",
            "category" => "Harvest",
            "amount" => 1000,
            "transaction_date" => now(),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/transactions/{$transaction->id}");
        $response->assertStatus(200);
        $response->assertJsonPath("data.category", "Harvest");
    }
    public function test_get_summary(): void {
        FinancialTransaction::create([
            "account_id" => $this->account->id,
            "type" => "income",
            "category" => "Sales",
            "amount" => 2000,
            "transaction_date" => now(),
            "created_by" => $this->user->id,
        ]);
        FinancialTransaction::create([
            "account_id" => $this->account->id,
            "type" => "expense",
            "category" => "Feed",
            "amount" => 500,
            "transaction_date" => now(),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/transactions/summary");
        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data" => ["total_income", "total_expenses", "net"]]);
    }
}
