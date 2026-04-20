<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UserInvitationService
{
    public function sendInvitation(User $inviter, Company $company, string $email, string $role = "farmer")
    {
        $token = Str::random(60);
        $expiresAt = Carbon::now()->addDays(7);

        return UserInvitation::create([
            "company_id" => $company->id,
            "email" => $email,
            "token" => $token,
            "role" => $role,
            "expires_at" => $expiresAt,
            "created_by" => $inviter->id,
        ]);
    }

    public function getInvitations(Company $company)
    {
        return UserInvitation::where("company_id", $company->id)
            ->with(["createdBy"])
            ->orderBy("created_at", "desc")
            ->get();
    }

    public function getPendingInvitations(Company $company)
    {
        return UserInvitation::where("company_id", $company->id)
            ->pending()
            ->with(["createdBy"])
            ->orderBy("created_at", "desc")
            ->get();
    }

    public function getInvitationByToken(string $token)
    {
        return UserInvitation::where("token", $token)->first();
    }

    public function acceptInvitation(UserInvitation $invitation, array $userData)
    {
        if ($invitation->isExpired()) {
            throw new \Exception("Invitation has expired");
        }

        $user = User::create([
            "company_id" => $invitation->company_id,
            "email" => $invitation->email,
            "password" => bcrypt($userData["password"]),
            "first_name" => $userData["first_name"],
            "last_name" => $userData["last_name"],
            "is_active" => true,
        ]);

        if (class_exists("Spatie\Permission\Models\Role")) {
            try {
                $user->assignRole($invitation->role);
            } catch (\Exception $e) {
            }
        }

        $invitation->update(["accepted_at" => Carbon::now()]);

        return $user;
    }

    public function deleteInvitation(UserInvitation $invitation)
    {
        return $invitation->delete();
    }
}
