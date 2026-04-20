<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerSchedule extends Model
{
    protected $fillable = [
        "farm_id",
        "worker_id",
        "work_date",
        "start_time",
        "end_time",
        "shift_type",
        "notes",
        "status",
        "created_by",
    ];

    protected $casts = [
        "work_date" => "date",
        "start_time" => "datetime:H:i",
        "end_time" => "datetime:H:i",
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function scopeByFarm($query, $farmId)
    {
        return $query->where("farm_id", $farmId);
    }

    public function scopeByWorker($query, $workerId)
    {
        return $query->where("worker_id", $workerId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where("work_date", $date);
    }
}
