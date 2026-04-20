<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::create([
            "name" => "Test Company",
            "email" => "test@example.com",
        ]);
        $this->admin = User::create([
            "company_id" => $this->company->id,
            "email" => "admin@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Admin",
            "last_name" => "User",
            "is_active" => true,
        ]);
        $this->user = User::create([
            "company_id" => $this->company->id,
            "email" => "user@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "John",
            "last_name" => "Doe",
            "is_active" => true,
        ]);
    }

    public function test_list_users(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->getJson("/api/v1/users");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data",
            "pagination" => ["total", "per_page", "current_page", "last_page"],
        ]);
    }

    public function test_create_user(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->postJson("/api/v1/users", [
                "email" => "newuser@example.com",
                "password" => "password123",
                "password_confirmation" => "password123",
                "first_name" => "New",
                "last_name" => "User",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("users", [
            "email" => "newuser@example.com",
            "first_name" => "New",
        ]);
    }

    public function test_get_user(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->getJson("/api/v1/users/{$this->user->id}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.email", "user@example.com");
    }

    public function test_update_user(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->putJson("/api/v1/users/{$this->user->id}", [
                "first_name" => "Jane",
                "phone" => "1234567890",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", [
            "id" => $this->user->id,
            "first_name" => "Jane",
        ]);
    }

    public function test_delete_user(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->deleteJson("/api/v1/users/{$this->user->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted("users", ["id" => $this->user->id]);
    }

    public function test_toggle_user_active(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->postJson("/api/v1/users/{$this->user->id}/toggle-active");

        $response->assertStatus(200);
        $this->assertFalse($this->user->fresh()->is_active);
    }

    public function test_get_user_activity(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->getJson("/api/v1/users/{$this->user->id}/activity");

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data"]);
    }

    public function test_get_user_audit_trail(): void
    {
        $response = $this->actingAs($this->admin, "api")
            ->getJson("/api/v1/users/{$this->user->id}/audit-trail");

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data"]);
    }

    public function test_unauthorized_access(): void
    {
        $otherCompany = Company::create([
            "name" => "Other Company",
            "email" => "other@example.com",
        ]);
        $otherUser = User::create([
            "company_id" => $otherCompany->id,
            "email" => "other@example.com",
            "password" => bcrypt("password123"),
            "first_name" => "Other",
            "last_name" => "User",
        ]);

        $response = $this->actingAs($this->admin, "api")
            ->getJson("/api/v1/users/{$otherUser->id}");

        $response->assertStatus(403);
    }
}
