<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farm extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'location',
        'latitude',
        'longitude',
        'total_area',
        'unit',
        'farm_type',
        'soil_type',
        'climate_zone',
        'is_active',
        'created_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'total_area' => 'float',
            'farm_type' => 'string',
        'soil_type' => 'string',
        'climate_zone' => 'string',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Scopes
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function getFieldsCount()
    {
        return $this->fields()->count();
    }

    public function getTotalFieldsArea()
    {
        return $this->fields()->sum('area');
    }
}
