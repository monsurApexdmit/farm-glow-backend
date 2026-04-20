<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialInvoice extends Model {
    use SoftDeletes;
    protected $fillable = ["farm_id", "invoice_number", "client_name", "client_email", "description", "amount", "issue_date", "due_date", "paid_date", "status", "notes", "created_by", "deleted_by"];
    protected $casts = ["amount" => "decimal:2", "issue_date" => "date", "due_date" => "date", "paid_date" => "date"];
    public function farm() { return $this->belongsTo(Farm::class); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
    public function deletedBy() { return $this->belongsTo(User::class, "deleted_by"); }
    public function isOverdue() { return $this->status === 'pending' && $this->due_date->isPast(); }
    public function scopeByStatus($query, $status) { return $query->where("status", $status); }
    public function scopeByFarm($query, $farmId) { return $query->where("farm_id", $farmId); }
}
