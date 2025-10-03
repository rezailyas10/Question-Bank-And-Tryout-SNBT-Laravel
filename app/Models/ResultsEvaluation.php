<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultsEvaluation extends Model
{
    use HasFactory;

    protected $fillable = ['result_id', 'evaluation', 'recommendation','correct','wrong','score','empty'];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
