<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinancialBudget extends Model {
    protected $fillable = ["farm_id", "category", "budgeted_amount", "spent_amount", "month", "year", "notes", "created_by"];
    protected $casts = ["budgeted_amount" => "decimal:2", "spent_amount" => "decimal:2"];
    public function farm() { return $this->belongsTo(Farm::class); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
    public function getRemainingAttribute() { return $this->budgeted_amount - $this->spent_amount; }
    public function getUsagePercentageAttribute() { return ($this->spent_amount / $this->budgeted_amount) * 100; }
    public function scopeByMonth($query, $month, $year) { return $query->where("month", $month)->where("year", $year); }
}
