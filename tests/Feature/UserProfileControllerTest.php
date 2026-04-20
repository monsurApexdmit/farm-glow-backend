<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::create([
            "name" => "Test Company",
            "email" => "test@example.com",
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

    public function test_get_current_user(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/users/me");

        $response->assertStatus(200);
        $response->assertJsonPath("data.email", "user@example.com");
    }

    public function test_update_profile(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/users/me", [
                "first_name" => "Jane",
                "last_name" => "Smith",
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", [
            "id" => $this->user->id,
            "first_name" => "Jane",
            "last_name" => "Smith",
        ]);
    }

    public function test_get_preferences(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/users/me/preferences");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "message",
            "data" => ["theme", "language", "timezone"],
        ]);
    }

    public function test_update_preferences(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->putJson("/api/v1/users/me/preferences", [
                "theme" => "dark",
                "language" => "es",
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath("data.theme", "dark");
        $response->assertJsonPath("data.language", "es");
    }

    public function test_change_password(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/users/me/change-password", [
                "current_password" => "password123",
                "password" => "newpassword123",
                "password_confirmation" => "newpassword123",
            ]);

        $response->assertStatus(200);
    }

    public function test_change_password_invalid_current(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/users/me/change-password", [
                "current_password" => "wrongpassword",
                "password" => "newpassword123",
                "password_confirmation" => "newpassword123",
            ]);

        $response->assertStatus(400);
    }

    public function test_get_activity(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/users/me/activity");

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "data"]);
    }
}
