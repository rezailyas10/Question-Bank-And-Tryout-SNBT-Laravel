<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationITI extends Model
{
    use HasFactory;

     protected $table = 'registration_iti';


    protected $fillable = [
        'result_id',
        'periode_akademik',
        'program_studi',
        'agree_to_contact',
        'status',
        'keterangan'
    ];

    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
