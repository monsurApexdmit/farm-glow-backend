<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryReorderPoint extends Model {
    protected $fillable = ["inventory_item_id", "reorder_point", "reorder_quantity", "is_active", "created_by"];
    protected $casts = ["reorder_point" => "decimal:3", "reorder_quantity" => "decimal:3", "is_active" => "boolean"];
    public function item() { return $this->belongsTo(InventoryItem::class); }
}
