<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class Appointment extends Model
{
    //

	public function DoctorDetail(){
		return $this->hasOne('\App\User','id','doctor_id');
	}

	public function PatientDetail(){
		return $this->hasOne('\App\User','id','patient_id');
	}

	public function Reffered_To_Doctor_Detail(){
		return $this->hasOne('\App\User','id','reffered_to_doctor_id');
	}

	public function Reffered_By_Doctor_Detail(){
		return $this->hasOne('\App\User','id','rescheduled_by_doctor');
	}

	public static function get_all_appointment_of_patient_by_date($date,$UserDetail, $page_number){

		if($page_number == 0){
			$skip = 0;
		}else{
			$skip = $page_number * 10;
		}
		
		$data = Self::Where(['patient_id' => $UserDetail])
			->whereDate('created_at',$date)
			->with('DoctorDetail','Reffered_To_Doctor_Detail','Reffered_By_Doctor_Detail')
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
			->whereDate('appointment_date','>=',$date)
			->where('status_of_appointment','<>','Accepted')
			->where('status_of_appointment','<>','Rejected')
			->with('PatientDetail','Reffered_To_Doctor_Detail','Reffered_By_Doctor_Detail')
			->with('PatientDetail','Reffered_By_Doctor_Detail')
			->skip($skip)
			->take(10)
			->get();
		return $data;
	}
}
