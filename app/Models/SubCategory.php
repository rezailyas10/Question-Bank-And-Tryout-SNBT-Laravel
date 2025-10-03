<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','photo','slug', 'category_id', 'timer'
    ];

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function exam(){
        return $this->hasMany(Exam::class);
    }
    public function question(){
        return $this->hasMany(Question::class);
    }

    public function user(){
        return $this->hasMany(User::class);
    }

    // public function tryoutSubtest()
    // {
    //     return $this->hasMany(TryoutSubtest::class, 'subcategory_id', 'id');
    // }
}
