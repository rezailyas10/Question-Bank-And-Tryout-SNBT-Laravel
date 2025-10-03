<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

      protected $fillable = [
        'name','level','peminat','passing_score','quota','slug', 'university_id'
    ];

    public function university(){
        return $this->belongsTo(University::class);
    }

      public function userMajor()
    {
        return $this->hasMany(UserMajor::class);
    }


}

