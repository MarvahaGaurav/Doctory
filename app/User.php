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

    public function OtpDetail(){
        return $this->belongsTo(\App\Otp::class,'id','userId');
    }

    public function getUserDetail($userId){
        $data = Self::where(['id' => $userId , 'status' => 1])->with('OtpDetail')->first();
        return $data;
    }

    public static function getUserList($query){
        // dd($query['status']);
        $data = Self::Where(['status' => $query['status'],'user_type' => $query['user_type']])->get();
       return $data;
    }
}
