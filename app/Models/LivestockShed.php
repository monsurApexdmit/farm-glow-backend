<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockShed extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "farm_id",
        "name",
        "description",
        "shed_type",
        "type",
        "capacity",
        "length",
        "width",
        "height",
        "area",
        "temperature_min",
        "temperature_max",
        "humidity_level",
        "last_cleaned_at",
        "feed_schedule",
        "status",
        "is_active",
        "created_by",
        "deleted_by",
        "grid_row",
        "grid_col",
        "grid_row_span",
        "grid_col_span",
    ];

    protected $casts = [
        "last_cleaned_at" => "datetime",
        "capacity" => "integer",
        "length" => "decimal:2",
        "width" => "decimal:2",
        "height" => "decimal:2",
        "area" => "decimal:2",
        "temperature_min" => "decimal:2",
        "temperature_max" => "decimal:2",
        "humidity_level" => "decimal:2",
        "is_active" => "boolean",
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function livestocks()
    {
        return $this->hasMany(Livestock::class, "shed_id");
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

    public function scopeByStatus($query, $status)
    {
        return $query->where("status", $status);
    }

    public function getOccupancy()
    {
        $occupiedCount = $this->livestocks()->where("is_active", true)->count();
        return $this->capacity ? ($occupiedCount / $this->capacity) * 100 : 0;
    }

    public function getAvailableCapacity()
    {
        if (!$this->capacity) {
            return null;
        }
        $occupiedCount = $this->livestocks()->where("is_active", true)->count();
        return $this->capacity - $occupiedCount;
    }
}
