<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInvitationControllerTest extends TestCase
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

    public function test_list_invitations(): void
    {
        UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "invite@example.com",
            "token" => "test-token",
            "role" => "farmer",
            "expires_at" => Carbon::now()->addDays(7),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/invitations");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_send_invitation(): void
    {
        $response = $this->actingAs($this->user, "api")
            ->postJson("/api/v1/invitations/send", [
                "email" => "newinvite@example.com",
                "role" => "farmer",
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas("user_invitations", [
            "email" => "newinvite@example.com",
            "company_id" => $this->company->id,
        ]);
    }

    public function test_get_invitation_by_token(): void
    {
        $invitation = UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "invite@example.com",
            "token" => "test-token-123",
            "role" => "farmer",
            "expires_at" => Carbon::now()->addDays(7),
            "created_by" => $this->user->id,
        ]);

        $response = $this->getJson("/api/v1/invitations/{$invitation->token}");

        $response->assertStatus(200);
        $response->assertJsonPath("data.email", "invite@example.com");
    }

    public function test_get_expired_invitation(): void
    {
        $invitation = UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "invite@example.com",
            "token" => "expired-token",
            "role" => "farmer",
            "expires_at" => Carbon::now()->subDay(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->getJson("/api/v1/invitations/{$invitation->token}");

        $response->assertStatus(400);
    }

    public function test_accept_invitation(): void
    {
        $invitation = UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "newuser@example.com",
            "token" => "valid-token",
            "role" => "farmer",
            "expires_at" => Carbon::now()->addDays(7),
            "created_by" => $this->user->id,
        ]);

        $response = $this->postJson("/api/v1/invitations/{$invitation->token}/accept", [
            "password" => "password123",
            "password_confirmation" => "password123",
            "first_name" => "New",
            "last_name" => "User",
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", [
            "email" => "newuser@example.com",
            "first_name" => "New",
            "company_id" => $this->company->id,
        ]);
    }

    public function test_get_pending_invitations(): void
    {
        UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "pending1@example.com",
            "token" => "token1",
            "role" => "farmer",
            "expires_at" => Carbon::now()->addDays(7),
            "created_by" => $this->user->id,
        ]);

        UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "accepted@example.com",
            "token" => "token2",
            "role" => "farmer",
            "expires_at" => Carbon::now()->addDays(7),
            "accepted_at" => Carbon::now(),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->getJson("/api/v1/invitations/pending");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
    }

    public function test_delete_invitation(): void
    {
        $invitation = UserInvitation::create([
            "company_id" => $this->company->id,
            "email" => "delete@example.com",
            "token" => "delete-token",
            "role" => "farmer",
            "expires_at" => Carbon::now()->addDays(7),
            "created_by" => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, "api")
            ->deleteJson("/api/v1/invitations/{$invitation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing("user_invitations", ["id" => $invitation->id]);
    }
}
