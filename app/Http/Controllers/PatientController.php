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
use App\Review;
use Hash;
use Auth;
use Exception;

class PatientController extends Controller
{

	public function __construct(){
		date_default_timezone_set('Asia/Calcutta');
	} 

 	public function bookmark_UnBookMark_Doctor(Request $request){
		Log::info('----------------------PatientController--------------------------bookmark_UnBookMark_Doctor'.print_r($request->all(),True));
 		
		$accessToken = $request->header('accessToken');
		$patient_id = $request->patient_id;
		$doctor_id = $request->doctor_id;
		$key = $request->key; // 1 for bookmark or 0 for un bookmark
		$locale = $request->header('locale');
		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
 		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
    				$final_result = [];
    				foreach ($PatientBookmark as $key => $value) {
    					// dd($value->doctor_id);
				    	$result[] = $this->getUserDetail($User->getUserDetail($value->doctor_id));
    				}
    				foreach ($result as $key => $value) {
    					$Review = Review::where(['doctor_id' => $value['id'] , 'status_by_doctor' => 1])->get();
    					$final_result[] = [
    						'UserIdentificationType' => $value['UserIdentificationType'],
				   		'id' => $value['id'],
				   		'firebase_id' => $value['firebase_id'],
				   		'name' => $value['name'],
				   		'email' => $value['email'],
				   		'country_code' => $value['country_code'],
				   		'mobile' => $value['mobile'],
				   		'profile_image' => $value['profile_image'],
				   		'speciality_id' => $value['speciality_id'],
				   		'experience' => $value['experience'],
				   		'working_place' => $value['working_place'],
				   		'latitude' => $value['latitude'],
				   		'longitude' => $value['longitude'],
				   		'about_me' => $value['about_me'],
				   		'remember_token' => $value['remember_token'],
				   		'device_token' => $value['device_token'],
				   		'device_type' => $value['device_type'],
				   		'user_type' => $value['user_type'],
				   		'medical_licence_number' => $value['medical_licence_number'],
				   		'issuing_country' => $value['issuing_country'],
				   		'status' => $value['status'],
				   		'profile_status' => $value['profile_status'],
				   		'notification' => $value['notification'],
				   		'language' => $value['language'],
				   		'speciality' => $value['speciality'],
				   		'otp_detail' => $value['otp_detail'],
				   		'qualification' => $value['qualification'],
				   		'mother_language' => $value['mother_language'],
				   		'doctor_availabilities' => $value['doctor_availabilities'],
				   		'bookmark' => '1',
				   		'reviews' => $Review
    					];
    				}
    				$Response = [
						'message'  => trans('messages.success.success'),
						'response' => $final_result
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
		$timezone = $request->header('timezone');
		Log::info('Timezone: '.$timezone);
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
					    			->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
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
					    				->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
					    				->first();
					    				// dd($already_booked);
					    				if(!$already_booked){
					    					if(!Carbon::parse($appointment_date)->isToday())
					    					{
							    				$appontmentId = Appointment::insertGetId($AppointmentData);
							    				
							    				$NotificationDataArray = [
					                        'getter_id' => $doctor_id,
					                        'message' => __('messages.notification_messages.Scheduled_Appointment')
					                    	];
					                    	$NotificationGetterDetail = User::find($doctor_id);
                                    if($NotificationGetterDetail->notification){
                                        $this->send_notification($NotificationDataArray);
                                    }

							    				Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$patient_id,'type'=>__('messages.notification_status_codes.Scheduled_Appointment'),'appointment_id' => $appontmentId]);
							    				$result = Appointment::find($appontmentId);
								    			$Response = [
													'message'  => trans('messages.success.appointment_scheduled'),
													'response' => $result
												];

												Log::info('----------RESPONSE------------PatientController--------------------------schedule_appointment_with_doctor'.print_r($Response,True));

										      return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
										   }else{
										   	if(TimeSlot::find($time_slot_id)->start_time > Carbon::now()->format('h:i:s')){
										   		$appontmentId = Appointment::insertGetId($AppointmentData);

										   		$NotificationDataArray = [
						                        'getter_id' => $doctor_id,
						                        'message' => __('messages.notification_messages.Scheduled_Appointment')
						                    	];
						                    	$NotificationGetterDetail = User::find($doctor_id);
	                                    if($NotificationGetterDetail->notification){
	                                        $this->send_notification($NotificationDataArray);
	                                    }

								    				Notification::insert(['doctor_id' => $doctor_id,'patient_id'=>$patient_id,'type' => __('messages.notification_status_codes.Scheduled_Appointment'),'appointment_id' => $appontmentId]);
								    				$result = Appointment::find($appontmentId);
									    			$Response = [
														'message'  => trans('messages.success.appointment_scheduled'),
														'response' => $result
													];
													Log::info('-------RESPONSE---------------PatientController--------------------------schedule_appointment_with_doctor'.print_r($Response,True));
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
 		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
		    			// return $result;
		    			$final_result = [];
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

						$qualificationArr = [];
						$DoctorDetail = [];
						$final_result =[];
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
							
							foreach ($res->doctor_availability as $key => $value) {
								foreach ($days as $key => $value1) {
									if($value1 == 1 && $value->day_id == 1){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])
								   	->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
								   	->where('appointment_date',$dates[$key])
								   	->first();

								   	if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 1 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
								   if($value1 == 2 && $value->day_id == 2){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
								   		->where('appointment_date',$dates[$key])
								   		->first();
								   	if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 2 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
								   if($value1 == 3 && $value->day_id == 3){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
									   	->where('appointment_date',$dates[$key])
									   	->first();
									   if(!empty($busyOrFree->rescheduled_day_id)){
		                           if($busyOrFree->rescheduled_day_id == 3 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
								   if($value1 == 4 && $value->day_id == 4){
								   	// dd($value);
								   	// dd($dates[$key]);
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
									   	->where('appointment_date',$dates[$key])
									   	->first();
									   if(!empty($busyOrFree->rescheduled_day_id)){
				                     if($busyOrFree->rescheduled_day_id == 4 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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

								   if($value1 == 5 && $value->day_id == 5){
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
									   		->where('appointment_date',$dates[$key])
									   		->first();
									   	if(!empty($busyOrFree->rescheduled_day_id)){
					                     if($busyOrFree->rescheduled_day_id == 5 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
									
									if($value1 == 6 && $value->day_id == 6){
									   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
									   		->where('appointment_date',$dates[$key])
									   		->first();
									   	if(!empty($busyOrFree->rescheduled_day_id)){
					                     if($busyOrFree->rescheduled_day_id == 6 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
								   if($value1 == 7 && $value->day_id == 7){
								   	$busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value1])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
								   		->where('appointment_date',$dates[$key])
								   		->first();
								   	if(!empty($busyOrFree->rescheduled_day_id)){
				                     if($busyOrFree->rescheduled_day_id == 7 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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

							   if($value->day_id == 1){
			                  if(Carbon::now()->dayOfWeek+1 == 1){
			                    $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])
			                    ->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
			                    ->where('appointment_date',Date('Y-m-d'))
			                    ->first();
			                     if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 1 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
			               }

							   if($value->day_id == 2){
					            if(Carbon::now()->dayOfWeek+1 == 2){
					              $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
					              ->where('appointment_date',Date('Y-m-d'))
					              ->first();
					              // dd($busyOrFree);
					               if(!empty($busyOrFree->rescheduled_day_id)){
					                  if($busyOrFree->rescheduled_day_id == 2 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
					         }

							   if($value->day_id == 3){
			                  if(Carbon::now()->dayOfWeek+1 == 3){
			                    $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
			                        ->where('appointment_date',Date('Y-m-d'))
			                        ->first();
			                     if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 3 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
			               }

							   if($value->day_id == 4){
			                  if(Carbon::now()->dayOfWeek+1 == 4){
			                     $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
			                     ->where('appointment_date',Date('Y-m-d'))
			                     ->first();
			                     if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 4 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
               			}

							   if($value->day_id == 5){
			                  if(Carbon::now()->dayOfWeek+1 == 5){
			                     $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
			                        ->where('appointment_date',Carbon::now()->format('Y-m-d'))
			                        ->first();
			                     if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 5 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
			               }

							   if($value->day_id == 6){
			                  if(Carbon::now()->dayOfWeek+1 == 6){
			                    $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
			                        ->where('appointment_date',Date('Y-m-d'))
			                        ->first();
			                    if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 6 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
			               }

							   if($value->day_id == 7){
			                  if(Carbon::now()->dayOfWeek+1 == 7){
			                    $busyOrFree = Appointment::where(['doctor_id'=>$value->doctor_id,'time_slot_id'=>$value->time_slot_id,'day_id'=>$value->day_id])->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired','Completed','Transfered'])
			                        ->where('appointment_date',Date('Y-m-d'))
			                        ->first();
			                     if(!empty($busyOrFree->rescheduled_day_id)){
			                        if($busyOrFree->rescheduled_day_id == 7 && $busyOrFree->status_of_appointment!= 'Cancelled' && $busyOrFree->status_of_appointment != 'Expired' && $busyOrFree->status_of_appointment != 'Rejected') {
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
							}
							$doctor_availabilities_result = [
							   '1' => $this->filter($day1),
							   '2' => $this->filter($day2),
							   '3' => $this->filter($day3),
							   '4' => $this->filter($day4),
							   '5' => $this->filter($day5),
							   '6' => $this->filter($day6),
							   '7' => $this->filter($day7),
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
				        		'firebase_id' => $res->DoctorDetail->firebase_id,
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
				        		'created_at' => Carbon::parse($res->DoctorDetail->created_at)->format('Y-m-d H:i:s'),
				        		'updated_at' => Carbon::parse($res->DoctorDetail->updated_at)->format('Y-m-d H:i:s'),
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
		    					'reffered__to__doctor__detail' => $res->Reffered_To_Doctor_Detail,
		    				];
		    			}

		    			$Rsult = [];
		    			// dd($final_result[]);
		    			foreach ($final_result as $key => $value) {
		    				// dd($value['doctor_detail']);
		    				$Aptment_date = Carbon::parse($value['appointment_date'])->format('Y-m-d');
		    				$today_date = Carbon::now()->format('Y-m-d');
		    				$today_day_id = Carbon::parse($value['appointment_date'])->dayOfWeek+1;
		    				$TimeSlotDetail = TimeSlot::find($value['time_slot_id']);
                     $TimeSlotDetail_startTime = $TimeSlotDetail->start_time;
                     $TimeSlotDetail_endTime = $TimeSlotDetail->end_time;

		    				if($Aptment_date == $today_date && $today_day_id == $value['day_id'] && Carbon::parse($TimeSlotDetail_startTime) < Carbon::now() && $value['status_of_appointment'] == 'Pending')
                     {
                     	$Appointment = Appointment::find($value['id']);
                     	$Appointment->status_of_appointment = 'Expired';
                     	$Appointment->save();

			    				$Rsult[] = [
			    					'id' => $value['id'],
			    					'patient_id' => $value['patient_id'],
			    					'patient_age' => $value['patient_age'],
			    					'patient_gender' => $value['patient_gender'],
			    					'question' => $value['question'],
			    					'previous_illness_desc' => $value['previous_illness_desc'],
			    					'doctor_id' => $value['doctor_id'],
			    					'time_slot_id' => $value['time_slot_id'],
			    					'day_id' => $value['day_id'],
			    					'appointment_date' => $value['appointment_date'],
			    					'status_of_appointment' => 'Expired',
			    					'reffered_to_doctor_id' => $value['reffered_to_doctor_id'],
			    					'rescheduled_by_doctor' => $value['rescheduled_by_doctor'],
			    					'rescheduled_time_slot_id' => $value['rescheduled_time_slot_id'],
			    					'rescheduled_day_id' => $value['rescheduled_day_id'],
			    					'rescheduled_date' => $value['rescheduled_date'],
			    					'rescheduled_by_patient' => $value['rescheduled_by_patient'],
			    					'doctor_detail' => $value['doctor_detail'],
			    					'reffered__to__doctor__detail' => $value['reffered__to__doctor__detail'],
			    					'is_rated' => Review::where(['appointment_id' => $value['id']])->count()
		    					];
		    				}else{
		    					// dd(Carbon::parse($value['appointment_date']) < Carbon::now());
		    					if(Carbon::parse($TimeSlotDetail_endTime) < Carbon::now() && Carbon::parse($value['appointment_date']) < Carbon::now()){
		    						Appointment::where(['id' => $value['id']])->update(['status_of_appointment' => 'Completed']);
                           $status_of_appointment = 'Completed';
                        }else{
                           $status_of_appointment = $value['status_of_appointment'];
                        }
		    					$Rsult[] = [
			    					'id' => $value['id'],
			    					'patient_id' => $value['patient_id'],
			    					'patient_age' => $value['patient_age'],
			    					'patient_gender' => $value['patient_gender'],
			    					'question' => $value['question'],
			    					'previous_illness_desc' => $value['previous_illness_desc'],
			    					'doctor_id' => $value['doctor_id'],
			    					'time_slot_id' => $value['time_slot_id'],
			    					'day_id' => $value['day_id'],
			    					'appointment_date' => $value['appointment_date'],
			    					'status_of_appointment' => $status_of_appointment,
			    					'reffered_to_doctor_id' => $value['reffered_to_doctor_id'],
			    					'rescheduled_by_doctor' => $value['rescheduled_by_doctor'],
			    					'rescheduled_time_slot_id' => $value['rescheduled_time_slot_id'],
			    					'rescheduled_day_id' => $value['rescheduled_day_id'],
			    					'rescheduled_date' => $value['rescheduled_date'],
			    					'rescheduled_by_patient' => $value['rescheduled_by_patient'],
			    					'doctor_detail' => $value['doctor_detail'],
			    					'reffered__to__doctor__detail' => $value['reffered__to__doctor__detail'],
			    					'is_rated' => Review::where(['appointment_id' => $value['id']])->count()
		    					];
		    				}
		    			}

		    			$Response = [
							'message'  => trans('messages.success.success'),
							'response' => $Rsult
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
		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

 		if( !empty( $accessToken ) ) {
 			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
 			// return $UserDetail;
 			if(count($UserDetail)){
 				// return $UserDetail;
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
								// if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') )
								// dd(Carbon::parse($Appointment_TimeSlot_StartTime ) > Carbon::now());
								if(Carbon::parse($Appointment_TimeSlot_StartTime ) > Carbon::now() )
								{
                           if($accept_or_reject == 'Accepted'){
                           	Notification::where('appointment_id',$AppointmentDetail->id)->delete();
                           	$AppointmentDetail->status_of_appointment = $accept_or_reject;
                           	$AppointmentDetail->time_slot_id = $AppointmentDetail->rescheduled_time_slot_id;
                           	$AppointmentDetail->day_id = $AppointmentDetail->rescheduled_day_id;
                           	$AppointmentDetail->appointment_date = $AppointmentDetail->rescheduled_date;
                           	$AppointmentDetail->save();

                           	$NotificationDataArray = [
			                        'getter_id' => $doctor_id,
			                        'message' => __('messages.notification_messages.RESCHEDULED_ACCEPTED_BY_PATIENT')
			                    	];
			                    	$NotificationGetterDetail = User::find($doctor_id);
                              if($NotificationGetterDetail->notification){
                                  $this->send_notification($NotificationDataArray);
                              }

                           	Notification::insert(['doctor_id'=>$doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Rescheduled_Appointment_Accepted_By_Patient'),'appointment_id' => $appointment_id,'appointment_status'=>'Accepted']);

                               $Response = [
                                   'message'  => trans('messages.success.appointment_accepted'),
                               ];
                               return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
                           }
                           if($accept_or_reject == 'Rejected'){
                           	Notification::where('appointment_id',$AppointmentDetail->id)->delete();
                           	// $AppointmentDetail->status_of_appointment = 'Rejected by patient';
                           	$AppointmentDetail->rescheduled_time_slot_id = null;
                           	$AppointmentDetail->rescheduled_day_id = null;
                           	$AppointmentDetail->rescheduled_date = null;

                           	$NotificationDataArray = [
			                        'getter_id' => $doctor_id,
			                        'message' => __('messages.notification_messages.RESCHEDULED_REJECTED_BY_PATIENT')
			                    	];
			                    	$NotificationGetterDetail = User::find($doctor_id);
                              if($NotificationGetterDetail->notification){
                                  $this->send_notification($NotificationDataArray);
                              }

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
								   Notification::where('appointment_id',$AppointmentDetail->id)->delete();
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
 		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
									if(Carbon::parse($Appointment_TimeSlot_StartTime ) > Carbon::now()){
										// if( Carbon::parse(strtoupper(($Appointment_TimeSlot_StartTime)))->format('g:i A') > Carbon::now()->format('g:i A') ){
										// dd($AppointmentDetail->doctor_id);
	                        	$AppointmentDetail->status_of_appointment = 'Cancelled';
	                        	$AppointmentDetail->save();

	                        	$NotificationDataArray = [
			                        'getter_id' => $AppointmentDetail->doctor_id,
			                        'message' => __('messages.notification_messages.Appointment_Cancelled_By_Patient')
			                    	];
			                    	$NotificationGetterDetail = User::find($AppointmentDetail->doctor_id);
                              if($NotificationGetterDetail->notification){
                                  $this->send_notification($NotificationDataArray);
                              }

	                        	Notification::insert(['doctor_id'=>$AppointmentDetail->doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Appointment_Cancelled_By_Patient'),'appointment_id' => $appointment_id]);

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

                        	$NotificationDataArray = [
		                        'getter_id' => $AppointmentDetail->doctor_id,
		                        'message' => __('messages.notification_messages.Appointment_Cancelled_By_Patient')
		                    	];
		                    	$NotificationGetterDetail = User::find($AppointmentDetail->doctor_id);
                           if($NotificationGetterDetail->notification){
                               $this->send_notification($NotificationDataArray);
                           }

                        	Notification::insert(['doctor_id'=>$AppointmentDetail->doctor_id,'patient_id'=>$AppointmentDetail->patient_id,'type'=>__('messages.notification_status_codes.Appointment_Cancelled_By_Patient'),'appointment_id' => $appointment_id]);
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
 		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
 		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
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
	    			$USER = new User;
	    			// dd($Notification);
	    			foreach ($Notification as $key => $value) {
	    				$drName = User::where(['id'=>$value->doctor_id])->select('name')->first()->name;
	    				$patient_Name = User::where(['id'=>$value->patient_id])->select('name')->first()->name;
	    				$Appointment = Appointment::find($value->appointment_id);
	    				$trans_dr_detail = null;
	    				if(!empty($value->reffered_to_doctor_id)){
	    					$trans_dr_detail = $this->getUserDetail($USER->getUserDetail($value->reffered_to_doctor_id));
	    				}
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
                      'created_at' => Carbon::parse($value->created_at)->format('Y-d-m h:i:s'),
                      'updated_at' => Carbon::parse($value->updated_at)->format('Y-d-m h:i:s'),
                      'refer_doctor_detail' => $trans_dr_detail
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
                                ->whereNotIn('status_of_appointment',['Rejected','Cancelled','Expired'])
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
													// $AppointmentDetail->status_of_appointment = 'Pending';
													$AppointmentDetail->save();

													Notification::where(['appointment_id' => $appointment_id])->delete();

													$NotificationDataArray = [
						                        'getter_id' => $doctor_id,
						                        'message' => __('messages.notification_messages.RESCHEDULED_BY_PATIENT')

						                    	];
						                    	$NotificationGetterDetail = User::find($doctor_id);
			                              if($NotificationGetterDetail->notification){
			                                  $this->send_notification($NotificationDataArray);
			                              }

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
                                        // $AppointmentDetail->status_of_appointment = 'Pending';
                                        $AppointmentDetail->save();
                                        Notification::where(['appointment_id' => $appointment_id])->delete();
                                       
                                       $NotificationDataArray = [
						                        'getter_id' => $doctor_id,
						                        'message' => __('messages.notification_messages.RESCHEDULED_BY_PATIENT')
						                        
						                    	];
						                    	$NotificationGetterDetail = User::find($doctor_id);
			                              if($NotificationGetterDetail->notification){
			                                  $this->send_notification($NotificationDataArray);
			                              }

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

   public function giveReviewToDoctor(Request $request){
   	Log::info('------------------PatientController------------get_notification_list');
 		$accessToken = $request->header('accessToken');
 		$rating = $request->rating;
 		$review = $request->review;
 		$doctor_id = $request->doctor_id;
 		$appointment_id = $request->appointment_id;
 		$locale = $request->header('locale');
 		$timezone = $request->header('timezone');
   	if($timezone){
			$this->setTimeZone($timezone);
    	}
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {

			$UserDetail = User::where(['remember_token'=>$accessToken])->first();
			// dd($UserDetail);
    		if(count($UserDetail)){
    			$validations = [
					'rating' => 'required',
					'doctor_id' => 'required',
					'appointment_id' => 'required',
				];
				$Validator = Validator::make($request->all(),$validations);
				if($Validator->fails()){
					$Response = [
						'messages' => $Validator->errors($Validator)->first()
					];
	        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
				}
    			switch ($UserDetail->user_type) {
    				case 1:
    					$Response = [
		    			  'message'  => trans('messages.invalid.credentials'),
		    			];
		        		return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
    					break;
    				case 2:
    					$review_data = Review::firstOrNew(['patient_id' => $UserDetail->id, 'appointment_id' => $appointment_id , 'doctor_id' => $doctor_id]);
    					$review_data->rating = $rating;
    					$review_data->review_text = $review;
    					$review_data->save();
    					$Response = [
							'messages' => __('messages.success.success')
						];
		        		return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );
    					break;	
    				default:
    					$Response = [
							'messages' => __('messages.invalid.request')
						];
		        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
    					break;
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
}
