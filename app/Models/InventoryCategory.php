<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryCategory extends Model {
    use SoftDeletes;
    protected $fillable = ["name", "slug", "description", "icon", "color", "is_active", "created_by"];
    protected $casts = ["is_active" => "boolean"];
    public function items() { return $this->hasMany(InventoryItem::class, "category_id"); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
    public function scopeActive($query) { return $query->where("is_active", true)->whereNull("deleted_at"); }
}
