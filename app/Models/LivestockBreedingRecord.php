<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockBreedingRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "farm_id",
        "male_id",
        "female_id",
        "breeding_date",
        "expected_birth_date",
        "actual_birth_date",
        "offspring_count",
        "observations",
        "status",
        "created_by",
    ];

    protected $casts = [
        "breeding_date" => "date",
        "expected_birth_date" => "date",
        "actual_birth_date" => "date",
        "offspring_count" => "integer",
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function male()
    {
        return $this->belongsTo(Livestock::class, "male_id");
    }

    public function female()
    {
        return $this->belongsTo(Livestock::class, "female_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function scopeByFarm($query, $farmId)
    {
        return $query->where("farm_id", $farmId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where("status", $status);
    }
}
