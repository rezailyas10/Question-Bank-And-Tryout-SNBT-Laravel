<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMajor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'major_id'

    ];
    

    // User.php
public function major()
{
    return $this->belongsTo(Major::class);
}

// Major.php
public function user()
{
    return $this->belongsTo(User::class);
}
}
