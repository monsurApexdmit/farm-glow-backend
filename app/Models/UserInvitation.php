<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserInvitation extends Model
{
    protected $fillable = [
        "company_id",
        "email",
        "token",
        "role",
        "expires_at",
        "accepted_at",
        "created_by",
    ];

    protected $casts = [
        "expires_at" => "datetime",
        "accepted_at" => "datetime",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }

    public function isPending()
    {
        return $this->accepted_at === null && !$this->isExpired();
    }

    public function scopePending($query)
    {
        return $query->whereNull("accepted_at")->where("expires_at", ">", Carbon::now());
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where("email", $email);
    }
}
