<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockHealthRecord extends Model
{
    protected $fillable = [
        "livestock_id",
        "health_status",
        "observations",
        "treatment",
        "disease_name",
        "disease_start_date",
        "recovery_date",
        "temperature",
        "weight",
        "weight_unit",
        "notes",
        "veterinarian",
        "created_by",
    ];

    protected $casts = [
        "disease_start_date" => "date",
        "recovery_date" => "date",
        "temperature" => "decimal:2",
        "weight" => "decimal:2",
    ];

    public function livestock()
    {
        return $this->belongsTo(Livestock::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function scopeByLivestock($query, $livestockId)
    {
        return $query->where("livestock_id", $livestockId);
    }

    public function scopeByHealthStatus($query, $status)
    {
        return $query->where("health_status", $status);
    }
}
