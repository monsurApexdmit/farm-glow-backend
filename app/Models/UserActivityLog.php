<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $table = "user_activity_logs";

    protected $fillable = [
        "user_id",
        "action",
        "description",
        "ip_address",
        "user_agent",
        "data",
    ];

    protected $casts = [
        "data" => "json",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where("user_id", $userId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where("action", $action);
    }
}
