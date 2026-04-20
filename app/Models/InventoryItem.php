<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model {
    use SoftDeletes;
    protected $fillable = ["farm_id", "category_id", "supplier_id", "name", "sku", "description", "unit", "quantity", "min_quantity", "max_quantity", "cost_per_unit", "total_value", "expiry_date", "location", "status", "is_active", "created_by", "deleted_by"];
    protected $casts = ["quantity" => "decimal:3", "cost_per_unit" => "decimal:2", "total_value" => "decimal:2", "min_quantity" => "decimal:3", "max_quantity" => "decimal:3", "expiry_date" => "date", "is_active" => "boolean"];
    public function farm() { return $this->belongsTo(Farm::class); }
    public function category() { return $this->belongsTo(InventoryCategory::class); }
    public function supplier() { return $this->belongsTo(InventorySupplier::class); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class); }
    public function reorderPoint() { return $this->hasOne(InventoryReorderPoint::class); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
    public function deletedBy() { return $this->belongsTo(User::class, "deleted_by"); }
    public function scopeActive($query) { return $query->where("is_active", true)->whereNull("deleted_at"); }
    public function scopeByFarm($query, $farmId) { return $query->where("farm_id", $farmId); }
    public function scopeByCategory($query, $categoryId) { return $query->where("category_id", $categoryId); }
    public function scopeByStatus($query, $status) { return $query->where("status", $status); }
    public function isLowStock() { if (!$this->reorderPoint) return false; return $this->quantity <= $this->reorderPoint->reorder_point; }
    public function isExpired() { return $this->expiry_date && $this->expiry_date->isPast(); }
}
