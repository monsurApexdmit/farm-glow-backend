<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles for testing
        Role::create(['name' => 'owner', 'guard_name' => 'api']);
        Role::create(['name' => 'manager', 'guard_name' => 'api']);
        Role::create(['name' => 'worker', 'guard_name' => 'api']);
        Role::create(['name' => 'owner', 'guard_name' => 'web']);
        Role::create(['name' => 'manager', 'guard_name' => 'web']);
        Role::create(['name' => 'worker', 'guard_name' => 'web']);
    }

    // ===================== REGISTER COMPANY TESTS =====================
    public function test_register_company_success(): void
    {
        $response = $this->postJson('/api/v1/auth/register-company', [
            'company_name' => 'Acme Corp',
            'company_email' => 'company@acme.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@acme.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '1234567890',
            'country' => 'USA',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'email', 'first_name', 'last_name'],
            'company' => ['id', 'name', 'email'],
            'token',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Acme Corp',
            'email' => 'company@acme.com',
        ]);

        $user = User::where('email', 'john@acme.com')->first();
        $this->assertTrue($user->hasRole('owner'));
    }

    public function test_register_company_password_mismatch(): void
    {
        $response = $this->postJson('/api/v1/auth/register-company', [
            'company_name' => 'Acme Corp',
            'company_email' => 'company@acme.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@acme.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword!',
            'phone' => '1234567890',
            'country' => 'USA',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_register_company_duplicate_email(): void
    {
        // Create a company and user first
        $company = Company::create([
            'name' => 'Existing Corp',
            'email' => 'existing@corp.com',
        ]);

        User::create([
            'company_id' => $company->id,
            'email' => 'john@acme.com',
            'password' => Hash::make('Password123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->postJson('/api/v1/auth/register-company', [
            'company_name' => 'Acme Corp',
            'company_email' => 'company@acme.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'john@acme.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '1234567890',
            'country' => 'USA',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_register_company_missing_required_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/register-company', [
            'email' => 'john@acme.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'company_name',
            'company_email',
            'first_name',
            'last_name',
            'password',
        ]);
    }

    // ===================== LOGIN TESTS =====================
    public function test_login_success(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'email'],
            'token',
        ]);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure(['error']);
    }

    public function test_login_wrong_password(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('CorrectPassword123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_missing_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    // ===================== ME TESTS =====================
    public function test_me_authenticated(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        // Use actingAs to set authenticated user
        $response = $this->actingAs($user, 'api')->getJson('/api/v1/auth/me');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'email',
                'first_name',
                'last_name',
            ],
        ]);
    }

    public function test_me_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }

    // ===================== LOGOUT TESTS =====================
    public function test_logout_success(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        // Login to get a valid token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'Password123!',
        ]);

        $token = $loginResponse->json('token');

        // Use the token for logout
        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logged out successfully']);
    }

    public function test_logout_unauthenticated(): void
    {
        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);
    }

    // ===================== REFRESH TOKEN TESTS =====================
    public function test_refresh_token_success(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        // Login to get a valid token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'Password123!',
        ]);

        $token = $loginResponse->json('token');

        // Use the token to refresh
        $response = $this->withToken($token)->postJson('/api/v1/auth/refresh-token');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
            'message',
        ]);
    }

    public function test_refresh_token_unauthenticated(): void
    {
        $response = $this->postJson('/api/v1/auth/refresh-token');

        $response->assertStatus(401);
    }

    // ===================== CHANGE PASSWORD TESTS =====================
    public function test_change_password_success(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('OldPassword123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        // Login to get a valid token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'OldPassword123!',
        ]);

        $token = $loginResponse->json('token');

        // Change password with token
        $response = $this->withToken($token)->postJson('/api/v1/auth/change-password', [
            'current_password' => 'OldPassword123!',
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Password changed successfully']);

        // Verify password was actually changed
        $this->assertTrue(Hash::check('NewPassword123!', $user->refresh()->password));
    }

    public function test_change_password_wrong_current_password(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('CorrectPassword123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        // Login to get a valid token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'CorrectPassword123!',
        ]);

        $token = $loginResponse->json('token');

        $response = $this->withToken($token)->postJson('/api/v1/auth/change-password', [
            'current_password' => 'WrongPassword123!',
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(400);
    }

    public function test_change_password_new_password_mismatch(): void
    {
        $company = Company::create([
            'name' => 'Test Corp',
            'email' => 'test@corp.com',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'email' => 'user@test.com',
            'password' => Hash::make('Password123!'),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'is_active' => true,
        ]);

        // Login to get a valid token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@test.com',
            'password' => 'Password123!',
        ]);

        $token = $loginResponse->json('token');

        $response = $this->withToken($token)->postJson('/api/v1/auth/change-password', [
            'current_password' => 'Password123!',
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'DifferentPassword!',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['new_password']);
    }

    public function test_change_password_unauthenticated(): void
    {
        $response = $this->postJson('/api/v1/auth/change-password', [
            'current_password' => 'Password123!',
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(401);
    }
}
