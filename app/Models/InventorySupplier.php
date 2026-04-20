<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventorySupplier extends Model {
    use SoftDeletes;
    protected $fillable = ["company_id", "name", "email", "phone", "address", "city", "state", "country", "website", "notes", "is_active", "created_by", "deleted_by"];
    protected $casts = ["is_active" => "boolean"];
    public function company() { return $this->belongsTo(Company::class); }
    public function items() { return $this->hasMany(InventoryItem::class, "supplier_id"); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
    public function deletedBy() { return $this->belongsTo(User::class, "deleted_by"); }
    public function scopeActive($query) { return $query->where("is_active", true)->whereNull("deleted_at"); }
    public function scopeByCompany($query, $companyId) { return $query->where("company_id", $companyId); }
}
