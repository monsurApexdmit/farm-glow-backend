<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerPerformance extends Model
{
    protected $fillable = [
        "worker_id",
        "review_date",
        "overall_rating",
        "quality_rating",
        "productivity_rating",
        "attitude_rating",
        "reliability_rating",
        "comments",
        "strengths",
        "improvements",
        "reviewed_by",
        "created_by",
    ];

    protected $casts = [
        "review_date" => "date",
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

    public function getAverageRating()
    {
        $ratings = [
            $this->overall_rating,
            $this->quality_rating,
            $this->productivity_rating,
            $this->attitude_rating,
            $this->reliability_rating,
        ];
        return array_sum(array_filter($ratings)) / count(array_filter($ratings));
    }
}
