<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'score',
        'exam_id'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(ResultDetails::class);
    }

    public function aiEvaluation()
{
    return $this->hasMany(ResultsEvaluation::class);
}
    public function log()
{
    return $this->hasMany(ResultLog::class);
}
}
