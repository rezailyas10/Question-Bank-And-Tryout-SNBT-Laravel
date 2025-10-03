<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'is_published',
        'exam_type',
        'created_by',
        'user_id',
        'tanggal_dibuka',
        'tanggal_ditutup',
        'sub_category_id'

    ];

    protected $casts = [
        'tanggal_dibuka' => 'date',
        'tanggal_ditutup' => 'date',
    ];
    

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function results()
    {
        return $this->hasMany(Result::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questionExam()
    {
        return $this->hasMany(QuestionExam::class);
    }


}
