<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "field_id",
        "name",
        "type",
        "description",
        "variety",
        "planting_date",
        "expected_harvest_date",
        "actual_harvest_date",
        "estimated_yield",
        "actual_yield",
        "yield_unit",
        "status",
        "is_active",
        "created_by",
        "deleted_by",
    ];

    protected $casts = [
        "planting_date" => "datetime",
        "expected_harvest_date" => "datetime",
        "actual_harvest_date" => "datetime",
        "estimated_yield" => "decimal:2",
        "actual_yield" => "decimal:2",
        "is_active" => "boolean",
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(CropHealthRecord::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, "deleted_by");
    }

    public function scopeByField($query, $fieldId)
    {
        return $query->where("field_id", $fieldId);
    }

    public function scopeActive($query)
    {
        return $query->where("is_active", true)->whereNull("deleted_at");
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where("status", $status);
    }

    public function getLatestHealthRecord()
    {
        return $this->healthRecords()->latest()->first();
    }
}
