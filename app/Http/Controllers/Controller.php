<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Mail;
use Log;
use Response;
use Session;
use \Carbon\Carbon;
use App\Category;
use App\Day;
use App\DoctorAvailability;
use App\DoctorMotherlanguage;
use App\DoctorQualification;
use App\Faq;
use App\MotherLanguage;
use App\Otp;
use App\PatientBookmark;
use App\Qualification;
use App\TimeSlot;
use App\User;
use App\UserDetail;
use App\Appointment;
use Hash;
use Auth;
use Exception;
use Config;
use Artisan;

class Controller extends BaseController
{
 	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

 	public function __construct(){
		$timezone = Config::get('app.timezone');
		date_default_timezone_set($timezone);
	}

	public function test_CHECK(){
		Log::info('TEst From Controller');
	}

   public function getUserDetail($data){
   	// dd($data);
   	$result = [];
   	$qualification = [];
   	$DoctorMotherlanguage = [];
   	if($data->user_type == 1){ // doctor
   		if(isset(($data['qualification']))) {
	   		foreach ($data['qualification'] as $key => $value) {
	   			$QualificationDetail = Qualification::Where(['id' => $value->qualification_id])->first();
	   			$qualification[]=[
	   				'id' => $value->id,
	   				'user_id' => $value->user_id,
	   				'qualification_id' => $value->qualification_id,
	   				'qualification_name' => $QualificationDetail['name']
	   			];
	   		}
	   	}
	   	if(isset(($data['mother_language']))) {
	   		foreach ($data['mother_language'] as $key => $value) {
	   			$DoctorMotherlanguageDetail = MotherLanguage::Where(['id' => $value->mother_language_id])->first();
	   			$DoctorMotherlanguage[]=[
	   				'id' => $value->id,
	   				'user_id' => $value->user_id,
	   				'mother_language_id' => $value->mother_language_id,
	   				'mother_language_name' => $DoctorMotherlanguageDetail['name']
	   			];
	   		}
	   	}
   		$doctor_availabilities = DoctorAvailability::Where(['doctor_id' => $data->id])->orderBy('day_id','asc')->get();
   		
   		// dd($doctor_availabilities);
   		$day1 = []; 
			$day2 = []; 
			$day3 = []; 
			$day4 = []; 
			$day5 = []; 
			$day6 = []; 
			$day7 = []; 

			$day1_arr = []; 
			$day2_arr = []; 
			$day3_arr = []; 
			$day4_arr = []; 
			$day5_arr = []; 
			$day6_arr = []; 
			$day7_arr = []; 

			$dates = $this->dates();
			$days = $this->days();

			foreach ($doctor_availabilities as $key => $value) {
				foreach ($days as $key => $value1) {
					if($value1 == 1 && $value->day_id == 1){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])
				   	->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
				   	->where('appointment_date',$dates[$key])
				   	->first();
				   	if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 1 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day1_arr)){
                           array_push($day1_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day1,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day1_arr)){
                           array_push($day1_arr,$busyOrFree->time_slot_id);
                           array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day1_arr)){
                           array_push($day1_arr,$busyOrFree->time_slot_id);
                           array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                     $checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
                     ->first();
                     if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day1_arr)){
                           array_push($day1_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day1,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }

                     if(!in_array($value->time_slot_id, $day1_arr)){
                        array_push($day1_arr,$value->time_slot_id);
                        array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
				   }
				   if($value1 == 2 && $value->day_id == 2){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   	if(!empty($busyOrFree->rescheduled_day_id)){
                        if($busyOrFree->rescheduled_day_id == 2 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                           // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day2_arr)){
                              array_push($day2_arr,$busyOrFree->rescheduled_time_slot_id);
                              array_push($day2,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                           // }
                           if(!in_array($busyOrFree->time_slot_id, $day2_arr)){
                              array_push($day2_arr,$busyOrFree->time_slot_id);
                              array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                           }
                        }else{
                           if(!in_array($busyOrFree->time_slot_id, $day2_arr)){
                              array_push($day2_arr,$busyOrFree->time_slot_id);
                              array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                           }
                        }
                     }else{
                     	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
	                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
	                     ->first();
	                     if($checkReschedule){
	                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day2_arr)){
	                           array_push($day2_arr,$checkReschedule->rescheduled_time_slot_id);
	                           array_push($day2,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
	                        }
	                     }
                        if(!in_array($value->time_slot_id, $day2_arr)){
                           array_push($day2_arr,$value->time_slot_id);
                           array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
				   }
				   if($value1 == 3 && $value->day_id == 3){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
						   	->where('appointment_date',$dates[$key])
						   	->first();
						   if(!empty($busyOrFree->rescheduled_day_id)){
	                     if($busyOrFree->rescheduled_day_id == 3 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
	                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day3_arr)){
	                           array_push($day3_arr,$busyOrFree->rescheduled_time_slot_id);
	                           array_push($day3,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
	                        // }
	                        if(!in_array($busyOrFree->time_slot_id, $day3_arr)){
	                           array_push($day3_arr,$busyOrFree->time_slot_id);
	                           array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                        }
	                     }else{
	                        if(!in_array($busyOrFree->time_slot_id, $day3_arr)){
	                           array_push($day3_arr,$busyOrFree->time_slot_id);
	                           array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                        }
	                     }
	                  }else{
	                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
	                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
	                     ->first();
	                     if($checkReschedule){
	                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day3_arr)){
	                           array_push($day3_arr,$checkReschedule->rescheduled_time_slot_id);
	                           array_push($day3,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
	                        }
	                     }
	                     if(!in_array($value->time_slot_id, $day3_arr)){
	                        array_push($day3_arr,$value->time_slot_id);
	                        array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                     }
	                  }
				   }
				   if($value1 == 4 && $value->day_id == 4){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
						   	->where('appointment_date',$dates[$key])
						   	->first();
						   if(!empty($busyOrFree->rescheduled_day_id)){
	                     if($busyOrFree->rescheduled_day_id == 4 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
	                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day4_arr)){
	                           array_push($day4_arr,$busyOrFree->rescheduled_time_slot_id);
	                           array_push($day4,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
	                        // }
	                        if(!in_array($busyOrFree->time_slot_id, $day4_arr)){
	                           array_push($day4_arr,$busyOrFree->time_slot_id);
	                           array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                        }
	                     }else{
	                        if(!in_array($busyOrFree->time_slot_id, $day4_arr)){
	                           array_push($day4_arr,$busyOrFree->time_slot_id);
	                           array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                        }
	                     }
	                  }else{
	                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
	                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
	                     ->first();
	                     if($checkReschedule){
	                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day4_arr)){
	                           array_push($day4_arr,$checkReschedule->rescheduled_time_slot_id);
	                           array_push($day4,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
	                        }
	                     }
	                     if(!in_array($value->time_slot_id, $day4_arr)){
	                        array_push($day4_arr,$value->time_slot_id);
	                        array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                     }
	                  }
				   }
				   if($value1 == 5 && $value->day_id == 5){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   	if(!empty($busyOrFree->rescheduled_day_id)){
	                     if($busyOrFree->rescheduled_day_id == 5 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
	                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day5_arr)){
	                           array_push($day5_arr,$busyOrFree->rescheduled_time_slot_id);
	                           array_push($day5,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
	                        // }
	                        if(!in_array($busyOrFree->time_slot_id, $day5_arr)){
	                           array_push($day5_arr,$busyOrFree->time_slot_id);
	                           array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                        }
	                     }else{
	                        if(!in_array($busyOrFree->time_slot_id, $day5_arr)){
	                           array_push($day5_arr,$busyOrFree->time_slot_id);
	                           array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                        }
	                     }
	                  }else{
	                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
	                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
	                     ->first();
	                     if($checkReschedule){
	                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day5_arr)){
	                           array_push($day5_arr,$checkReschedule->rescheduled_time_slot_id);
	                           array_push($day5,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
	                        }
	                     }
	                     if(!in_array($value->time_slot_id, $day5_arr)){
	                        array_push($day5_arr,$value->time_slot_id);
	                        array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                     }
	                  }
				   }
				   if($value1 == 6 && $value->day_id == 6){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
				   		->where('appointment_date',$dates[$key])
				   		->first();
				   	if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 6 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day6_arr)){
                           array_push($day6_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day6,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day6_arr)){
                           array_push($day6_arr,$busyOrFree->time_slot_id);
                           array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day6_arr)){
                           array_push($day6_arr,$busyOrFree->time_slot_id);
                           array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
                     ->first();
                     if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day6_arr)){
                           array_push($day6_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day6,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }
                     if(!in_array($value->time_slot_id, $day6_arr)){
                        array_push($day6_arr,$value->time_slot_id);
                        array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
				   }
				   if($value1 == 7 && $value->day_id == 7){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
				   		->where('appointment_date',$dates[$key])
				   		->first();
				   	if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 7 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day7_arr)){
                           array_push($day7_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day7,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day7_arr)){
                           array_push($day7_arr,$busyOrFree->time_slot_id);
                           array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day7_arr)){
                           array_push($day7_arr,$busyOrFree->time_slot_id);
                           array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('rescheduled_date',Carbon::now()->format('Y-m-d'))
                     ->first();
                     if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day7_arr)){
                           array_push($day7_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day7,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }
                     if(!in_array($value->time_slot_id, $day7_arr)){
                        array_push($day7_arr,$value->time_slot_id);
                        array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
			   	}
				}
				// Code Run For Comming Days END

			  

			   if($value->day_id == 1){
               if(Carbon::now()->dayOfWeek+1 == 1){
                 $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])
                 ->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                 ->where('appointment_date',Date('Y-m-d'))
                 ->first();
                  if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 1 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day1_arr)){
                           array_push($day1_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day1,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day1_arr)){
                           array_push($day1_arr,$busyOrFree->time_slot_id);
                           array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day1_arr)){
                           array_push($day1_arr,$busyOrFree->time_slot_id);
                           array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                                 ->where('rescheduled_date',$dates[$key])
                                 ->first();
                  	if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day1_arr)){
                           array_push($day1_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day1,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }
                     if(!in_array($value->time_slot_id, $day1_arr)){
                        array_push($day1_arr,$value->time_slot_id);
                        array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }
            }
			  
			   if($value->day_id == 2){
	            if(Carbon::now()->dayOfWeek+1 == 2){
	              $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
	              ->where('appointment_date',Date('Y-m-d'))
	              ->first();
	              // dd($busyOrFree);
	               if(!empty($busyOrFree->rescheduled_day_id)){
	                  if($busyOrFree->rescheduled_day_id == 2 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
	                     // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day2_arr)){
	                        array_push($day2_arr,$busyOrFree->rescheduled_time_slot_id);
	                        array_push($day2,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
	                     // }
	                     if(!in_array($busyOrFree->time_slot_id, $day2_arr)){
	                        array_push($day2_arr,$busyOrFree->time_slot_id);
	                        array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                     }
	                  }else{
	                     if(!in_array($busyOrFree->time_slot_id, $day2_arr)){
	                        array_push($day2_arr,$busyOrFree->time_slot_id);
	                        array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                     }
	                  }
	               }else{
	               	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('rescheduled_date',$dates[$key])
                     ->first();
                  	if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day2_arr)){
                           array_push($day2_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day2,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }
	                  if(!in_array($value->time_slot_id, $day2_arr)){
	                     array_push($day2_arr,$value->time_slot_id);
	                     array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
	                  }
	               }
	            }
	         }



			  
			   if($value->day_id == 3){
               if(Carbon::now()->dayOfWeek+1 == 3){
                 $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('appointment_date',Date('Y-m-d'))
                     ->first();
                  if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 3 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day3_arr)){
                           array_push($day3_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day3,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day3_arr)){
                           array_push($day3_arr,$busyOrFree->time_slot_id);
                           array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day3_arr)){
                           array_push($day3_arr,$busyOrFree->time_slot_id);
                           array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
							->where('rescheduled_date',$dates[$key])
							->first();
							if($checkReschedule){
								if(!in_array($checkReschedule->rescheduled_time_slot_id, $day3_arr)){
									array_push($day3_arr,$checkReschedule->rescheduled_time_slot_id);
									array_push($day3,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
								}
							}
                     if(!in_array($value->time_slot_id, $day3_arr)){
                        array_push($day3_arr,$value->time_slot_id);
                        array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }
            }


			  
			   if($value->day_id == 4){
               if(Carbon::now()->dayOfWeek+1 == 4){
                  $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                  ->where('appointment_date',Date('Y-m-d'))
                  ->first();
                  if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 4 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day4_arr)){
                           array_push($day4_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day4,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day4_arr)){
                           array_push($day4_arr,$busyOrFree->time_slot_id);
                           array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day4_arr)){
                           array_push($day4_arr,$busyOrFree->time_slot_id);
                           array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                                 ->where('rescheduled_date',$dates[$key])
                                 ->first();
	                  	if($checkReschedule){
                           if(!in_array($checkReschedule->rescheduled_time_slot_id, $day4_arr)){
                              array_push($day4_arr,$checkReschedule->rescheduled_time_slot_id);
                              array_push($day4,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                           }
                        }
                     if(!in_array($value->time_slot_id, $day4_arr)){
                        array_push($day4_arr,$value->time_slot_id);
                        array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }
            }


			   if($value->day_id == 5){
               if(Carbon::now()->dayOfWeek+1 == 5){
                  $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('appointment_date',Carbon::now()->format('Y-m-d'))
                     ->first();
                  if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 5 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day5_arr)){
                           array_push($day5_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day5,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day5_arr)){
                           array_push($day5_arr,$busyOrFree->time_slot_id);
                           array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day5_arr)){
                           array_push($day5_arr,$busyOrFree->time_slot_id);
                           array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                                 ->where('rescheduled_date',$dates[$key])
                                 ->first();
	                  	if($checkReschedule){
                           if(!in_array($checkReschedule->rescheduled_time_slot_id, $day5_arr)){
                              array_push($day5_arr,$checkReschedule->rescheduled_time_slot_id);
                              array_push($day5,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                           }
                        }
                     if(!in_array($value->time_slot_id, $day5_arr)){
                        array_push($day5_arr,$value->time_slot_id);
                        array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }
            }


			
			   if($value->day_id == 6){
               if(Carbon::now()->dayOfWeek+1 == 6){
                 $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('appointment_date',Date('Y-m-d'))
                     ->first();
                 	if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 6 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day6_arr)){
                           array_push($day6_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day6,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day6_arr)){
                           array_push($day6_arr,$busyOrFree->time_slot_id);
                           array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day6_arr)){
                           array_push($day6_arr,$busyOrFree->time_slot_id);
                           array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                                 ->where('rescheduled_date',$dates[$key])
                                 ->first();
                  	if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day6_arr)){
                           array_push($day6_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day6,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }
                     if(!in_array($value->time_slot_id, $day6_arr)){
                        array_push($day6_arr,$value->time_slot_id);
                        array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }
            }

			   
			   if($value->day_id == 7){
               if(Carbon::now()->dayOfWeek+1 == 7){
                 $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                     ->where('appointment_date',Date('Y-m-d'))
                     ->first();
                  if(!empty($busyOrFree->rescheduled_day_id)){
                     if($busyOrFree->rescheduled_day_id == 7 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected' && $busyOrFree->status_of_appointment != 'Completed') {
                        // if(!in_array($busyOrFree->rescheduled_time_slot_id, $day7_arr)){
                           array_push($day7_arr,$busyOrFree->rescheduled_time_slot_id);
                           array_push($day7,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>1]);
                        // }
                        if(!in_array($busyOrFree->time_slot_id, $day7_arr)){
                           array_push($day7_arr,$busyOrFree->time_slot_id);
                           array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }else{
                        if(!in_array($busyOrFree->time_slot_id, $day7_arr)){
                           array_push($day7_arr,$busyOrFree->time_slot_id);
                           array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                        }
                     }
                  }else{
                  	$checkReschedule = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
                                 ->where('rescheduled_date',$dates[$key])
                                 ->first();
                  	if($checkReschedule){
                        if(!in_array($checkReschedule->rescheduled_time_slot_id, $day7_arr)){
                           array_push($day7_arr,$checkReschedule->rescheduled_time_slot_id);
                           array_push($day7,['time_slot_id'=>$checkReschedule->rescheduled_time_slot_id,'busyOrFree'=> 1]);
                        }
                     }
                     if(!in_array($value->time_slot_id, $day7_arr)){
                        array_push($day7_arr,$value->time_slot_id);
                        array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
                     }
                  }
               }
            }
			}

        	if(!empty($day1)){
	        	foreach ($day1 as $key => $value) {
	        		$sort1[] = $value['time_slot_id'];
	        		
	        	}
	        	array_multisort($sort1, SORT_ASC, $day1);
	      }

	      if(!empty($day2)){
	        	foreach ($day2 as $key => $value) {
	        		$sort2[] = $value['time_slot_id'];
	        		
	        	}
        		array_multisort($sort2, SORT_ASC, $day2);
        	}

        	if(!empty($day3)){
	        	foreach ($day3 as $key => $value) {
	        		$sort3[] = $value['time_slot_id'];
	        		
	        	}
	        	array_multisort($sort3, SORT_ASC, $day3);
	      }

	      if(!empty($day4)){
	        	foreach ($day4 as $key => $value) {
	        		$sort4[] = $value['time_slot_id'];
	        		
	        	}
        		array_multisort($sort4, SORT_ASC, $day4);
        	}

        	if(!empty($day5)){
	        	foreach ($day5 as $key => $value) {
	        		$sort5[] = $value['time_slot_id'];
	        		
	        	}
	        	array_multisort($sort5, SORT_ASC, $day5);
	      }

	      if(!empty($day6)){
	        	foreach ($day6 as $key => $value) {
	        		$sort6[] = $value['time_slot_id'];
	        		
	        	}
	        	array_multisort($sort6, SORT_ASC, $day6);
	      }

	      if(!empty($day7)){
	        	foreach ($day7 as $key => $value) {
	        		$sort7[] = $value['time_slot_id'];
	        		
	        	}
	        	array_multisort($sort7, SORT_ASC, $day7);
			}

        	$doctor_availabilities_result = [
			   '1' => $this->filter_data($day1),
			   '2' => $this->filter_data($day2),
			   '3' => $this->filter_data($day3),
			   '4' => $this->filter_data($day4),
			   '5' => $this->filter_data($day5),
			   '6' => $this->filter_data($day6),
			   '7' => $this->filter_data($day7),
        	];

	   	$result = [
	   		'UserIdentificationType' => "Doctor",
	   		'id' => $data['id'],
	   		'firebase_id' => $data['firebase_id'],
	   		'name' => $data['name'],
	   		'email' => $data['email'],
	   		'country_code' => $data['country_code'],
	   		'mobile' => $data['mobile'],
	   		'profile_image' => $data['profile_image'],
	   		'speciality_id' => $data['speciality_id'],
	   		'experience' => $data['experience'],
	   		'working_place' => $data['working_place'],
	   		'latitude' => $data['latitude'],
	   		'longitude' => $data['longitude'],
	   		'about_me' => $data['about_me'],
	   		'remember_token' => $data['remember_token'],
	   		'device_token' => $data['device_token'],
	   		'device_type' => $data['device_type'],
	   		'user_type' => $data['user_type'],
	   		'medical_licence_number' => $data['medical_licence_number'],
	   		'issuing_country' => $data['issuing_country'],
	   		'status' => $data['status'],
	   		'profile_status' => $data['profile_status'],
	   		'notification' => $data['notification'],
	   		'language' => $data['language'],
	   		'speciality' => $data['speciality'],
	   		'otp_detail' => $data['Otp_detail'],
	   		'change_email_otp' => $data['change_email_otp'],
	   		// 'change_email_otp_status' => $data['change_email_otp_status'],
	   		'qualification' => $qualification,
	   		'mother_language' => $DoctorMotherlanguage,
	   		'doctor_availabilities' => $doctor_availabilities_result
	   	];
	   	return $result;
   	}

   	if($data->user_type == 2){ // patient
   		$result = [
   			'UserIdentificationType' => "Patient",
   			'firebase_id' => $data['firebase_id'],
	   		'id' => $data['id'],
	   		'name' => $data['name'],
	   		'email' => $data['email'],
	   		'country_code' => $data['country_code'],
	   		'mobile' => $data['mobile'],
	   		'profile_image' => $data['profile_image'],
	   		'remember_token' => $data['remember_token'],
	   		'device_token' => $data['device_token'],
	   		'device_type' => $data['device_type'],
	   		'user_type' => $data['user_type'],
	   		'status' => $data['status'],
	   		'profile_status' => $data['profile_status'],
	   		'notification' => $data['notification'],
	   		'language' => $data['language'],
	   		'otp_detail' => $data['Otp_detail'],
	   	];
	   	return $result;
   	}
   }

   public function dates(){
   	$dates = [ 
			Carbon::now()->addDay(1)->format('Y-m-d'),
			Carbon::now()->addDay(2)->format('Y-m-d'),
			Carbon::now()->addDay(3)->format('Y-m-d'),
			Carbon::now()->addDay(4)->format('Y-m-d'),
			Carbon::now()->addDay(5)->format('Y-m-d'),
			Carbon::now()->addDay(6)->format('Y-m-d')
		];
		return $dates;
   }

   public function days(){
   	$days = [
			Carbon::now()->addDay(1)->dayOfWeek+1,
			Carbon::now()->addDay(2)->dayOfWeek+1,
			Carbon::now()->addDay(3)->dayOfWeek+1,
			Carbon::now()->addDay(4)->dayOfWeek+1,
			Carbon::now()->addDay(5)->dayOfWeek+1,
			Carbon::now()->addDay(6)->dayOfWeek+1
		];	
		return $days;
   }

	public function filter_data($day){
		$result = [];
		$result1 = [];
		foreach ($day as $key => $value) {
		   $result[$value['time_slot_id']] = $value['busyOrFree'];
		}
		foreach ($result as $key => $value) {
		   $result1[] = ['time_slot_id'=>$key,'busyOrFree'=>$value];
		}
		return $result1;
	}

	public function send_notification($NotificationDataArray){
		// dd($NotificationDataArray);
		$notifyType = 1; // 1 for simple , 2 ExtendChat

		
		if(!empty($NotificationDataArray['Notification_type']) && $NotificationDataArray['Notification_type'] == 2 ){
			$notifyType = 2;
		}

		if(!empty($NotificationDataArray['Notification_type']) && $NotificationDataArray['Notification_type'] == 3 ){
			$notifyType = 3;
		}

		if(!empty($NotificationDataArray['Notification_type']) && $NotificationDataArray['Notification_type'] == 4 ){
			$notifyType = 4;
		}

		if(!empty($NotificationDataArray['appointment_id'])) {
			$appointment_id = $NotificationDataArray['appointment_id'];
		}else{
			$appointment_id = null;
		}

		$userId = (int) $NotificationDataArray['getter_id'];
		$bodyText = [
			'message'=>$NotificationDataArray['message'],
			'appointment_id' => $appointment_id,
		];
		$this->notification($userId,$bodyText,$notifyType);
	}

	public function notification($userId , $body_text, $notifyType){

	  	$data = DB::table('users')
	  			->where('id',$userId)
	  			->get();
	  	// dd($data);
	  	Log::info(print_r($data,True));		
	  	// dd($data);
	  	if(count($data)){
	      $notification_type = $notifyType;
	      $id = $data[0]->id;
	      $notificationobject = new NotificationController();
	      // $tokens[] = $data[0]->device_token;
	      $tokens = $data[0]->device_token;
	      $device_type = $data[0]->device_type;
	      // dd($body_text);

	      Log::info(print_r('notification_type '.$notification_type,True));
	      Log::info(print_r('id '.$id,True));
	      Log::info(print_r('device_token '.$data[0]->device_token,True));
	      Log::info(print_r('device_type '.$data[0]->device_type,True));



	      if($device_type == "0"){
				$status = $notificationobject->androidPushNotification($body_text,$notification_type,$tokens,$id);
				Log::info(print_r('status '.$status,True));
				// return $status;

	      }else if($device_type == "1"){
				$status = $notificationobject->iosPushNotification($body_text,$notification_type,$tokens,$id);
				Log::info(print_r('status '.$status,True));
				// return $status;
	      }
	  	}
	}

	public function updateDeviceToken(Request $request){
		Log::info('----------------------Controller--------------------------updateDeviceToken'.print_r($request->all(),True));
		$accessToken =  $request->header('accessToken');
		$device_token = $request->device_token;
		$locale = $request->header('locale');
		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
		if(empty($locale)){
			$locale = 'en';
		}
		if(!empty($locale)){
			\App::setLocale($locale);
			if( !empty( $accessToken ) ) {
				$user = new \App\User;
				$userDetail = User::where(['remember_token' => $accessToken])->first();
				if(count($userDetail)){
					if($device_token){
		    			$userDetail->device_token = $device_token;
		    			$userDetail->save();
		    			$Response = [
		    			  'message'  => trans('messages.success.success'),
		    			];
		        		return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );	
		        	}else{
		        		$Response = [
		    			  'message'  => "device token is required.",
		    			];
		        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
		        	}
				}else{
					$response['message'] = trans('messages.invalid.detail');
					return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
				}
			} else {
		    	$Response = [
				  'message'  => trans('messages.required.accessToken'),
				];
		      return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function setTimeZone($timezone){
		/*config(['app.timezone' => 'America/Chicago']);
   	$timezone = Config::get('app.timezone');
		date_default_timezone_set($timezone);*/
		date_default_timezone_set($timezone);
	}

	public function test(Request $request){
		$exitCode = Artisan::call('gaurav:command');
		dd($exitCode);
	}
}
