<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "farm_id",
        "first_name",
        "last_name",
        "email",
        "phone",
        "position",
        "employment_type",
        "national_id",
        "date_of_birth",
        "address",
        "city",
        "state",
        "postal_code",
        "emergency_contact_name",
        "emergency_contact_phone",
        "hourly_rate",
        "monthly_salary",
        "hiring_date",
        "termination_date",
        "status",
        "is_active",
        "created_by",
        "deleted_by",
    ];

    protected $casts = [
        "date_of_birth" => "date",
        "hiring_date" => "date",
        "termination_date" => "date",
        "hourly_rate" => "decimal:2",
        "monthly_salary" => "decimal:2",
        "is_active" => "boolean",
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function schedules()
    {
        return $this->hasMany(WorkerSchedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function performances()
    {
        return $this->hasMany(WorkerPerformance::class);
    }

    public function payrolls()
    {
        return $this->hasMany(WorkerPayroll::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, "deleted_by");
    }

    public function scopeByFarm($query, $farmId)
    {
        return $query->where("farm_id", $farmId);
    }

    public function scopeActive($query)
    {
        return $query->where("is_active", true)->whereNull("deleted_at");
    }

    public function getFullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAge()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->diffInYears(now());
    }
}
