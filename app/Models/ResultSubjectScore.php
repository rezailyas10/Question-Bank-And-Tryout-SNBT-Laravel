<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultSubjectScore extends Model
{
    use HasFactory;
    protected $fillable = ['result_id', 'sub_category_id', 'irt_score'];
    
    public function result()
    {
        return $this->belongsTo(Result::class);
    }
    
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
