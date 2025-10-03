<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'multiple_text',
        'multiple1',
        'yes/no1',
        'multiple2',
        'yes/no2',
        'multiple3',
        'yes/no3',
        'multiple4',
        'yes/no4',
        'multiple5',
        'yes/no5',
        'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
