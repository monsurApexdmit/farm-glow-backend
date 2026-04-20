<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        "user_id",
        "theme",
        "language",
        "timezone",
        "notifications_enabled",
        "email_notifications",
        "data",
    ];

    protected $casts = [
        "notifications_enabled" => "boolean",
        "email_notifications" => "boolean",
        "data" => "json",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
