<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'roles',
        'is_validator',
        'country',
        'phone_number',
        // tambahkan baris berikut:
        'jenjang',
        'kelas',
        'sekolah',
        'instansi',
        'instagram',
        'facebook',
        'twitter',
        'google_id',
        'sub_category_id'
        
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function category(){
        return $this->belongsTo(Category::class,'categories_id','id');
    }
    public function province(){
        return $this->belongsTo(Province::class,'province_id','id');
    }
    public function regency(){
        return $this->belongsTo(Regency::class,'regencies_id','id');
    }
    public function district(){
        return $this->belongsTo(District::class,'district_id','id');
    }
    public function village(){
        return $this->belongsTo(Village::class,'village_id','id');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
    public function userMajor()
    {
        return $this->hasMany(UserMajor::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

       public function subCategory()
{
    return $this->belongsTo(SubCategory::class);
}
}
