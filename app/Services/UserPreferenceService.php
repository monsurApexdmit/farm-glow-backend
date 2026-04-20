<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceService
{
    public function getPreferences(User $user)
    {
        return $user->preferences ?? UserPreference::create([
            "user_id" => $user->id,
            "theme" => "light",
            "language" => "en",
            "timezone" => "UTC",
        ]);
    }

    public function updatePreferences(User $user, array $data)
    {
        $preferences = $this->getPreferences($user);
        $preferences->update($data);
        return $preferences;
    }
}
