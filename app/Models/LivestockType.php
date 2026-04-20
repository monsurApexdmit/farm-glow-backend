<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivestockType extends Model
{
    protected $fillable = [
        "name",
        "code",
        "description",
        "icon",
        "color",
        "is_active",
    ];

    protected $casts = [
        "is_active" => "boolean",
    ];

    public function livestocks()
    {
        return $this->hasMany(Livestock::class);
    }
}
