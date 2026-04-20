<?php
namespace Tests\Feature;
use App\Models\Company;
use App\Models\Farm;
use App\Models\FinancialInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialInvoiceControllerTest extends TestCase {
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
    public function test_list_invoices(): void {
        FinancialInvoice::create([
            "farm_id" => $this->farm->id,
            "invoice_number" => "INV-001",
            "client_name" => "Client A",
            "amount" => 500,
            "issue_date" => now(),
            "due_date" => now()->addDays(30),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/invoices");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
    public function test_create_invoice(): void {
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/invoices", [
            "farm_id" => $this->farm->id,
            "invoice_number" => "INV-NEW-" . time(),
            "client_name" => "New Client",
            "client_email" => "client@example.com",
            "amount" => 1500,
            "issue_date" => now()->toDateString(),
            "due_date" => now()->addDays(30)->toDateString(),
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas("financial_invoices", ["client_name" => "New Client"]);
    }
    public function test_mark_invoice_paid(): void {
        $invoice = FinancialInvoice::create([
            "farm_id" => $this->farm->id,
            "invoice_number" => "INV-002",
            "client_name" => "Paid Client",
            "amount" => 2000,
            "issue_date" => now(),
            "due_date" => now()->addDays(30),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->postJson("/api/v1/invoices/{$invoice->id}/mark-paid");
        $response->assertStatus(200);
        $this->assertDatabaseHas("financial_invoices", ["id" => $invoice->id, "status" => "paid"]);
    }
    public function test_get_overdue_invoices(): void {
        FinancialInvoice::create([
            "farm_id" => $this->farm->id,
            "invoice_number" => "INV-003",
            "client_name" => "Overdue Client",
            "amount" => 750,
            "issue_date" => now()->subDays(60),
            "due_date" => now()->subDays(30),
            "created_by" => $this->user->id,
        ]);
        $response = $this->actingAs($this->user, "api")->getJson("/api/v1/invoices/overdue");
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }
}
