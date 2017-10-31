<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    //

	public function DoctorDetail(){
		return $this->hasOne('\App\User','id','doctor_id');
	}

	public function PatientDetail(){
		return $this->hasOne('\App\User','id','doctor_id');
	}

	public static function get_all_appointment_of_patient_by_date($date,$UserDetail, $page_number){

		if($page_number == 0){
			$skip = 0;
		}else{
			$skip = $page_number * 10;
		}
		
		$data = Self::Where(['patient_id' => $UserDetail])
			->whereDate('created_at',$date)
			->with('DoctorDetail')
			->skip($skip)
			->take(10)
			->get();
		return $data;
	}

	public static function get_all_appointment_of_doctor_by_date($date,$UserDetail,$page_number){
		if($page_number == 0){
			$skip = 0;
		}else{
			$skip = $page_number * 10;
		}
		$data = Self::Where(['doctor_id' => $UserDetail])
			->whereDate('created_at',$date)
			->with('PatientDetail')
			->skip($skip)
			->take(10)
			->get();
		return $data;
	}
}
