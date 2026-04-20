<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropHealthRecord extends Model
{
    protected $fillable = [
        "crop_id",
        "health_status",
        "disease_count",
        "disease_notes",
        "pest_count",
        "pest_notes",
        "weed_count",
        "weed_notes",
        "moisture_level",
        "nitrogen_level",
        "phosphorus_level",
        "potassium_level",
        "observations",
        "weather_condition",
        "temperature",
        "humidity",
        "recorded_by",
        "created_by",
    ];

    protected $casts = [
        "moisture_level" => "decimal:2",
        "temperature" => "decimal:2",
        "humidity" => "decimal:2",
    ];

    public function crop()
    {
        return $this->belongsTo(Crop::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function scopeByCrop($query, $cropId)
    {
        return $query->where("crop_id", $cropId);
    }

    public function scopeByHealthStatus($query, $status)
    {
        return $query->where("health_status", $status);
    }
}
