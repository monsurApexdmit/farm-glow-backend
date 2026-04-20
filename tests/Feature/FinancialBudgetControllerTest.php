<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\Farm;
use App\Models\FinancialBudget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialBudgetControllerTest extends TestCase {
    use RefreshDatabase;
    private Company $company;
    private User $user;
    private Farm $farm;
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
    }
    public function test_list_budgets(): void {
        FinancialBudget::create([
            "farm_id" => $this->farm->id,
            "category" => "Feed",
            "budgeted_amount" => 5000,
            "month" => now()->month,
            "year" => now()->year,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/budgets");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_budget(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/budgets", [
            "farm_id" => $this->farm->id,
            "category" => "Labor",
            "budgeted_amount" => 10000,
            "month" => now()->month,
            "year" => now()->year,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas("financial_budgets", ["category" => "Labor"]);
    }
    public function test_get_budget_summary(): void {
        FinancialBudget::create([
            "farm_id" => $this->farm->id,
            "category" => "Seeds",
            "budgeted_amount" => 3000,
            "spent_amount" => 1500,
            "month" => now()->month,
            "year" => now()->year,
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/budgets/summary");
        $response->assertStatus(200);
        $response->assertJsonPath("data.total_budgeted", 3000);
        $response->assertJsonPath("data.total_spent", 1500);
    }
}
