<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text', 'photo', 'question_type', 'explanation', 'difficulty','lesson',
        'sub_category_id', 'exam_id', 'user_id', 'status','note'
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail jawaban
    public function multipleChoice()
    {
        return $this->hasOne(MultipleChoice::class);
    }

    public function multipleOption()
    {
        return $this->hasOne(MultipleOption::class);
    }

    public function essay()
    {
        return $this->hasOne(Essay::class);
    }

    public function resultDetails()
    {
        return $this->hasOne(ResultDetails::class);
    }
}
