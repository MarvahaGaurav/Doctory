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
use App\UserDetail;
use Hash;
use Auth;
use Exception;
use Twilio\Rest\Client;

class CommonController extends Controller
{
	public function signUp(Request $request){
		$fullName = $request->fullName;
		$email = $request->email;
		$mobile = $request->mobile;
		$password = Hash::make($request->password);
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$otp = rand(100000,1000000);
		$validations = [
			'fullName' => 'required|max:255',
			'email' => 'required|email|unique:users',
			'mobile' => 'required|numeric',
			'password' => 'required|min:8',
			'device_token' => 'required',
			'device_type' => 'required|numeric'
    	];
    	$validator = Validator::make($request->all(),$validations);
    	if($validator->fails()){
    		$response = [
			'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,400);
    	}else{
    		$user = new \App\User;
    		$user->name = $fullName;
    		$user->email = $email;
    		$user->mobile = $mobile;
    		$user->password = $password;
    		$user->device_token = $device_token;
    		$user->device_type = $device_type;
    		if($user->save()){
    			$userData = \App\User::find($user->id);
    			$this->sendOtp($mobile,$otp);
    			$response = [
					'message' =>  __('messages.success.success'),
					'response' => $userData
				];
				return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
    		}else{
    			$response = [
					'message' =>  __('messages.error.insert')
				];
				return response()->json($response,__('messages.statusCode.ERROR_IN_EXECUTION'));
    		}
    	}
	}

	public function sendOtp($mobile,$otp) {
		try{
			$sid = 'AC6ceef3619be02e48da4aba2512cc426b';
			$token = 'eeaa38187028b4a0a9c4f4e105162b6e';
			$client = new Client($sid, $token);
			$number = $client->lookups
					    ->phoneNumbers("+14154291712")
					    ->fetch(array("type" => "carrier"));
			$client->messages->create(
					    $mobile, array(
					        'from' => '+14154291712',
					        'body' => 'loovline please enter this code to verify :'.$otp
					    )
					);
		} catch(Exception $e){
			return 1;
		}
	}
}
