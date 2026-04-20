<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        "company_id",
        "email",
        "password",
        "first_name",
        "last_name",
        "phone",
        "avatar_url",
        "is_active",
        "created_by",
        "deleted_by",
    ];

    protected $hidden = [
        "password",
        "remember_token",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "email_verified_at" => "datetime",
        "password" => "hashed",
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, "deleted_by");
    }

    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }

    public function auditTrails()
    {
        return $this->hasMany(UserAuditTrail::class);
    }

    public function invitations()
    {
        return $this->hasMany(UserInvitation::class, "created_by");
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where("company_id", $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }

    public function getFullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
