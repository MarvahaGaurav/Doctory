<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Mail;
use Illuminate\Validation\Rule;
use Log;
use Response;
use Session;
use \Carbon\Carbon;
use App\User;
use App\Otp;
use App\UserDetail;
use App\Category;
use App\TimeSlot;
use App\Day;
use App\MotherLanguage;
use App\PatientBookmark;
use App\DoctorAvailability;
use App\Qualification;
use App\DoctorQualification;
use App\Appointment;
use App\Notification;
use Hash;
use Auth;
use Exception;

class PatientController extends Controller
{
 	public function bookmark_UnBookMark_Doctor(Request $request){
		Log::info('----------------------PatientController--------------------------bookmark_UnBookMark_Doctor'.print_r($request->all(),True));
 		
		$accessToken = $request->header('accessToken');
		$patient_id = $request->patient_id;
		$doctor_id = $request->doctor_id;
		$key = $request->key; // 1 for bookmark or 0 for un bookmark
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

    	if( !empty( $accessToken ) ) {
    		$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
    			$validations = [
					'patient_id' => 'required|numeric',
					'doctor_id' => 'required|numeric',
					'key' => 'required|numeric'
		    	];
		    	$validator = Validator::make($request->all(),$validations);
		    	if($validator->fails()){
		    		$response = [
						'message' => $validator->errors($validator)->first()
					];
					return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    	}else{
		    		$patient_detail = User::where(['id'=>$patient_id,'user_type'=>2])->first();
		    		$doctor_detail = User::where(['id'=>$doctor_id,'user_type'=>1])->first();
		    		if($doctor_detail && $patient_detail ){
		    			$alreadyBookMarked = PatientBookmark::where(['doctor_id' => $doctor_id , 'patient_id' => $patient_id])->first();
		    			if($key == 1 ){
		    				if(!$alreadyBookMarked){
		    					$PatientBookmark = new PatientBookmark;
				    			$PatientBookmark->patient_id = $patient_id;
				    			$PatientBookmark->doctor_id = $doctor_id;
				    			$PatientBookmark->save();
		    				}
		    			}
		    			if($key == 0 && count($alreadyBookMarked)){
		    				PatientBookmark::where(['doctor_id' => $doctor_id , 'patient_id' => $patient_id])->delete();
		    			}
		    			$Response = [
							'message'  => trans('messages.success.success'),
						];
				      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    		}else {
				    	$Response = [
							'message'  => trans('messages.invalid.request'),
						];
				      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
				   }
		    	}
    		}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
    	}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function get_patient_bookmarks_doctors(Request $request){
 		Log::info('------------------PatientController------------get_patient_bookmarks_doctors'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$result = [];
 		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

		if( !empty( $accessToken ) ) {
			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
				Log::info('UserDetail'.print_r($UserDetail,True));
    			if($UserDetail->user_type == 2){
    				$PatientBookmark = PatientBookmark::where(['patient_id' => $UserDetail->id])->get();
    				$User = new User;
    				foreach ($PatientBookmark as $key => $value) {
    					// dd($value->doctor_id);
				    	$result[] = $this->getUserDetail($User->getUserDetail($value->doctor_id));
    				}
    				$Response = [
						'message'  => trans('messages.success.success'),
						'response' => $result
					];
			      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
    			}else{
			    	$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE'));
    			}
			}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
 	}

 	public function schedule_appointment_with_doctor(Request $request){
		Log::info('----------------------PatientController--------------------------schedule_appointment_with_doctor'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$patient_id = $request->patient_id;
 		$patient_age = $request->patient_age;
 		$patient_gender = $request->patient_gender;
 		$question = $request->question;
 		$previous_illness_desc = $request->previous_illness_desc;
		$doctor_id = $request->doctor_id;
		$time_slot_id = $request->time_slot_id;
		// $day_id = $request->day_id;
		$appointment_date = $request->appointment_date;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			// dd($UserDetail);
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'patient_id' => 'required|numeric',
						'doctor_id' => 'required|numeric',
						'time_slot_id' => 'required|numeric',
						// 'day_id' => 'required|numeric',
						'appointment_date' => 'required|date_format:"Y-m-d"'
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			if($UserDetail->id == $patient_id){
			    			$appointment_date_from_user = Carbon::parse($appointment_date);
			    			if($appointment_date_from_user >= Carbon::now()->format('Y-m-d')){
			    				$day_id = Carbon::parse($appointment_date)->dayOfWeek+1;
				    			$AppointmentData =[
				    				'patient_id' => $patient_id,
				    				'patient_age' => $patient_age,
				    				'patient_gender' => $patient_gender,
				    				'question' => $question,
				    				'previous_illness_desc' => $previous_illness_desc,
				    				'doctor_id' => $doctor_id,
				    				'time_slot_id' => $time_slot_id,
				    				'day_id' => $day_id,
				    				'appointment_date' => $appointment_date
				    			]; 
				    			$check_doctor_availability = DoctorAvailability::where(['doctor_id' => $doctor_id,'day_id' => $day_id ,'time_slot_id' => $time_slot_id])->first();
				    			// dd($check_doctor_availability);
				    			if($check_doctor_availability){
					    			$Already_Busy_Time_Slot_With_Other_Patient = Appointment::where(['doctor_id' => $doctor_id,'time_slot_id' => $time_slot_id,'day_id' => $day_id])
					    			->where('patient_id','<>',$patient_id)
					    			->where('appointment_date','=',$appointment_date_from_user)
					    			->where('status_of_appointment','<>','Rejected')
					    			->first();
					    			// dd($Already_Busy_Time_Slot_With_Other_Patient);
					    			if(!$Already_Busy_Time_Slot_With_Other_Patient){
					    				$already_booked = Appointment::where([
						    				'patient_id' => $patient_id,
						    				'doctor_id' => $doctor_id,
						    				'time_slot_id' => $time_slot_id,
						    				'day_id' => $day_id,
						    				'appointment_date' => $appointment_date_from_user
						    				])
					    				->where('status_of_appointment','<>','Rejected')
					    				->first();
					    				if(!$already_booked){
					    					if(!Carbon::parse($appointment_date)->isToday())
					    					{
							    				$appontmentId = Appointment::insertGetId($AppointmentData);
							    				Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'type'=>__('messages.notification_status_codes.Scheduled_Appointment'),'appointment_id' => $appontmentId]);
							    				$result = Appointment::find($appontmentId);
								    			$Response = [
													'message'  => trans('messages.success.appointment_scheduled'),
													'response' => $result
												];
										      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
										   }else{
										   	if(TimeSlot::find($time_slot_id)->start_time > Carbon::now()->format('h:i:s')){
										   		$appontmentId = Appointment::insertGetId($AppointmentData);
								    				Notification::insert(['doctor_id' => $doctor_id,'patient_id'=>$patient_id,'type' => __('messages.notification_status_codes.Scheduled_Appointment'),'appointment_id' => $appontmentId]);
								    				$result = Appointment::find($appontmentId);
									    			$Response = [
														'message'  => trans('messages.success.appointment_scheduled'),
														'response' => $result
													];
											      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
										   	}else{
										   		$Response = [
														'message'  => trans('messages.invalid.request'),
													];
											      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
										   	}
										   }
						    			}else{
						    				$Response = [
												'message'  => trans('messages.Patient_Already_Booked_appointment'),
											];
									      return Response::json( $Response , __('messages.statusCode.ALREADY_EXIST') );
						    			}
					    			}else{
						    				$Response = [
												'message'  => trans('messages.Already_Busy_Time_Slot_With_Other_Patient')
											];
									      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
					    			}
					    		}else{
					    			$response = [
										'message' => __('messages.invalid.doctor_not_available_at_this_time_slot')
									];
									return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
					    		}
				    		}else{
				    			$response = [
									'message' => __('messages.invalid.appointment_date')
								];
								return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
				    		}
				    	}else{
			    			$Response = [
			    			  'message'  => trans('messages.invalid.request'),
			    			];
			        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
    					}
		    		}
 				}else{
 					$response=[
						'message' => trans('messages.invalid.request'),
		      	];
		     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
 				}
 			}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
 		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function get_all_appointment_of_patient_by_date(Request $request){
 		Log::info('----------------------PatientController--------------------------get_all_appointment_of_patient_by_date'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$date = date('Y-m-d',strtotime($request->date));
	   $page_number = $request->page_number;
 		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){
	 				$validations = [
						'date' => 'required',
						// 'page_number' => 'required|numeric'
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			// $result = Appointment::get_all_appointment_of_patient_by_date($date,$UserDetail->id,$page_number);
		    			$result = Appointment::get_all_appointment_of_patient_by_date($date,$UserDetail->id);
		    			$final_result = [];
		    			$day1 = []; 
						$day2 = []; 
						$day3 = []; 
						$day4 = []; 
						$day5 = []; 
						$day6 = []; 
						$day7 = []; 
						$qualificationArr = [];
		    			foreach ($result as $key => $res) {
		    				$dates = [ 
								Carbon::now()->addDay(1)->format('Y-m-d'),
								Carbon::now()->addDay(2)->format('Y-m-d'),
								Carbon::now()->addDay(3)->format('Y-m-d'),
								Carbon::now()->addDay(4)->format('Y-m-d'),
								Carbon::now()->addDay(5)->format('Y-m-d'),
								Carbon::now()->addDay(6)->format('Y-m-d')
							];
							$days = [
								Carbon::now()->addDay(1)->dayOfWeek+1,
								Carbon::now()->addDay(2)->dayOfWeek+1,
								Carbon::now()->addDay(3)->dayOfWeek+1,
								Carbon::now()->addDay(4)->dayOfWeek+1,
								Carbon::now()->addDay(5)->dayOfWeek+1,
								Carbon::now()->addDay(6)->dayOfWeek+1
							];
							// dd($dates);
							// dd($days);
							// dd($res->doctor_availability);
							foreach ($res->doctor_availability as $key => $value) {
								foreach ($days as $key => $value1) {
									if($value1 == 1 && $value->day_id == 1){
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])
									   	->where('status_of_appointment','<>','rejected')
									   	->where('appointment_date',$dates[$key])
									   	->first();
									      if($busyOrFree){
										      array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
										   }else{
										   	array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
										   }
								   }
								   if($value1 == 2 && $value->day_id == 2){
								   	// dd($value->doctor_id);
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
									   		->where('appointment_date',$dates[$key])
									   		->first();
									   	// dd($busyOrFree);
									   	if($busyOrFree){
									     		array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
									     	}else{
								      		array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
								      	}
								   }
								   if($value1 == 3 && $value->day_id == 3){

									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
										   	->where('appointment_date',$dates[$key])
										   	->first();
										   if($busyOrFree){
										   	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
										   }else{
												array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);   		
										   }
								   }
								   if($value1 == 4 && $value->day_id == 4){

									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
										   	->where('appointment_date',$dates[$key])
										   	->first();
										   if($busyOrFree){
								       		array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								       	}else{
								       		array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
								       	}
								   }
								   if($value1 == 5 && $value->day_id == 5){
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
									   		->where('appointment_date',$dates[$key])
									   		->first();
									   	if($busyOrFree){
									     		array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
									     	}else{
									   		array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
									   	}
								   }
								   if($value1 == 6 && $value->day_id == 6){
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
									   		->where('appointment_date',$dates[$key])
									   		->first();
									   	if($busyOrFree){
								       		array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								       	}else{
								      		array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
								      	}
								   }
								   if($value1 == 7 && $value->day_id == 7){
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
									   		->where('appointment_date',$dates[$key])
									   		->first();
									   	if($busyOrFree){
								      		array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								      	}else{
								      		array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
								      	}
							   	}
								}

							   if($value->day_id == 1){
							   	if(Carbon::now()->dayOfWeek+1 == 1){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])
								   	->where('status_of_appointment','<>','rejected')
								   	->where('appointment_date',Carbon::now()->addDay(1)->format('Y-m-d'))
								   	->first();
								       array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								   }
							   }
							   if($value->day_id == 2){
							   	if(Carbon::now()->dayOfWeek+1 == 2){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
								   		->where('appointment_date',Date('Y-m-d'))
								   		->first();
								      array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								   }
							   }
							   if($value->day_id == 3){
							   	if(Carbon::now()->dayOfWeek+1 == 3){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
									   	->where('appointment_date',Date('Y-m-d'))
									   	->first();
							       	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
							      }
							   }
							   if($value->day_id == 4){
							   	if(Carbon::now()->dayOfWeek+1 == 4){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
									   	->where('appointment_date',Date('Y-m-d'))
									   	->first();
							       	array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
							      }
							   }
							   if($value->day_id == 5){
							   	if(Carbon::now()->dayOfWeek+1 == 5){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
								   		->where('appointment_date',Date('Y-m-d'))
								   		->first();
								      array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
								   }
							   }
							   if($value->day_id == 6){
							   	if(Carbon::now()->dayOfWeek+1 == 6){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
								   		->where('appointment_date',Date('Y-m-d'))
								   		->first();
							       	array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
							      }
							   }
							   if($value->day_id == 7){
							   	if(Carbon::now()->dayOfWeek+1 == 7){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
								   		->where('appointment_date',Date('Y-m-d'))
								   		->first();
							      	array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
							      }
							   }
							}
							$doctor_availabilities_result = [
							   '1' => $day1,
							   '2' => $day2,
							   '3' => $day3,
							   '4' => $day4,
							   '5' => $day5,
							   '6' => $day6,
							   '7' => $day7,
				        	];
				        	$qualification = DoctorQualification::where(['user_id'=>$res->DoctorDetail->id])->get();
				        	$qualificationArr = [];
				        	foreach ($qualification as $key => $value) {
				   			$QualificationDetail = Qualification::Where(['id' => $value->qualification_id])->first();
				   			$qualificationArr[]=[
				   				'id' => $value->id,
				   				'user_id' => $value->user_id,
				   				'qualification_id' => $value->qualification_id,
				   				'qualification_name' => $QualificationDetail['name']
				   			];
				   		}
				        	$DoctorDetail = [
				        		'id' => $res->DoctorDetail->id,
				        		'name' => $res->DoctorDetail->name,
				        		'email' => $res->DoctorDetail->email,
				        		'country_code' => $res->DoctorDetail->country_code,
				        		'mobile' => $res->DoctorDetail->mobile,
				        		'profile_image' => $res->DoctorDetail->profile_image,
				        		'speciality_id' => $res->DoctorDetail->speciality_id,
				        		'experience' => $res->DoctorDetail->experience,
				        		'working_place' => $res->DoctorDetail->working_place,
				        		'latitude' => $res->DoctorDetail->latitude,
				        		'about_me' => $res->DoctorDetail->about_me,
				        		'remember_token' => $res->DoctorDetail->remember_token,
				        		'device_token' => $res->DoctorDetail->device_token,
				        		'device_type' => $res->DoctorDetail->device_type,
				        		'user_type' => $res->DoctorDetail->user_type,
				        		'medical_licence_number' => $res->DoctorDetail->medical_licence_number,
				        		'issuing_country' => $res->DoctorDetail->issuing_country,
				        		'status' => $res->DoctorDetail->status,
				        		'profile_status' => $res->DoctorDetail->profile_status,
				        		'notification' => $res->DoctorDetail->notification,
				        		'language' => $res->DoctorDetail->language,
				        		'created_at' => $res->DoctorDetail->created_at,
				        		'updated_at' => $res->DoctorDetail->updated_at,
				        		'doctor_availabilities' => $doctor_availabilities_result,
				        		'qualification' => $qualificationArr
				        	];
		    				$final_result[] = [
		    					'id' => $res->id,
		    					'patient_id' => $res->patient_id,
		    					'patient_age' => $res->patient_age,
		    					'patient_gender' => $res->patient_gender,
		    					'question' => $res->question,
		    					'previous_illness_desc' => $res->previous_illness_desc,
		    					'doctor_id' => $res->doctor_id,
		    					'time_slot_id' => $res->time_slot_id,
		    					'day_id' => $res->day_id,
		    					'appointment_date' => $res->appointment_date,
		    					'status_of_appointment' => $res->status_of_appointment,
		    					'reffered_to_doctor_id' => $res->reffered_to_doctor_id,
		    					'rescheduled_by_doctor' => $res->rescheduled_by_doctor,
		    					'rescheduled_time_slot_id' => $res->rescheduled_time_slot_id,
		    					'rescheduled_day_id' => $res->rescheduled_day_id,
		    					'rescheduled_date' => $res->rescheduled_date,
		    					'rescheduled_by_patient' => $res->rescheduled_by_patient,
		    					'doctor_detail' => $DoctorDetail,
		    					'reffered__to__doctor__detail' => $res->reffered__to__doctor__detail,
		    				];
		    			}
		    			$Response = [
							'message'  => trans('messages.success.success'),
							'response' => $final_result
						];
				      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    		}
		    	}else{
		    		$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
		    	}
 			}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function accept_or_reject_appointment_by_patient_rescheduled_by_doctor(Request $request){
 		Log::info('----------------------PatientController--------------------------accept_or_reject_appointment_by_patient_rescheduled_by_doctor'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$appointment_id = $request->appointment_id;
 		$doctor_id = $request->doctor_id;
 		$time_slot_id = $request->time_slot_id;
		$day_id = $request->day_id;
		$accept_or_reject = $request->accept_or_reject;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'appointment_id' => 'required|numeric',
						'doctor_id' => 'required|numeric',
						'time_slot_id' => 'required|numeric',
						'day_id' => 'required|numeric',
						'accept_or_reject' => 'required|alpha',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$AppointmentDetail = Appointment::find($appointment_id);
		    			// dd($AppointmentDetail);
		    			if($AppointmentDetail && $AppointmentDetail->doctor_id == $doctor_id && $AppointmentDetail->rescheduled_by_doctor == 1)
		    			{
		    				$appointmentDateInDb = Carbon::parse($AppointmentDetail->appointment_date)->format('Y-m-d');
		    				if($appointmentDateInDb >= Carbon::now()->format('Y-m-d')){
			    				$Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
								$Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
								$Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;
								if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') )
								{
                                    
                                    
                                    if($accept_or_reject == 'Accepted'){
                                    	$AppointmentDetail->status_of_appointment = $accept_or_reject;
                                    	$AppointmentDetail->time_slot_id = $AppointmentDetail->rescheduled_time_slot_id;

                                    	$AppointmentDetail->day_id = $AppointmentDetail->rescheduled_day_id;
                                    	
                                    	$AppointmentDetail->appointment_date = $AppointmentDetail->rescheduled_date;


                                    	$AppointmentDetail->save();

                                    	Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Rescheduled_Appointment_Accepted_By_Patient'),'appointment_id' => $appointment_id,'appointment_status'=>'Accepted']);

                                        $Response = [
                                            'message'  => trans('messages.success.appointment_accepted'),
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
                                    if($accept_or_reject == 'Rejected'){
                                    	$AppointmentDetail->status_of_appointment = 'Rejected by patient';
                                    	Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Rescheduled_Appointment_Rejected_By_Patient'),'appointment_id' => $appointment_id,'appointment_status'=>'Rejected']);

                                    	$AppointmentDetail->save();
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rejected'),
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }
								}else{
								   $AppointmentDetail->status_of_appointment = "Expired";
								   $AppointmentDetail->save();
								   $response = [
								       'message' => __('messages.invalid.appointment_expired')
								   ];
								   return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
								}
			    			}else{
								$response = [
								   'message' => __('messages.invalid.appointment_expired')
								];
								return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
							}	
		    				
			    		}else{
			    			$response = [
								'message' => __('messages.success.NO_DATA_FOUND')
							];
							return response()->json($response,trans('messages.statusCode.NO_DATA_FOUND'));
			    		}
		    		}
		    	}else{
 					$response=[
						'message' => trans('messages.invalid.request'),
		      	];
		     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
 				}
		   }else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
 	}

 	public function cancel_appointment_by_patient(Request $request){
   	Log::info('----------------------PatientController--------------------------cancel_appointment_by_patient'.print_r($request->all(),True));
 		$accessToken = $request->header('accessToken');
 		$appointment_id = $request->appointment_id;

 		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			// dd($UserDetail);
 			if(count($UserDetail)){
 				if($UserDetail->user_type == 2){ // for Patient Only
 					$validations = [
						'appointment_id' => 'required|numeric',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			// dd('else');
		    			$AppointmentDetail = Appointment::find($appointment_id);
		    			// dd($AppointmentDetail);
		    			if($AppointmentDetail){
		    				// dd($AppointmentDetail->appointment_date);
		    				$appointmentDateInDb = Carbon::parse($AppointmentDetail->appointment_date)->format('Y-m-d');
		    				// dd($appointmentDateInDb>= Carbon::now()->format('Y-m-d'));
		    				if($appointmentDateInDb >= Carbon::now()->format('Y-m-d')){
		    					// dd($AppointmentDetail);
			    				$Time_slot_detail = TimeSlot::find($AppointmentDetail->time_slot_id);
								$Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
								$Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;
								// dd(Carbon::parse($appointmentDateInDb)->isToday());
								// dd($Appointment_TimeSlot_StartTime);
								if(Carbon::parse($appointmentDateInDb)->isToday()){
									if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') ){
										// dd($AppointmentDetail->doctor_id);
	                        	$AppointmentDetail->status_of_appointment = 'Cancelled';
	                        	$AppointmentDetail->save();

	                        	Notification::insert(['doctor_id'=>$AppointmentDetail->doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Appointment_Cancelled_By_Patient'),'appointment_id' => $appointment_id,'appointment_status'=>'Cancelled']);

	                            $Response = [
	                                'message'  => trans('messages.appointment_status.Appointment_Cancelled_By_Patient'),
	                            ];
	                            return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
									}else{
									   $AppointmentDetail->status_of_appointment = "Expired";
									   $AppointmentDetail->save();
									   $response = [
									       'message' => __('messages.invalid.appointment_expired')
									   ];
									   return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
									}
								}else{
									$AppointmentDetail->status_of_appointment = 'Cancelled';
                        	$AppointmentDetail->save();
                        	Notification::insert(['doctor_id'=>$AppointmentDetail->doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Appointment_Cancelled_By_Patient'),'appointment_id' => $appointment_id,'appointment_status'=>'Cancelled']);
									$Response = [
									'message'  => trans('messages.appointment_status.Appointment_Cancelled_By_Patient'),
									];
									return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
								}
			    			}else{
								$response = [
								   'message' => __('messages.invalid.appointment_expired')
								];
								return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
							}	
			    		}else{
			    			$response = [
								'message' => __('messages.success.NO_DATA_FOUND')
							];
							return response()->json($response,trans('messages.statusCode.NO_DATA_FOUND'));
			    		}
		    		}
		    	}else{
 					$response=[
						'message' => trans('messages.invalid.request'),
		      	];
		     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
 				}
		   }else{
    			$Response = [
    			  'message'  => trans('messages.invalid.detail'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
   }

 	public function search_doctor(Request $request){
 		Log::info('------------------PatientController------------search_doctor');
 		$accessToken = $request->header('accessToken');
 		$name = $request->name;
 		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		
 		if( !empty( $accessToken ) ) {
			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
				Log::info('UserDetail'.print_r($UserDetail,True));
    			if($UserDetail->user_type == 2){
    				$validations = [
						'name' => 'required',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
							'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    		}else{
		    			$query = [
		    				'user_type' => 1,
		    				'profile_status' => 1,
		    				'name' => $name
		    			];
		    			$doctor_detail = User::get_doctor_by_search_query($query);
		    			// dd(count($doctor_detail));
		    			$result = [];
		    			foreach ($doctor_detail as $key => $value) {
		    				$result[] = $this->getUserDetail($value);
		    			}
		    			$Response = [
							'message'  => trans('messages.success.success'),
							'response' => $result
						];
				      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE'));
		    		}
    			}else{
			    	$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE'));
    			}
    		}else{
    			$Response = [
    			  'message'  => trans('messages.invalid.credentials'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
    	}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken')
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
 	}

 	public function get_notification_list(Request $request){
 		Log::info('------------------PatientController------------get_notification_list');
 		$accessToken = $request->header('accessToken');
 		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {
			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
    		if(count($UserDetail)){
				Log::info('UserDetail'.print_r($UserDetail,True));
    			if($UserDetail->user_type == 2){
	    			$Notification = Notification::where(['patient_id'=>$UserDetail->id])->where('type','<>',2)->get();
	    			$result = [];
	    			foreach ($Notification as $key => $value) {
	    				$drName = User::where(['id'=>$value->doctor_id])->select('name')->first()->name;
	    				$patient_Name = User::where(['id'=>$value->patient_id])->select('name')->first()->name;
	    				$Appointment = Appointment::find($value->appointment_id);
                        // dd($Appointment);
                        $result[] = [
                            'notification_id' => $value->id,
                            'doctor_id' => $value->doctor_id,
                            'doctor_name' => $drName,
                            'patient_id' => $value->patient_id,
                            'patient_name' => $patient_Name,
                            'appointment_id' => $value->appointment_id,
                            'time_slot_id' => $Appointment->time_slot_id,
                            'day_id' => $Appointment->day_id,
                            'appointment_date' => $Appointment->appointment_date,
                            'rescheduled_time_slot_id' => $Appointment->rescheduled_time_slot_id,
                            'rescheduled_day_id' => $Appointment->rescheduled_day_id,
                            'rescheduled_date' => $Appointment->rescheduled_date,
                            'type' => $value->type,
                            // 'created_at' => Carbon::parse($value->created_at)->format('h:i A, d M'),
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at
                            // 'updated_at' => Carbon::parse($value->updated_at)->format('h:i A, d M'),
                        ];
	    			}
	    			$response = [
						'message' => __('messages.success.success'),
						'response' => $result
					];
					return response()->json($response,trans('messages.statusCode.ACTION_COMPLETE'));
		    	}else{
			    	$Response = [
						'message'  => trans('messages.invalid.request'),
					];
			      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE'));
    			}
		   }else{
    			$Response = [
    			  'message'  => trans('messages.invalid.credentials'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    		}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken')
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
 	}

 	public  function reschedule_appointment_by_patient(Request $request){
        $accessToken =  $request->header('accessToken');
        $appointment_id = $request->appointment_id;
        $doctor_id = $request->doctor_id;
        // $day_id = $request->day_id;
        $time_slot_id = $request->time_slot_id;
        $appointment_date = $request->appointment_date;
        if( !empty( $accessToken ) ) {
            $PatientDetail = User::Where(['remember_token' => $accessToken])->first();
            // dd($PatientDetail);
            if(count($PatientDetail)){
                if($PatientDetail->user_type == 2){
                    $validations = [
                        'doctor_id' => 'required|numeric',
                        'appointment_id' => 'required|numeric',
                        'time_slot_id' => 'required|numeric',
                        // 'day_id' => 'required|numeric',
                        'appointment_date' => 'required|date_format:"Y-m-d"'
                    ];
                    $validator = Validator::make($request->all(),$validations);
                    if($validator->fails()){
                        $response = [
                            'message' => $validator->errors($validator)->first()
                        ];
                        return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                    }else{
                        $appointment_date_from_user = Carbon::parse($appointment_date);
                        // dd($appointment_date_from_user);
                        if(!$appointment_date_from_user->isYesterday()){
                        	// dd(Carbon::parse($appointment_date_from_user)->dayOfWeek+1);
                        	$day_id = Carbon::parse($appointment_date_from_user)->dayOfWeek+1;
                         	$check_doctor_availability = DoctorAvailability::where(['doctor_id' => $doctor_id,'day_id' => $day_id ,'time_slot_id' => $time_slot_id])->first();
                         	// dd($check_doctor_availability);
                            if($check_doctor_availability){

                                $AlreadyBusyTimeSlot = Appointment::where([
                                    'doctor_id' => $doctor_id,
                                    'time_slot_id' => $time_slot_id, 
                                    'day_id' => $day_id,
                                    'appointment_date' => $appointment_date_from_user
                                ])
                                ->where('status_of_appointment','<>','Rejected')
                                ->where('patient_id','<>',$PatientDetail->id)
                                ->get();
                                // dd($AlreadyBusyTimeSlot);
                                if(!count($AlreadyBusyTimeSlot)){
                                    $AppointmentDetail = Appointment::find($appointment_id);
                                    // dd($AppointmentDetail);
                                    if($AppointmentDetail && $AppointmentDetail->patient_id = $PatientDetail->id){

                                 		// dd($time_slot_id);
													// $AppointmentDetail->time_slot_id = $time_slot_id;
													$AppointmentDetail->rescheduled_time_slot_id = $time_slot_id;

													// $AppointmentDetail->day_id = $day_id;
													$AppointmentDetail->rescheduled_day_id = $day_id;

													// $AppointmentDetail->appointment_date = $appointment_date_from_user;
													$AppointmentDetail->rescheduled_date = $appointment_date_from_user;


													$AppointmentDetail->rescheduled_by_patient = 1;
													$AppointmentDetail->status_of_appointment = 'Pending';
													$AppointmentDetail->save();

													Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$PatientDetail->id,'type' => __('messages.notification_status_codes.Appointment_Rescheduled_By_Patient'),'appointment_id' => $appointment_id]);


													// HERE I HAVE TO SEND NOTIFICATION TO GET CONFIRM ABOUT RESCHEDULED APPOINTMENT
													$Response = [
													  'message'  => trans('messages.success.appointment_rescheduled'),
													  'response' => Appointment::find($appointment_id)
													];
													return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }else{
                                        $Response = [
                                            'message'  => trans('messages.success.NO_DATA_FOUND'),
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.NO_DATA_FOUND'));
                                    }
                                }else{
                                    if($AlreadyBusyTimeSlot[0]->patient_id == $patient_id){
                                        $AppointmentDetail = Appointment::find($appointment_id);
                                        // $AppointmentDetail->time_slot_id = $time_slot_id;
                                        $AppointmentDetail->rescheduled_time_slot_id = $time_slot_id;

                                        // $AppointmentDetail->day_id = $day_id;
                                        $AppointmentDetail->rescheduled_day_id = $day_id;

                                        // $AppointmentDetail->appointment_date = $appointment_date_from_user;
                                        $AppointmentDetail->rescheduled_date = $appointment_date_from_user;


                                        $AppointmentDetail->rescheduled_by_patient = 1;
                                        $AppointmentDetail->status_of_appointment = 'Pending';
                                        $AppointmentDetail->save();

                                        Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$PatientDetail->id,'type' => __('messages.notification_status_codes.Appointment_Rescheduled_By_Patient'),'appointment_id' => $appointment_id]);
                                        // HERE I HAVE TO SEND NOTIFICATION TO GET CONFIRM ABOUT RESCHEDULED APPOINTMENT
                                        $Response = [
                                            'message'  => trans('messages.success.appointment_rescheduled'),
                                            'response' => Appointment::find($appointment_id)
                                        ];
                                        return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                                    }else{
                                        $response = [
                                            'messages' => __('messages.Already_Busy_Time_Slot_With_Other_Patient')
                                        ];
                                        return response()->json($response,__('messages.statusCode.ALREADY_EXIST'));
                                    }
                                }
                            }else{
                                $response = [
                                    'message' => __('messages.invalid.doctor_not_available_at_this_time_slot')
                                ];
                                return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                            }
                        }else{
                            $response = [
                                'message' => __('messages.invalid.appointment_date')
                            ];
                            return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                        }
                    }
                }else{
                    $Response = [
                        'message'  => trans('messages.invalid.request'),
                    ];
                    return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                }
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,__('messages.statusCode.INVALID_ACCESS_TOKEN'));
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
   }

   

   /*public function doctor_availability(){
   	$dates = [ 
			Carbon::now()->addDay(1)->format('Y-m-d'),
			Carbon::now()->addDay(2)->format('Y-m-d'),
			Carbon::now()->addDay(3)->format('Y-m-d'),
			Carbon::now()->addDay(4)->format('Y-m-d'),
			Carbon::now()->addDay(5)->format('Y-m-d'),
			Carbon::now()->addDay(6)->format('Y-m-d')
		];
		$days = [
			Carbon::now()->addDay(1)->dayOfWeek+1,
			Carbon::now()->addDay(2)->dayOfWeek+1,
			Carbon::now()->addDay(3)->dayOfWeek+1,
			Carbon::now()->addDay(4)->dayOfWeek+1,
			Carbon::now()->addDay(5)->dayOfWeek+1,
			Carbon::now()->addDay(6)->dayOfWeek+1
		];
		foreach ($res->doctor_availability as $key => $value) {
			foreach ($days as $key => $value1) {
				if($value1 == 1 && $value->day_id == 1){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])
				   	->where('status_of_appointment','<>','rejected')
				   	->where('appointment_date',$dates[$key])
				   	->first();
				      if($busyOrFree){
					      array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }else{
					   	array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
					   }
			   }
			   if($value1 == 2 && $value->day_id == 2){
			   	// dd($value->doctor_id);
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
				   		->where('appointment_date',$dates[$key])
				   		->first();
				   	// dd($busyOrFree);
				   	if($busyOrFree){
				     		array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				     	}else{
			      		array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
			      	}
			   }
			   if($value1 == 3 && $value->day_id == 3){
			   	
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
					   	->where('appointment_date',$dates[$key])
					   	->first();
					   if($busyOrFree){
					   	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
					   }else{
							array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);   		
					   }
			   }
			   if($value1 == 4 && $value->day_id == 4){

				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
					   	->where('appointment_date',Carbon::now()->addDay(4)->format('Y-m-d'))
					   	->first();
					   if($busyOrFree){
			       		array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			       	}else{
			       		array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
			       	}
			   }
			   if($value1 == 5 && $value->day_id == 5){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
				   		->where('appointment_date',Carbon::now()->addDay(5)->format('Y-m-d'))
				   		->first();
				   	if($busyOrFree){
				     		array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
				     	}else{
				   		array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
				   	}
			   }
			   if($value1 == 6 && $value->day_id == 6){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
				   		->where('appointment_date',Carbon::now()->addDay(6)->format('Y-m-d'))
				   		->first();
				   	if($busyOrFree){
			       		array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			       	}else{
			      		array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
			      	}
			   }
			   if($value1 == 7 && $value->day_id == 7){
				   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->where('status_of_appointment','<>','rejected')
				   		->where('appointment_date',Carbon::now()->addDay(7)->format('Y-m-d'))
				   		->first();
				   	if($busyOrFree){
			      		array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			      	}else{
			      		array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>0]);
			      	}
		   	}
			}

		   if($value->day_id == 1){
		   	if(Carbon::now()->dayOfWeek+1 == 1){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])
			   	->where('status_of_appointment','<>','rejected')
			   	->where('appointment_date',Carbon::now()->addDay(1)->format('Y-m-d'))
			   	->first();
			       array_push($day1,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			   }
		   }
		   if($value->day_id == 2){
		   	if(Carbon::now()->dayOfWeek+1 == 2){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
			   		->where('appointment_date',Date('Y-m-d'))
			   		->first();
			      array_push($day2,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			   }
		   }
		   if($value->day_id == 3){
		   	if(Carbon::now()->dayOfWeek+1 == 3){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
				   	->where('appointment_date',Date('Y-m-d'))
				   	->first();
		       	array_push($day3,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
		      }
		   }
		   if($value->day_id == 4){
		   	if(Carbon::now()->dayOfWeek+1 == 4){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
				   	->where('appointment_date',Date('Y-m-d'))
				   	->first();
		       	array_push($day4,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
		      }
		   }
		   if($value->day_id == 5){
		   	if(Carbon::now()->dayOfWeek+1 == 5){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
			   		->where('appointment_date',Date('Y-m-d'))
			   		->first();
			      array_push($day5,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
			   }
		   }
		   if($value->day_id == 6){
		   	if(Carbon::now()->dayOfWeek+1 == 6){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
			   		->where('appointment_date',Date('Y-m-d'))
			   		->first();
		       	array_push($day6,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
		      }
		   }
		   if($value->day_id == 7){
		   	if(Carbon::now()->dayOfWeek+1 == 7){
			   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->where('status_of_appointment','<>','rejected')
			   		->where('appointment_date',Date('Y-m-d'))
			   		->first();
		      	array_push($day7,['time_slot_id'=>$value->time_slot_id,'busyOrFree'=>count($busyOrFree)]);
		      }
		   }
		}
		$doctor_availabilities_result = [
		   '1' => $day1,
		   '2' => $day2,
		   '3' => $day3,
		   '4' => $day4,
		   '5' => $day5,
		   '6' => $day6,
		   '7' => $day7,
     	];

     	return $doctor_availabilities_result;
   }*/

}
