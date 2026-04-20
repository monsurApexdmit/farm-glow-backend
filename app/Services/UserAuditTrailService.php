<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAuditTrail;
use Illuminate\Http\Request;

class UserAuditTrailService
{
    public function log(User $user, string $event, ?string $modelType = null, ?int $modelId = null, ?array $changes = null, ?Request $request = null)
    {
        return UserAuditTrail::create([
            "user_id" => $user->id,
            "event" => $event,
            "model_type" => $modelType,
            "model_id" => $modelId,
            "changes" => $changes ? json_encode($changes) : null,
            "ip_address" => $request?->ip(),
        ]);
    }

    public function getAuditTrail(User $user, int $limit = 100)
    {
        return $user->auditTrails()
            ->orderBy("created_at", "desc")
            ->limit($limit)
            ->get();
    }

    public function getAuditByEvent(User $user, string $event, int $limit = 100)
    {
        return $user->auditTrails()
            ->where("event", $event)
            ->orderBy("created_at", "desc")
            ->limit($limit)
            ->get();
    }

    public function getAuditByModel(User $user, string $modelType, ?int $modelId = null, int $limit = 100)
    {
        $query = $user->auditTrails()
            ->where("model_type", $modelType);

        if ($modelId) {
            $query->where("model_id", $modelId);
        }

        return $query->orderBy("created_at", "desc")
            ->limit($limit)
            ->get();
    }
}
