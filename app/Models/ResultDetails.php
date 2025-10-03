<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'result_id',
        'question_id',
        'answer',
        'correct',
        'score'
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
