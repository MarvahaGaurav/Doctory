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
use App\DoctorAvailability;
use App\Category;
use Hash;
use Auth;
use Exception;


class DoctorController extends Controller
{
    public function getList(Request $request){
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

    public function getDoctorBySpecialityId(Request $request){
        $accessToken =  $request->header('accessToken');
        $speciality_id =  $request->speciality_id;
        if( !empty( $accessToken ) ) {
            $userDetail = User::Where(['remember_token' => $accessToken])->first();
            if(count($userDetail)){
                $validations = [
                    'speciality_id' => 'required|numeric',
                ];
                $validator = Validator::make($request->all(),$validations);
                if( $validator->fails() ) {
                    $response = [
                        'message'   =>  $validator->errors($validator)->first()
                    ];
                    return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
                } else {
                    $query = [
                        'status' => 1,
                        'user_type' => 1,
                        'speciality_id' => $speciality_id
                    ];
                    $list = User::getDoctorBySpecialityId($query);
                    $response = [
                        'message' => __('messages.success.success'),
                        'response' => $list
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

    
}
