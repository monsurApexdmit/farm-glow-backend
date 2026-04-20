<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuditTrail extends Model
{
    protected $table = "user_audit_trail";

    protected $fillable = [
        "user_id",
        "event",
        "model_type",
        "model_id",
        "changes",
        "ip_address",
    ];

    public $timestamps = ["created_at"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where("user_id", $userId);
    }

    public function scopeByEvent($query, $event)
    {
        return $query->where("event", $event);
    }

    public function scopeByModel($query, $modelType)
    {
        return $query->where("model_type", $modelType);
    }
}
