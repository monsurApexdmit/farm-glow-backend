<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerAttendance extends Model
{
    protected $fillable = [
        "worker_id",
        "attendance_date",
        "check_in_time",
        "check_out_time",
        "hours_worked",
        "status",
        "notes",
        "created_by",
    ];

    protected $casts = [
        "attendance_date" => "date",
        "hours_worked" => "decimal:2",
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

    public function scopeByDate($query, $date)
    {
        return $query->where("attendance_date", $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where("status", $status);
    }
}
