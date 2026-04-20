<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model {
    protected $fillable = ["inventory_item_id", "type", "quantity", "quantity_before", "quantity_after", "cost_per_unit", "notes", "reference_number", "transaction_date", "created_by"];
    protected $casts = ["quantity" => "decimal:3", "cost_per_unit" => "decimal:2", "quantity_before" => "decimal:3", "quantity_after" => "decimal:3", "transaction_date" => "date"];
    public function item() { return $this->belongsTo(InventoryItem::class, "inventory_item_id"); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
}
