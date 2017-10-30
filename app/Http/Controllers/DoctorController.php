<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
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
use App\DoctorAvailability;
use App\PatientBookmark;
use App\Category;
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
        if( !empty( $accessToken ) ) {
            $PATIENT_DETAIL = User::Where(['remember_token' => $accessToken])->first();
            if(count($PATIENT_DETAIL)){
                $validations = [
                    'speciality_id' => 'required|numeric',
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
                    $otp_detail = [];
                    if(count($list)) {
                        foreach ($list as $key => $value) {
                            // dd($value);
                            $data = $this->getUserDetail($value);
                            $bookmarked = PatientBookmark::where(['patient_id' => $PATIENT_DETAIL->id , 'doctor_id' => $data['id']])->count();
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
                return response()->json($response,401);
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
        $daysArr = $request->days;
        $timeslotsArr = $request->timeslots;
        if( !empty( $accessToken ) ) {
            $validations = [
                'days' => 'required|array',
                'timeslots' => 'required|array',
            ];
            $validator = Validator::make($request->all(),$validations);
            if( $validator->fails() ) {
                $response = [ 'message'=>$validator->errors($validator)->first()
                ];
                return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
            } else {
                $DoctorDetail = User::where(['remember_token' => $accessToken])->first();
                if($DoctorDetail){
                    foreach ($daysArr as $key => $dayId) {
                        foreach ($timeslotsArr as $key => $timeSlotId) {
                            $doctor_availabilities_Data = [
                                'day_id' => $dayId,
                                'time_slot_id' => $timeSlotId,
                                'doctor_id' => $DoctorDetail->id
                            ]; 
                            $exist = DoctorAvailability::where([
                                'day_id' => $dayId,
                                'time_slot_id' => $timeSlotId,
                                'doctor_id' => $DoctorDetail->id
                                ])->first();
                            if(!$exist){
                                DoctorAvailability::insert([
                                'day_id' => $dayId,
                                'time_slot_id' => $timeSlotId,
                                'doctor_id' => $DoctorDetail->id
                                ]);
                            }
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


    /*public function get_review_rating_at_doctor_app(Request $request){
        $accessToken =  $request->header('accessToken');
        if( !empty( $accessToken ) ) {
            $DOCTOR_DETAIL = User::Where(['remember_token' => $accessToken, 'user_type' => 1])->first();
            if(count($DOCTOR_DETAIL)){
                $reviews = Review::where(['doctor_id' => $DOCTOR_DETAIL->id , 'status_by_doctor' => 0])->get();
                $response = [
                    'message' => __('messages.success.success'),
                    'response' => $reviews
                ];
                return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
            }else{
                $response['message'] = trans('messages.invalid.detail');
                return response()->json($response,401);
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
          return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }

    public function change_status_of_reviews_from_doctor_app(Request $request)
    {
        $accessToken =  $request->header('accessToken');
        $review_id = $request->review_id;
        $acceptOrReject = $request->acceptOrReject; // 1 for accept / 2 for reject
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
                    $REVIEW_DETAIL = Review::where(['doctor_id' => $DOCTOR_DETAIL->id , 'id' => $review_id , 'status_by_doctor' => 0])->first();
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
                return response()->json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
            }
        }else {
            $Response = [
                'message'  => trans('messages.required.accessToken'),
            ];
            return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
        }
    }*/
}
