<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DB;
use Mail;
use Log;
use Response;
use Session;
use \Carbon\Carbon;
use App\User;
use App\Otp;
use App\UserDetail;
use App\Review;
use App\TimeSlot;
use App\DoctorAvailability;
use App\PatientBookmark;
use App\Category;
use App\Appointment;
use App\Notification;
use Hash;
use Auth;
use Exception;


class DoctorController extends Controller
{
   public function getList(Request $request){
     	Log::info('----------------------DoctorController--------------------------getList'.print_r($request->all(),True));
     	$query = [
     		'status' => 1,
     		'user_type' => 1
     	];
     	$list = User::getUserList($query);
     	$result = [];
     	$doctor_availabilities = [];
     	// dd(count($list));
     	foreach ($list as $key => $value) {
         $doctor_availabilities = DoctorAvailability::Where(['doctor_id' => $value->id])->get();
         $result[] = [
             'id' => $value->id,
             'name' => $value->name,
             'email' => $value->email,
             'country_code' => $value->country_code,
             'mobile' => $value->mobile,
             'profile_image' => $value->profile_image,
             'speciality_id' => $value->speciality_id,
             'experience' => $value->experience,
             'working_place' => $value->working_place,
             'latitude' => $value->latitude,
             'longitude' => $value->longitude,
             'about_me' => $value->about_me,
             'remember_token' => $value->remember_token,
             'device_token' => $value->device_token,
             'device_type' => $value->device_type,
             'user_type' => $value->user_type,
             'profile_status' => $value->profile_status,
             'notification' => $value->notification,
             'language' => $value->language,
             'doctor_availabilities' => $doctor_availabilities
         ];
     	}
   	$response = [
   		'message' => __('messages.success.success'),
   		'response' => $result
   	];
   	return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
   }
    // at search by category this api will hit only every time
   public function getDoctorBySpecialityId_FOR_PATIENT_SEARCH(Request $request){
      Log::info('----------------------DoctorController--------------------------getDoctorBySpecialityId_FOR_PATIENT_SEARCH'.print_r($request->all(),True));
      $accessToken =  $request->header('accessToken');
      $speciality_id =  $request->speciality_id;
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
         $PATIENT_DETAIL = User::Where(['remember_token' => $accessToken])->first();
         if(count($PATIENT_DETAIL)){
             $validations = [
                 'speciality_id' => 'required',
             ];
             $validator = Validator::make($request->all(),$validations);
             if( $validator->fails() ) {
                 $response = [ 'message'   =>  $validator->errors($validator)->first()
                 ];
                 return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
             } else {
                 $query = [
                     'status' => 1,
                     'user_type' => 1,
                     'speciality_id' => $speciality_id
                 ];
                 $result = [];
                 $list = User::getDoctorBySpecialityId($query);
                 // dd($list);
                 $otp_detail = [];
                 if(count($list)) {
                     foreach ($list as $key => $value) {
                         $data = $this->getUserDetail($value);
                         $bookmarked = PatientBookmark::where(['patient_id' => $PATIENT_DETAIL->id , 'doctor_id' => $data['id']])->count();
                         // dd($data['id']);
                         $Review = Review::where(['doctor_id' => $data['id'] , 'status_by_doctor' => 1])->get();
                         $result[] = [
                             'UserIdentificationType' => $data['UserIdentificationType'],
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
                             'status' => $data['status'],
                             'profile_status' => $data['profile_status'],
                             'notification' => $data['notification'],
                             'language' => $data['language'],
                             'otp_detail' => $data['otp_detail'],
                             'qualification' => $data['qualification'],
                             'mother_language' => $data['mother_language'],
                             'doctor_availabilities' => $data['doctor_availabilities'],
                             'bookmarked' => $bookmarked,
                             'reviews' => $Review
                         ];
                     }
                 }
                 $response = [
                     'message' => __('messages.success.success'),
                     'response' => $result
                 ];
                 return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
             }
         }else{
             $response['message'] = trans('messages.invalid.detail');
             return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
         }
      }else {
         $Response = [
             'message'  => trans('messages.required.accessToken'),
         ];
       return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
      }
   }

   public function save_doctor_timing_for_availability(Request $request){
      Log::info('----------------------DoctorController--------------------------save_doctor_timing_for_availability'.print_r($request->all(),True));
      $accessToken = $request->header('accessToken');
      // dd($request->all());
      $day = $request->day;
      $timeslotsArr = $request->timeslots;
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
         $validations = [
             'day' => 'required',
             // 'timeslots' => 'required_with:day',
         ];
         $validator = Validator::make($request->all(),$validations);
         if( $validator->fails() ) {
             $response = [ 'message'=>$validator->errors($validator)->first()
             ];
             return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
         } else {
             $DoctorDetail = User::where(['remember_token' => $accessToken])->first();
             // dd($timeslotsArr);
             if($DoctorDetail){
                 DoctorAvailability::where(['doctor_id' => $DoctorDetail->id,'day_id'=>$day])->delete();
                 if(!empty($timeslotsArr)){
                     foreach ($timeslotsArr as $key => $timeSlotId) {
                         DoctorAvailability::insert([
                         'day_id' => $day,
                         'time_slot_id' => $timeSlotId,
                         'doctor_id' => $DoctorDetail->id
                         ]);
                     }
                 }
                 $result = DoctorAvailability::where(['doctor_id' => $DoctorDetail->id])->get();
                 $response = [
                     'message' => __('messages.success.success'),
                     'response' => $result
                 ];
                 return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
             }else {
                 $response=[
                     'message' => trans('messages.invalid.request'),
                 ];
                 return Response::json($response,__('messages.statusCode.INVALID_ACCESS_TOKEN'));
             }
         }
      }else {
         $Response = [
             'message'  => trans('messages.required.accessToken'),
         ];
         return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
      }
   }

   public function get_doctor_availability(Request $request){
      Log::info('------------------DoctorController------------get_doctor_availability'.print_r($request->all(),True));
      $accessToken = $request->header('accessToken');
      if( !empty( $accessToken ) ) {
         $UserDetail = User::where(['remember_token'=>$accessToken])->first();
         if(count($UserDetail)){
             if($UserDetail->user_type == 1){
                 $DoctorAvailability = DoctorAvailability::where(['doctor_id' => $UserDetail->id])->get();  
                 $day1 = []; 
                 $day2 = []; 
                 $day3 = []; 
                 $day4 = []; 
                 $day5 = []; 
                 $day6 = []; 
                 $day7 = []; 
                 foreach ($DoctorAvailability as $key => $value) {
                     if($value->day_id == 1){
                         array_push($day1,(string)$value->time_slot_id);
                     }
                     if($value->day_id == 2){
                         array_push($day2,(string)$value->time_slot_id);
                     }
                     if($value->day_id == 3){
                         array_push($day3,(string)$value->time_slot_id);
                     }
                     if($value->day_id == 4){
                         array_push($day4,(string)$value->time_slot_id);
                     }
                     if($value->day_id == 5){
                         array_push($day5,(string)$value->time_slot_id);
                     }
                     if($value->day_id == 6){
                         array_push($day6,(string)$value->time_slot_id);
                     }
                     if($value->day_id == 7){
                         array_push($day7,(string)$value->time_slot_id);
                     }
                 }
                 $result = [
                     '1' => $day1,
                     '2' => $day2,
                     '3' => $day3,
                     '4' => $day4,
                     '5' => $day5,
                     '6' => $day6,
                     '7' => $day7,
                 ];
                 $Response = [
                     'message'  => trans('messages.success.success'),
                     'response' => $result
                 ];
                 return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
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

   public function getReviewList(Request $request){
      Log::info('------------------DoctorController------------getReviewList'.print_r($request->all(),True));
      $accessToken =  $request->header('accessToken');
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
         $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken, 'user_type' => 1])->first();
         if(count($DOCTOR_DETAIL)){
             $reviews = Review::where(['doctor_id' => $DOCTOR_DETAIL->id])
             ->whereIn('status_by_doctor',[0,1])->get();
             $response = [
                 'message' => __('messages.success.success'),
                 'response' => $reviews
             ];
             return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
         }else{
             $response['message'] = trans('messages.invalid.detail');
             return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
         }
      }else {
         $Response = [
             'message'  => trans('messages.required.accessToken'),
         ];
         return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
      }
   }

   public function acceptReview(Request $request){
      Log::info('------------------DoctorController------------acceptReview'.print_r($request->all(),True)); 
      $accessToken =  $request->header('accessToken');
      $review_id = $request->review_id;
      $acceptOrReject = $request->acceptOrReject; // 1 for accept / 2 for reject
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
         $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken, 'user_type' => 1])->first();
         if(count($DOCTOR_DETAIL)){
             $validations = [
                 'review_id'=>'required|numeric',
                 'acceptOrReject' => 'required|numeric'
             ];
             $validator = Validator::make($request->all(),$validations);
             if( $validator->fails() ){
                $response = [
                 'message'=>$validator->errors($validator)->first()
                ];
                return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
             }else{
                 // dd($DOCTOR_DETAIL->id);
                 $REVIEW_DETAIL = Review::where(['doctor_id' => $DOCTOR_DETAIL->id , 'id' => $review_id])->first();
                 // dd($REVIEW_DETAIL);
                 if($REVIEW_DETAIL){
                     $REVIEW_DETAIL->status_by_doctor = $acceptOrReject;
                     $REVIEW_DETAIL->save();
                     if($acceptOrReject == 1){
                         $response = [
                             'message' => __('messages.success.review_published'),
                         ];
                         return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));     
                     }
                     if($acceptOrReject == 2){
                         $response = [
                             'message' => __('messages.success.review_un_published'),
                         ];
                         return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));     
                     }
                 }else{
                     $response['message'] = trans('messages.invalid.request');
                     return response()->json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
                 }
             }
         }else{
             $response['message'] = trans('messages.invalid.detail');
             return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
         }
      }else {
         $Response = [
             'message'  => trans('messages.required.accessToken'),
         ];
         return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
      }
   }

   public function completeAppointmentByDoctor(Request $request){
      Log::info('------------------DoctorController------------acceptReview'.print_r($request->all(),True)); 
      $accessToken =  $request->header('accessToken');
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
         if(count($UserDetail)){
            if($UserDetail->user_type == 1){
               $validations = [
                  'appointment_id' => 'required',
               ];
               $validator = Validator::make($request->all(),$validations);
               if($validator->fails()){
                  $response = [
                      'message' => $validator->errors($validator)->first()
                  ];
                  return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
               }else{
                  // dd($UserDetail->id);
                  $appointment_detail = Appointment::where(['id' => $appointment_id,'doctor_id' => $UserDetail->id])->whereNotIn('status_of_appointment',['Pending','Expired','Cancelled'])->first();
                  if(count($appointment_detail)){
                     $appointment_detail->status_of_appointment = 'Completed';
                     $appointment_detail->save();
                     $Response = [
                        'message'  => trans('messages.success.success'),
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
               'message'  => trans('messages.invalid.detail'),
               ];
               return Response::json( $Response , trans('messages.statusCode.INVALID_ACCESS_TOKEN') );
            }
         }
      }else {
         $Response = [
             'message'  => trans('messages.required.accessToken'),
         ];
         return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
      }
   }

   public function transferAppointmentByDoctor(Request $request){
   	Log::info('------------------DoctorController------------transferAppointmentByDoctor'.print_r($request->all(),True)); 
      $accessToken =  $request->header('accessToken');
      $appointment_id = $request->appointment_id;
      $transfer_to_doctor_id = $request->transfer_to_doctor_id;
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
         // dd($UserDetail->notification);
         if(count($UserDetail)){
				$validations = [
				  'appointment_id'=>'required',
				  'transfer_to_doctor_id'=>'required',
				];
				$validator = Validator::make($request->all(),$validations);
          	if( $validator->fails() ){
					$response = [
					'message'=>$validator->errors($validator)->first()
					];
					return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
       		}else{
       			$appointment_detail = Appointment::where(['id'=>$appointment_id,'doctor_id'=>$UserDetail->id])->whereNotIn('status_of_appointment',['Expired','Cancelled','Completed','Transfered'])->first();
                // dd($appointment_detail);
       			if(count($appointment_detail)){
       				$appointment_detail->reffered_to_doctor_id = $transfer_to_doctor_id;
       				$appointment_detail->status_of_appointment = 'Transfered';
       				$appointment_detail->save();

                    $NotificationDataArray = [
                     'getter_id' => $appointment_detail->patient_id,
                     'message' => __('messages.notification_messages.Appointment_Transfered_By_Doctor')
                    ];

                    $NotificationGetterDetail = User::find($appointment_detail->patient_id);
                    if($NotificationGetterDetail->notification && !empty($NotificationGetterDetail->remember_token)){
                        $this->send_notification($NotificationDataArray);
                    }

                    Notification::insert([ 'doctor_id'=> $UserDetail->id ,'reffered_to_doctor_id' => $transfer_to_doctor_id,'patient_id' => $appointment_detail->patient_id,'appointment_id' => $appointment_detail->id , 'type' => __('messages.notification_status_codes.Appointment_Transfered_By_Doctor')]);
                    	$Response = [
                        'message'  => trans('messages.success.success'),
                    ];
                    return Response::json( $Response , __('messages.statusCode.ACTION_COMPLETE') );
       			}else{
       				$Response = [
                     'message'  => trans('messages.success.NO_DATA_FOUND'),
						];
						return Response::json( $Response , __('messages.statusCode.NO_DATA_FOUND') );
       			}
          	}
         }else{
				$response['message'] = trans('messages.invalid.detail');
          	return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
         }
      }else {
         $Response = [
             'message'  => trans('messages.required.accessToken'),
         ];
         return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
      }
   }

   public function get_all_appointment_of_doctor_by_date(Request $request){
      Log::info('------------------DoctorController------------get_all_appointment_of_doctor_by_date');
      $accessToken = $request->header('accessToken');
      $date = date('Y-m-d',strtotime($request->date));
      $page_number = $request->page_number;
      $device_token = $request->device_token;
      $locale = $request->header('locale');
      $timezone = $request->header('timezone');
      if($timezone){
        $this->setTimeZone($timezone);
      }
      if(empty($locale)){
      $locale = 'en';
      }
      \App::setLocale($locale);

      // dd(Carbon::now());
      if( !empty( $accessToken ) ) {
         $UserDetail = User::where(['remember_token'=>$accessToken])->first();
         if(count($UserDetail)){
            if($UserDetail->user_type == 1){
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
                     if($device_token){
                        $UserDetail->device_token = $device_token;
                        $UserDetail->save();
                     }
                     $result = Appointment::get_all_appointment_of_doctor_by_date($date,$UserDetail->id);
                     // return $result;
                     $final_result = [];
                     foreach ($result as $key => $value) {
                        // dd($value->id);
                        $today_day_id = Carbon::parse($value->appointment_date)->dayOfWeek+1;
                        $today_date = Carbon::now()->format('Y-m-d');
                        $Aptment_date = Carbon::parse($value->appointment_date)->format('Y-m-d');
                        $TimeSlotDetail = TimeSlot::find($value->time_slot_id);
                        $TimeSlotDetail_startTime = $TimeSlotDetail->start_time;
                        $TimeSlotDetail_endTime = $TimeSlotDetail->end_time;

                        if($Aptment_date == $today_date && $today_day_id == $value->day_id && Carbon::parse($TimeSlotDetail_startTime) < Carbon::now() && $value->status_of_appointment == 'Pending')
                        {
                           if(Carbon::parse($TimeSlotDetail_endTime) < Carbon::now()){
                              $Appointment = Appointment::find($value->id);
                              $Appointment->status_of_appointment = 'Expired';
                              $Appointment->save();
                              if($value->reffered_to_doctor_id){
                                $Reffered_To_Doctor_Detail = $value->Reffered_To_Doctor_Detail;
                                $Reffered_By_Doctor_Detail = $value->Reffered_By_Doctor_Detail;
                              }else{
                                $Reffered_To_Doctor_Detail = null;
                                $Reffered_By_Doctor_Detail = null;
                              }
                              
                              $final_result[]= [
                                 'id' => $value->id,
                                 'patient_id' => $value->patient_id,
                                 'patient_age' => $value->patient_age,
                                 'patient_gender' => $value->patient_gender,
                                 'question' => $value->question,
                                 'previous_illness_desc' => $value->previous_illness_desc,
                                 'doctor_id' => $value->doctor_id,
                                 'time_slot_id' => $value->time_slot_id,
                                 'day_id' => $value->day_id,
                                 'appointment_date' => $value->appointment_date,
                                 'status_of_appointment' => 'Expired',
                                 'reffered_to_doctor_id' => $value->reffered_to_doctor_id,
                                 'rescheduled_by_doctor' => $value->rescheduled_by_doctor,
                                 'rescheduled_time_slot_id' => $value->rescheduled_time_slot_id,
                                 'rescheduled_day_id' => $value->rescheduled_day_id,
                                 'rescheduled_date' => $value->rescheduled_date,
                                 'rescheduled_by_patient' => $value->rescheduled_by_patient,
                                 'created_at' => Carbon::parse($value->created_at)->format('Y-m-d H:i:s'),
                                 'updated_at' => Carbon::parse($value->updated_at)->format('Y-m-d H:i:s'),
                                 'patient_detail' => $value->patientDetail,
                                 'reffered__to__doctor__detail' => $Reffered_To_Doctor_Detail,
                                 'reffered__by__doctor__detail' => $Reffered_By_Doctor_Detail
                              ];
                           }else{
                              if($value->reffered_to_doctor_id){
                                $Reffered_To_Doctor_Detail = $value->Reffered_To_Doctor_Detail;
                                $Reffered_By_Doctor_Detail = $value->Reffered_By_Doctor_Detail;
                              }else{
                                $Reffered_To_Doctor_Detail = null;
                                $Reffered_By_Doctor_Detail = null;
                              }
                              $final_result[]= [
                                 'id' => $value->id,
                                 'patient_id' => $value->patient_id,
                                 'patient_age' => $value->patient_age,
                                 'patient_gender' => $value->patient_gender,
                                 'question' => $value->question,
                                 'previous_illness_desc' => $value->previous_illness_desc,
                                 'doctor_id' => $value->doctor_id,
                                 'time_slot_id' => $value->time_slot_id,
                                 'day_id' => $value->day_id,
                                 'appointment_date' => $value->appointment_date,
                                 'status_of_appointment' => $value->status_of_appointment,
                                 'reffered_to_doctor_id' => $value->reffered_to_doctor_id,
                                 'rescheduled_by_doctor' => $value->rescheduled_by_doctor,
                                 'rescheduled_time_slot_id' => $value->rescheduled_time_slot_id,
                                 'rescheduled_day_id' => $value->rescheduled_day_id,
                                 'rescheduled_date' => $value->rescheduled_date,
                                 'rescheduled_by_patient' => $value->rescheduled_by_patient,
                                 'created_at' => Carbon::parse($value->created_at)->format('Y-m-d H:i:s'),
                                 'updated_at' => Carbon::parse($value->updated_at)->format('Y-m-d H:i:s'),
                                 'patient_detail' => $value->patientDetail,
                                 'reffered__to__doctor__detail' => $Reffered_To_Doctor_Detail,
                                 'reffered__by__doctor__detail' => $Reffered_By_Doctor_Detail
                              ];
                           }
                        }else{
                           if(Carbon::parse($TimeSlotDetail_endTime) < Carbon::now() && Carbon::parse($value->appointment_date) < Carbon::now()){
                              Appointment::where(['id' => $value->id])->update(['status_of_appointment' => 'Completed']);
                              $status_of_appointment = 'Completed';
                           }else{
                              $status_of_appointment = $value->status_of_appointment;
                           }

                           if($value->reffered_to_doctor_id){
                             $Reffered_To_Doctor_Detail = $value->Reffered_To_Doctor_Detail;
                             $Reffered_By_Doctor_Detail = $value->Reffered_By_Doctor_Detail;
                           }else{
                             $Reffered_To_Doctor_Detail = null;
                             $Reffered_By_Doctor_Detail = null;
                           }

                           $final_result[]= [
                              'id' => $value->id,
                              'patient_id' => $value->patient_id,
                              'patient_age' => $value->patient_age,
                              'patient_gender' => $value->patient_gender,
                              'question' => $value->question,
                              'previous_illness_desc' => $value->previous_illness_desc,
                              'doctor_id' => $value->doctor_id,
                              'time_slot_id' => $value->time_slot_id,
                              'day_id' => $value->day_id,
                              'appointment_date' => $value->appointment_date,
                              'status_of_appointment' => $status_of_appointment,
                              'reffered_to_doctor_id' => $value->reffered_to_doctor_id,
                              'rescheduled_by_doctor' => $value->rescheduled_by_doctor,
                              'rescheduled_time_slot_id' => $value->rescheduled_time_slot_id,
                              'rescheduled_day_id' => $value->rescheduled_day_id,
                              'rescheduled_da