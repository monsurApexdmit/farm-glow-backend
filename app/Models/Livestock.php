<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Livestock extends Model
{
    use SoftDeletes;

    protected $table = "livestocks";

    protected $fillable = [
        "farm_id",
        "livestock_type_id",
        "shed_id",
        "tag_number",
        "name",
        "description",
        "breed",
        "gender",
        "date_of_birth",
        "acquisition_date",
        "weight",
        "weight_unit",
        "status",
        "is_active",
        "created_by",
        "deleted_by",
    ];

    protected $casts = [
        "date_of_birth" => "date",
        "acquisition_date" => "date",
        "weight" => "decimal:2",
        "is_active" => "boolean",
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function livestockType()
    {
        return $this->belongsTo(LivestockType::class);
    }

    public function shed()
    {
        return $this->belongsTo(LivestockShed::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(LivestockHealthRecord::class);
    }

    public function maleBreedingRecords()
    {
        return $this->hasMany(LivestockBreedingRecord::class, "male_id");
    }

    public function femaleBreedingRecords()
    {
        return $this->hasMany(LivestockBreedingRecord::class, "female_id");
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

    public function scopeByShed($query, $shedId)
    {
        return $query->where("shed_id", $shedId);
    }

    public function getLatestHealthRecord()
    {
        return $this->healthRecords()->latest()->first();
    }

    public function getAge()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->diffInMonths(now());
    }
}
