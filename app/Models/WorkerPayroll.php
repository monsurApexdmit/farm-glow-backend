<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerPayroll extends Model
{
    protected $fillable = [
        "worker_id",
        "year",
        "month",
        "base_salary",
        "hours_worked",
        "hourly_rate",
        "overtime_hours",
        "overtime_amount",
        "bonuses",
        "deductions",
        "net_salary",
        "payment_date",
        "payment_method",
        "payment_status",
        "notes",
        "created_by",
    ];

    protected $casts = [
        "base_salary" => "decimal:2",
        "hours_worked" => "decimal:2",
        "hourly_rate" => "decimal:2",
        "overtime_hours" => "decimal:2",
        "overtime_amount" => "decimal:2",
        "bonuses" => "decimal:2",
        "deductions" => "decimal:2",
        "net_salary" => "decimal:2",
        "payment_date" => "date",
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function scopeByWorker($query, $workerId)
    {
        return $query->where("worker_id", $workerId);
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->where("year", $year)->where("month", $month);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where("payment_status", $status);
    }
}
