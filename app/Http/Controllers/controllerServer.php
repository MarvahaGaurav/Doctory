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

class Controller extends BaseController
{
 	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
			$dates = $this->dates();
			$days = $this->days();

			foreach ($doctor_availabilities as $key => $value) {
				foreach ($days as $key => $value1) {
					if($value1 == 1 && $value->day_id == 1){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])
					   	->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   	->where('appointment_date',$dates[$key])
					   	->first();
					      if($busyOrFree){
						      array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						      if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 1 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day1,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}else{
							   		array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
								   }
					  			}
						   }else{
					   		array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
						   }
				   }
				   if($value1 == 2 && $value->day_id == 2){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   	if($busyOrFree){
					     		array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					     		if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 2 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day2,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}else{
					      			array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
						      	}
					  			}
					     	}else{
			      			array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
				      	}
				   }
				   if($value1 == 3 && $value->day_id == 3){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
						   	->where('appointment_date',$dates[$key])
						   	->first();
						   if($busyOrFree){
						   	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						   	if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 3 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day3,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}else{
										array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);   		
								   }
					  			}
						   }else{
								array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);   		
						   }
				   }
				   if($value1 == 4 && $value->day_id == 4){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
						   	->where('appointment_date',$dates[$key])
						   	->first();
						   if($busyOrFree){
				       		array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				       		if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 4 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day4,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}else{
					       			array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
						       	}
					  			}
				       	}else{
			       			array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
				       	}
				   }
				   if($value1 == 5 && $value->day_id == 5){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   	if($busyOrFree){
					     		array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					     		if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 5 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day5,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}else{
							      	array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								   }
					  			}
					     	}else{
				   			array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
					   	}
				   }
				   if($value1 == 6 && $value->day_id == 6){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   	if($busyOrFree){
				       		array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				       		if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 6 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day6,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}
					  			}else{
				      			array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
					      	}
				       	}else{
			      			array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
				      	}
				   }
				   if($value1 == 7 && $value->day_id == 7){
					   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   	if($busyOrFree){
				      		array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				      		if(!empty($busyOrFree->rescheduled_day_id)){
							   	if($busyOrFree->rescheduled_day_id == 7 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
							   		array_push($day7,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
							   	}
					  			}else{
				      			array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
					      	}
				      	}else{
				      		$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'rescheduled_time_slot_id'=>$value->time_slot_id,'rescheduled_day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   		->where('appointment_date',$dates[$key])
					   		->first();
					   		if($busyOrFree){
			      				array_push($day7,['time_slot_id'=>$value->rescheduled_time_slot_id,'busyOrFree'=>1]);
			      			}else{
			      				array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
			      			}
				      	}
			   	}
				}
				// Code Run For Comming Days END





			   if($value->day_id == 1){
			   	if(Carbon::now()->dayOfWeek+1 == 1){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])
				   	->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
				   	->where('appointment_date',Carbon::now()->addDay(1)->format('Y-m-d'))
				   	->first();
				      // array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			       	if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 1 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day1,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   		// dd($busyOrFree);
					   	}else{
					      	array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						   }
					   }else{
				      	array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }
				   }
			   }
			   if($value->day_id == 2){
			   	if(Carbon::now()->dayOfWeek+1 == 2){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
				   		->where('appointment_date',Date('Y-m-d'))
				   		->first();
				      // array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				      if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 2 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day2,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   	}
					   }else{
				      	array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }
				   }else{
			      	array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				   }
			   }
			   if($value->day_id == 3){
			   	if(Carbon::now()->dayOfWeek+1 == 3){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   	->where('appointment_date',Date('Y-m-d'))
					   	->first();
			       	// array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			       	if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 3 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day3,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   	}
					   }else{
				      	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }
			      }else{
			      	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				   }
			   }
			   if($value->day_id == 4){
			   	if(Carbon::now()->dayOfWeek+1 == 4){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					   	->where('appointment_date',Date('Y-m-d'))
					   	->first();
			       	// array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			       	if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 4 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day4,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   	}else{
					      	array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						   }
					   }else{
				      	array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }
			      }
			   }
			   if($value->day_id == 5){
			   	if(Carbon::now()->dayOfWeek+1 == 5){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
				   		->where('appointment_date',Date('Y-m-d'))
				   		->first();
				      if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 5 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day5,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   	}else{
					      	array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						   }
					   }else{
				      	array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						}
				   }
			   }
			   if($value->day_id == 6){
			   	if(Carbon::now()->dayOfWeek+1 == 6){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
				   		->where('appointment_date',Date('Y-m-d'))
				   		->first();
			       	// array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			       	if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 6 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day6,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   	}else{
					      	array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						   }
					   }else{
				      	array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }
			      }
			   }
			   if($value->day_id == 7){
			   	if(Carbon::now()->dayOfWeek+1 == 7){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
				   		->where('appointment_date',Date('Y-m-d'))
				   		->first();
			      	// array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			      	if(!empty($busyOrFree->rescheduled_day_id)){
					   	if($busyOrFree->rescheduled_day_id == 7 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
					   		array_push($day7,['time_slot_id'=>$busyOrFree->rescheduled_time_slot_id,'busyOrFree'=>'1']);
					   	}else{
					      	array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
						   }
					   }else{
				      	array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }
			      }
			   }
			}

			// dd($day5);
			$doctor_availabilities_result = [
			   '1' => $day1,
			   '2' => $day2,
			   '3' => $day3,
			   '4' => $day4,
			   '5' => $day5,
			   '6' => $day6,
			   '7' => $day7,
        	];

	   	$result = [
	   		'UserIdentificationType' => "Doctor",
	   		'id' => $data['id'],
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
	   		'qualification' => $qualification,
	   		'mother_language' => $DoctorMotherlanguage,
	   		'doctor_availabilities' => $doctor_availabilities_result
	   	];
	   	return $result;
   	}

   	if($data->user_type == 2){ // patient
   		$result = [
   			'UserIdentificationType' => "Patient",
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

}
