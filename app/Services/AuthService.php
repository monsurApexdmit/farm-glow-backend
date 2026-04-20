<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function registerCompany(array $data)
    {
        // Create company
        $company = Company::create([
            'name' => $data['company_name'],
            'email' => $data['company_email'],
            'country' => $data['country'] ?? 'Unknown',
        ]);

        // Create owner user
        $user = User::create([
            'company_id' => $company->id,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'is_active' => true,
        ]);

        // Assign owner role
        $user->assignRole('owner');

        return [
            'user' => $user,
            'company' => $company,
            'token' => auth('api')->login($user),
        ];
    }

    public function login(string $email, string $password)
    {
        $credentials = ['email' => $email, 'password' => $password];

        if (!$token = auth('api')->attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return [
            'user' => auth('api')->user(),
            'token' => $token,
        ];
    }

    public function logout()
    {
        auth('api')->logout();
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $user->update(['password' => Hash::make($newPassword)]);
        return $user;
    }

    public function getCurrentUser()
    {
        return auth('api')->user();
    }

    public function refreshToken()
    {
        return auth('api')->refresh();
    }
}
