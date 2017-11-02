<?php

namespace App;
use DB;
use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function Otp_detail(){
        return $this->belongsTo(\App\Otp::class,'id','user_id');
    }

    public function speciality(){
        return $this->hasOne('App\Category','id','speciality_id')->where(['status' => 1]);
    }

    public function qualification(){
        return $this->hasMany('App\DoctorQualification');
    }

    public function mother_language(){
        return $this->hasMany('App\DoctorMotherlanguage');
    }

    public function getUserDetail($userId){
        // dd($userId);
        $data = Self::where(['id' => $userId , 'status' => 1])
            ->with('speciality')
            ->with('qualification')
            ->with('Otp_detail')
            ->with('mother_language')
            ->first();
        return $data;
    }

    public static function getDoctorBySpecialityId($query){
        // dd($query);
        $data = Self::where(['speciality_id' => $query['speciality_id'] , 'status' => $query['status'],'profile_status' => 1])
            ->with('speciality')
            ->with('qualification')
            ->with('Otp_detail')
            ->with('mother_language')
            ->get();
        return $data;
    }

    public static function getUserList($query){
        // dd($query['status']);
        $data = Self::Where(['status' => $query['status'],'user_type' => $query['user_type']])->get();
       return $data;
    }
}
