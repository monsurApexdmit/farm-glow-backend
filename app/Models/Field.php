<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "farm_id",
        "name",
        "description",
        "soil_type",
        "area",
        "unit",
        "latitude",
        "longitude",
        "elevation",
        "status",
        "is_active",
        "created_by",
        "deleted_by",
    ];

    protected $casts = [
        "is_active" => "boolean",
        "latitude" => "float",
        "longitude" => "float",
        "elevation" => "float",
        "area" => "float",
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function crops()
    {
        return $this->hasMany(Crop::class);
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
        return $query->where("is_active", true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where("status", $status);
    }
}
