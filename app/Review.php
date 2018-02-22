<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\User;

class Review extends Model
{
    //
	protected $fillable = ['patient_id','appointment_id','doctor_id','rating'];

	public function patient_detail(){
		return $this->belongsTo(User::class,'patient_id');
	}

	public static function get_ranking($doctor_id){
		$data = Self::where(['doctor_id' => $doctor_id])
			->with('patient_detail')
			->get();
		return $data;
	}
}
