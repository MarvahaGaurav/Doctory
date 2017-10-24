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
        'password', 'remember_token',
    ];

    public function OtpDetail(){
        return $this->belongsTo(\App\Otp::class,'id','userId');
    }

    public function getUserDetail($userId){
        $data = Self::where('id',$userId)->with('OtpDetail')->first();
        return $data;
    }
}
