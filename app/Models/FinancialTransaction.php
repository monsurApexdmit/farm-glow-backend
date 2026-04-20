<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model {
    use SoftDeletes;
    protected $fillable = ["account_id", "farm_id", "type", "category", "description", "amount", "reference_number", "transaction_date", "status", "notes", "created_by", "deleted_by"];
    protected $casts = ["amount" => "decimal:2", "transaction_date" => "date"];
    public function account() { return $this->belongsTo(FinancialAccount::class); }
    public function farm() { return $this->belongsTo(Farm::class); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
    public function deletedBy() { return $this->belongsTo(User::class, "deleted_by"); }
    public function scopeByAccount($query, $accountId) { return $query->where("account_id", $accountId); }
    public function scopeByType($query, $type) { return $query->where("type", $type); }
    public function scopeByCategory($query, $category) { return $query->where("category", $category); }
    public function scopeByDate($query, $date) { return $query->where("transaction_date", $date); }
}
