<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;

class UserActivityLogService
{
    public function logActivity(User $user, string $action, ?string $description = null, ?array $data = null, ?Request $request = null)
    {
        return UserActivityLog::create([
            "user_id" => $user->id,
            "action" => $action,
            "description" => $description,
            "ip_address" => $request?->ip(),
            "user_agent" => $request?->userAgent(),
            "data" => $data,
        ]);
    }

    public function getActivityLogs(User $user, int $limit = 50)
    {
        return $user->activityLogs()
            ->orderBy("created_at", "desc")
            ->limit($limit)
            ->get();
    }

    public function getActivityByAction(User $user, string $action, int $limit = 50)
    {
        return $user->activityLogs()
            ->where("action", $action)
            ->orderBy("created_at", "desc")
            ->limit($limit)
            ->get();
    }
}
