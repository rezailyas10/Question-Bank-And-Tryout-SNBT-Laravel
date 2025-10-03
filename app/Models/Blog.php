<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'cover',
        'category',
        'author',
    ];

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y');
    }

}
