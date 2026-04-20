<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model {
    protected $fillable = ["farm_id", "type", "month", "year", "total_income", "total_expenses", "net_profit", "data", "created_by"];
    protected $casts = ["total_income" => "decimal:2", "total_expenses" => "decimal:2", "net_profit" => "decimal:2", "data" => "json"];
    public function farm() { return $this->belongsTo(Farm::class); }
    public function createdBy() { return $this->belongsTo(User::class, "created_by"); }
}
