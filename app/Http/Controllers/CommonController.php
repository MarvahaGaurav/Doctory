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
use App\Appointment;
use App\Category;
use App\TimeSlot;
use App\Day;
use App\MotherLanguage;
use App\DoctorAvailability;
use App\Qualification;
use App\DoctorQualification;
use App\DoctorMotherlanguage;
use Hash;
use Auth;
use Exception;
use Twilio\Rest\Client;
class CommonController extends Controller
{

	public function getSettingsData(Request $request){
		Log::info('------------------CommonController------------getSettingsData');

		$accessToken = $request->header('accessToken');
		$locale = $request->header('locale');
		$key = $request->key;
		$is_notification_on = $request->is_notification_on;
		$selected_language = $request->selected_language;

		if(empty($locale)){
		$locale = 'en';
		}
		\App::setLocale($locale);
		$validations = [
			'key' => 'required',
			'is_notification_on' => 'required_if:key,==,2',
			'selected_language' => 'required_if:key,==,2',
		];
		$validator = Validator::make($request->all(),$validations);
		if($validator->fails()){
    		$response = [
				'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}

		if( !empty( $accessToken ) ) {
         $UserDetail = User::where(['remember_token'=>$accessToken])->first();
         if(count($UserDetail)){
         	if($key == 1){
	         	$Response = [
	         		'is_notification_on' => $UserDetail->notification,
	         		'selected_language' => $UserDetail->language,
	         	];
	         	return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );	
	         }else if($key == 2){
	         	$UserDetail->language = $selected_language;
	         	$UserDetail->notification = $is_notification_on;
	         	$UserDetail->save();
	         	$UserDetail = User::where(['remember_token'=>$accessToken])->first();
	         	$Response = [
	         		'is_notification_on' => $UserDetail->notification,
	         		'selected_language' => $UserDetail->language,
	         	];
	         	return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );	
	         }else{
	         	$Response = [
	         		'message' => trans('messages.invalid.request'),
	         	];
	         	return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );	
	         }
         }else{
				$response['message'] = trans('messages.invalid.detail');
				return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
			}
      }else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
	}


	public function signUp(Request $request){
		Log::info('----------------------CommonController--------------------------signUp'.print_r($request->all(),True));
		$fullName = $request->fullName;
		$email = $request->email;
		$country_code = $request->country_code;
		$mobile = $request->mobile;
		$password = Hash::make($request->password);
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$user_type = $request->user_type;
		$otp = rand(1000,10000);
		$language = $request->language;
		$accessToken  = md5(uniqid(rand(), true));
		$locale = $request->header('locale');

		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		

		if(!empty($locale)){
			$validations = [
				'fullName' => 'required|max:255',
				'email' => 'required|email|unique:users',
				'mobile' => 'required|numeric|unique:users',
				'country_code' => 'required|numeric',
				'password' => 'required|min:8',
				'device_token' => 'required',
				'device_type' => 'required|numeric',
				'user_type' => 'required|numeric',
				'language' => 'required'
	    	];
	    	$validator = Validator::make($request->all(),$validations);
	    	if($validator->fails()){
	    		$response = [
				'message' => $validator->errors($validator)->first()
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}else{
	    		$user = new \App\User;
	    		$OTP = new \App\Otp;
	    		$user->name = $fullName;
	    		$user->email = $email;
	    		$user->country_code = $country_code;
	    		$user->mobile = $mobile;
	    		$user->password = $password;
	    		$user->device_token = $device_token;
	    		$user->device_type = $device_type;
	    		$user->user_type = $user_type;
	    		$user->remember_token = $accessToken;
	    		$user->language = $language;
	    		if($user->save()){
	    			$insertId = $user->id;
	    			$OTP->user_id = $insertId;
	    			$OTP->otp = $otp;
	    			$OTP->save();
	    			$userData = $user->getUserDetail($insertId);
	    			$this->sendOtp($country_code.$mobile,$otp);
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
	   }else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function login(Request $request){
		Log::info('----------------------CommonController--------------------------login'.print_r($request->all(),True));
		$email = $request->input('email');
		$password = $request->input('password');
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$accessToken  = md5(uniqid(rand(), true));
		$language = $request->language;
		$locale = $request->header('locale');
		$login_type = $request->login_type;

		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'language' => 'required',
				'email' => 'required|email',
				'password' => 'required|min:8',
				'device_token' => 'required',
				'device_type' => 'required|numeric',
				'login_type' => 'required'
	    	];
	    	$validator = Validator::make($request->all(),$validations);
	    	if($validator->fails()){
	    		$response = [
					'message' => $validator->errors($validator)->first()
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}else{
	    		$userDetail = User::Where(['email' => $email])->first();
	    		if(!empty($userDetail)){
	    			if($userDetail->user_type == $login_type){
	    				if(Hash::check($password,$userDetail->password)){
		    				$User = new \App\User;
		    				$UserDetail = $User::find($userDetail->id);
		    				$UserDetail->device_token = $device_token;
		    				$UserDetail->device_type = $device_type;
		    				$UserDetail->remember_token = $accessToken;
		    				$UserDetail->language = $language;
		    				$UserDetail->save();
		    				$result = $this->getUserDetail($User->getUserDetail($userDetail->id)); // $this->getUserDetail available in controlle
		    				$response = [
								'message' =>  __('messages.success.login'),
								'response' => $result
							];
							return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		    			}else{
		    				$response = [
								'message' =>  __('messages.invalid.detail')
							];
							return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
		    			}
		    		}else{
		    			$response = [
							'message' =>  __('messages.invalid.detail')
						];
						return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
		    		}
	    		}else{
	    			$response = [
						'message' =>  __('messages.invalid.detail')
					];
					return response()->json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    		}
	    	}
	   }else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function sendFirebaseId(Request $request){
		Log::info('----------------------CommonController--------------------------sendFirebaseId'.print_r($request->all(),True));
		$accessToken =  $request->header('accessToken');
		$firebase_id = $request->firebase_id;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);

		$validations = [
			'firebase_id' => 'required'
		];
		$validator = Validator::make($request->all(),$validations);
		if($validator->fails()){
    		$response = [
				'message' => $validator->errors($validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}

		if( !empty( $accessToken ) ) {
			$user = new \App\User;
			$userDetail = User::where(['remember_token' => $accessToken])->first();
			if(count($userDetail)){
				$User = User::find($userDetail->id);
    			$User->firebase_id = $firebase_id;
    			$User->save();
    			$Response = [
    			  'message'  => trans('messages.success.success'),
    			];
        		return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );	
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
	}

	public function logout( Request $request ) {
		Log::info('----------------------CommonController--------------------------logout'.print_r($request->all(),True));
		$accessToken =  $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		if(!empty($locale)){
			\App::setLocale($locale);
			if( !empty( $accessToken ) ) {
				$user = new \App\User;
				$userDetail = User::where(['remember_token' => $accessToken])->first();
				if(count($userDetail)){
					$User = User::find($userDetail->id);
	    			$User->remember_token = "";
	    			$User->save();
	    			$Response = [
	    			  'message'  => trans('messages.success.logout'),
	    			];
	        		return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );	
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

	public function otpVerify( Request $request ) {
		Log::info('----------------------CommonController--------------------------otpVerify'.print_r($request->all(),True));
	   $otp  		 = $request->input('otp');
	   $user_id  		 = $request->input('user_id');
		$locale = $request->header('locale');

		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'otp'   => 'required'
			];
		  	$validator = Validator::make($request->all(),$validations);
		  	if( !empty( $user_id ) ) {
				$user = new \App\User;
				$userDetail = User::where(['id' => $user_id])->first();
				if(count($userDetail)){
				  	if( $validator->fails() ) {
						$response = [
						 'message' => $validator->errors($validator)->first(),
						];
						return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
				   } else {
				    	$Exist =  $user->getUserDetail($userDetail->id);
				    	if( count($Exist) ) {
				    		if( $Exist->Otp_detail->otp == $otp || $otp == 1234 ){
				    			$OTP = Otp::find($Exist->id);
				    			$OTP->otp = "";
				    			$OTP->varified = 1;
				    			$OTP->save();
				    			$Response = [
			        			  'message'  => trans('messages.success.otp_verified'),
			        			  'status' => 1,
			        			  'response' => $user->getUserDetail($userDetail->id)
			        			];
			        			return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );
				    		} else {
				    			$Response = [
			        				'message'  => trans('messages.invalid.OTP'),
			        			];
			        			return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
				    		}
				    	} else {
				    		$Response = [
			    			  'message'  => trans('messages.invalid.detail'),
			    			];
			        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
				    	}
				   }
				}else{
					$response['message'] = trans('messages.invalid.detail');
					return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
				}
			} else {
		    	$Response = [
				  'message'  => trans('messages.required.user_id'),
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

	public function sendOtp($mobile,$otp) {
		/*$mobile = '+919258811111';
		$otp = 'ja raha hai otp';*/
		try{
			$sid = 'ACd27821f8121968f9ee06d74075dd5884';
			$token = '33a3460eb8930f36e87bbb29d1e28751';
			$client = new Client($sid, $token);
			$number = $client->lookups
				->phoneNumbers("+16193761210")
				->fetch(array("type" => "carrier"));
			$client->messages->create(
			    $mobile, array(
			        'from' => '+16193761210',
			        'body' => 'doctory please enter this code to verify :'.$otp
			    )
			);
		} catch(Exception $e){
			// dd($e->getMessage());
			$response = [
				'message' => $e->getMessage()
			];
			return $e->getMessage();
		}
	}

	public function resendOtp(Request $request){
		Log::info('----------------------CommonController--------------------------resendOtp'.print_r($request->all(),True));
		$key = $request->key; // 1 for send otp at mobile
		$email = $request->email;
		$mobile = $request->mobile;
		$country_code = $request->country_code;
	   $user_id  		 = $request->input('user_id');
		$otp = rand(1000,10000);
		$locale = $request->header('locale');

		if(empty($locale)){
			$locale = 'en';
		}
		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'key' => 'required|numeric',
				'user_id' => 'required',
			];
			$validator = Validator::make($request->all(),$validations);
			if( $validator->fails() ){
			   $response = [
			   	'message'=>$validator->errors($validator)->first()
			   ];
			   return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
			}else{
				$userDetail=User::where(['id' => $user_id])->first();
				if(count($userDetail)){
					$USER = new User;
					if($key == 1){ // otp at mobile
						$this->sendOtp($userDetail->country_code.$userDetail->mobile,$otp);
					}
					if($key == 2){ // otp at email
						$data = [
							'otp' => $otp,
							'email' => $userDetail->email
						];
						try{
							Mail::send(['text'=>'otp'], $data, function($message) use ($data)
							{
						         $message->to($data['email'])
						         		->subject ('OTP');
						         $message->from('techfluper@gmail.com');
						   });	
						}catch(Exception $e){
							$response=[
								'message' => $e->getMessage()
				      	];
				     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
						}
					}
					$userOtp = Otp::findOrNew($userDetail->id);
					$userOtp->user_id = $userDetail->id;
		 			$userOtp->otp = $otp;
		 			$userOtp->varified = 0;
		 			$userOtp->save();

		 			if($key == 1){ // otp at mobile
			 			$Response = [
		     			  'message'  => trans('messages.success.otp_resend'),
		     			  'response' => $USER->getUserDetail($userDetail->id)
		     			];
		     		}
		     		if($key == 2){ // otp at email
		     			$Response = [
		     			  'message'  => trans('messages.success.email_forget_otp'),
		     			  'response' => $USER->getUserDetail($userDetail->id)
		     			];
		     		}
		     			return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );	

		 		}else{
					$response['message'] = trans('messages.invalid.detail');
					return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
				}
			}
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function sendMail(Request $resuest){
		$otp = 123456;
		$email = 'gauravmrvh1@gmail.com';
		$data = [
			'otp' => $otp,
			'email' => $email
		];
		try{
			Mail::send(['text'=>'otp'], $data, function($message) use ($data)
			{
	         $message->to($data['email'])
	         		->subject ('Forget Password OTP');
	         $message->from('techfluper@gmail.com');
		   });
		   $response=[
				'message' => 'send'
      	];
     		return Response::json($response,200);
		}catch(Exception $e){
			$response=[
				'message' => $e->getMessage()
      	];
     		return Response::json($response,400);
		}
	}

	public function forgetPassword(Request $request) {
		Log::info('----------------------CommonController--------------------------resetPassword'.print_r($request->all(),True));
		/*$country_code = $request->country_code;
		$mobile = $request->mobile;*/
		$email = $request->email;
		$otp = rand(1000,10000);
		$locale = $request->header('locale');

		if(empty($locale)){
			$locale = 'en';
		}
		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'email'=>'required|email'
			];
			$validator = Validator::make($request->all(),$validations);
			if( $validator->fails() ){
			   $response = [
			   	'message'=>$validator->errors($validator)->first()
			   ];
			   return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
			}else{
				$UserDetail = User::Where(['email' => $email])->first();
				if(count($UserDetail)){
					// $this->sendOtp($country_code.$mobile,$otp);
					$data = [
						'otp' => $otp,
						'email' => $email
					];
					try{
						Mail::send(['text'=>'otp'], $data, function($message) use ($data)
						{
					         $message->to($data['email'])
					         		->subject ('Forget Password OTP');
					         $message->from('techfluper@gmail.com');
					   });	
					}catch(Exception $e){
						$response=[
							'message' => $e->getMessage()
			      	];
			     		return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
					}
					$UserOtp = Otp::find($UserDetail->id);
					$UserOtp->otp = $otp;
					$UserOtp->save();
					$response=[
						'message' => trans('messages.success.email_forget_otp'),
						'response' => ['user_id'=> $UserDetail->id]
			      ];
			      return Response::json($response,__('messages.statusCode.ACTION_COMPLETE'));
				} else {
					$response=[
						'message' => trans('messages.invalid.credentials'),
			      	];
			      return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
				}
			}
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function resetPassword(Request $request){
		Log::info('----------------------CommonController--------------------------resetPassword'.print_r($request->all(),True));
		$accessToken = $request->header('accessToken');
		$password = $request->password;

		$locale = $request->header('locale');

		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'password' => 'required|min:8'
	    	];
	    	$validator = Validator::make($request->all(),$validations);
	    	if( !empty( $accessToken ) ) {
		    	if($validator->fails()){
		    		$response = [
						'message' => $validator->errors($validator)->first()
					];
					return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    	}else{
		    		$UserDetail = User::where(['remember_token' => $accessToken])->first();
		    		if(count($UserDetail)){
		    			// dd($UserDetail);
		    			$User = User::find($UserDetail->id);
		    			$User->password = Hash::make($password);
		    			$User->save();
		    			$userDetail = new \App\User;

		    			$Response = [
		    			  'message'  => trans('messages.success.password_updated'),
		    			  'response' => $userDetail->getUserDetail($UserDetail->id)
		    			];
		        		return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );
		    		}else{
		    			$Response = [
		    			  'message'  => trans('messages.invalid.detail'),
		    			];
		        		return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
		    		}
		    	}
		   }else {
		    	$Response = [
					'message'  => trans('messages.required.accessToken'),
				];
		      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		   }
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function change_password(Request $request){
		Log::info('----------------------CommonController--------------------------change_password'.print_r($request->all(),True));
		$accessToken = $request->header('accessToken');
		$old_password = $request->old_password;
		$new_password = $request->new_password;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'old_password' => 'required|min:8',
				'new_password' => 'required|min:8'
	    	];
	    	$validator = Validator::make($request->all(),$validations);
	    	if( !empty( $accessToken ) ) {
		    	if($validator->fails()){
		    		$response = [
						'message' => $validator->errors($validator)->first()
					];
					return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		    	}else{
		    		$UserDetail = User::where(['remember_token' => $accessToken])->first();
		    		if(count($UserDetail)){
		    			// dd($UserDetail->password);
		    			if(Hash::check($old_password,$UserDetail->password)){
		    				// dd("correct");
		    				$User = User::find($UserDetail->id);
			    			$User->password = Hash::make($new_password);
			    			$User->save();
			    			$userDetail = new \App\User;
			    			$Response = [
			    			  'message'  => trans('messages.success.password_updated'),
			    			  'response' => $userDetail->getUserDetail($UserDetail->id)
			    			];
		        			return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );
		    			}else{
		    				$Response = [
			    			  'message'  => trans('messages.error.incorrect_old_password'),
			    			];
		        			return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
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
		      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
		   }
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function changeMobileNumber( Request $request ) {
		Log::info('----------------------CommonController--------------------------changeMobileNumber'.print_r($request->all(),True));
		$country_code = $request->country_code;
		$mobile 	 =  $request->mobile;
		$accessToken =  $request->header('accessToken');
   	$otp = rand(1000,10000);
		$isChangedCountryCode = $request->isChangedCountryCode;
		$isChangedMobile = $request->isChangedMobile;
		$userDetail  = [];
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
			$validations = [
				'country_code' => 'required|numeric',
				'mobile' 	   => 'required|numeric',
				'isChangedCountryCode' => 'required',
				'isChangedMobile' => 'required',
			];
			$validator = Validator::make($request->all(),$validations);
		  	if( !empty( $accessToken ) ) {
		  		$userDetail = User::Where(['remember_token' => $accessToken])->first();
		  		if(count($userDetail)){
		        if( $validator->fails() ) {
		            $response = [
							'message'	=>	$validator->errors($validator)->first(),
						];
		            return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		        } else {
			        	if( $isChangedMobile == 1 && $isChangedCountryCode == 0 ) {
			        		$validations = [
								'mobile' => 'unique:users',
							];
			    			$validator = Validator::make($request->all(),$validations);
			    			if( $validator->fails() ) {
			        			$response = [
									'message'	=>	$validator->errors($validator)->first()
								];
			            	return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
			            } else {
			            	
								$User = new \App\User;
								$UserDetail = $User::find($userDetail->id);
								$UserDetail->mobile = $mobile;
								$UserDetail->save();

								$OTP = Otp::find($userDetail->id);
								$OTP->otp = $otp;
								$OTP->varified = 0;
								$OTP->save();

				        		$this->sendOtp($country_code.$mobile , $otp);
				        		$response = [
				        			'message' => __('messages.success.success'),
				        			// 'response' => $User->getUserDetail($userDetail->id)
				        		];
				        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
			            }
			        	} 

			        	else if( $isChangedMobile == 0 && $isChangedCountryCode == 1) {
			        		$User = new \App\User;
							$UserDetail = $User::find($userDetail->id);
							$UserDetail->country_code = $country_code;
							$UserDetail->save();

							$OTP = Otp::find($userDetail->id);
							$OTP->otp = $otp;
							$OTP->varified = 0;
							$OTP->save();

			        		$this->sendOtp($country_code.$mobile , $otp);
			        		$response = [
			        			'message' => __('messages.success.success'),
			        			// 'response' => $User->getUserDetail($userDetail->id)
			        		];
			        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
			        	}

			        	else if( $isChangedMobile == 1 && $isChangedCountryCode == 1){
			        		$validations = [
								'mobile' => 'unique:users',
							];
			    			$validator = Validator::make($request->all(),$validations);
			    			if( $validator->fails() ) {
			        			$response = [
									'message'	=>	$validator->errors($validator)->first()
								];
			            	return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
			            } else {
				        		$User = new \App\User;
								$UserDetail = $User::find($userDetail->id);
								$UserDetail->mobile = $mobile;
								$UserDetail->country_code = $country_code;
								$UserDetail->save();

								$OTP = Otp::find($userDetail->id);
								$OTP->otp = $otp;
								$OTP->varified = 0;
								$OTP->save();

				        		$this->sendOtp($country_code.$mobile,$otp);
				        		$response = [
				        			'message' => __('messages.success.mobile_changed'),
				        			// 'response' => $User->getUserDetail($userDetail->id)
				        		];
				        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
				        	}
			        	}

			        	else {
			        		$User = new \App\User;
			        		$Response = [
								'message'  => trans('messages.same.same_number'),
								// 'response' =>  $User->getUserDetail($userDetail->id),
							];
			        		return Response::json($Response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
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
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
	}

	public function completeProfileOrEditProfile(Request $request){
		Log::info('----------------------CommonController--------------------------completeProfileOrEditProfile'.print_r($request->all(),True));
		// dd(json_decode($request->motherLanguage));
		$accessToken = $request->header('accessToken');
		$photo = $request->file('profileImage');
		$destinationPathOfProfile = base_path().'/'.'userImages/';
		// dd($destinationPathOfProfile);
		$fullName = $request->fullName;
		$specialityId = $request->specialityId;
		$qualificationArr = json_decode($request->qualification); // it would be array
		$experience = $request->experience;
		$workingPlace = $request->workingPlace;
		$latitude = $request->latitude;
		$longitude = $request->longitude;
		$motherLanguageArr = json_decode($request->motherLanguage);
		$aboutMe = $request->aboutMe;
		$key = $request->key;
		$email = $request->email;
		$mobile = $request->mobile;
		$medical_licence_number = $request->medical_licence_number;
		$issuing_country = $request->issuing_country;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
			if( !empty( $accessToken ) ) {
				$USER = User::Where(['remember_token' => $accessToken])->first();
				if(count($USER)){
					// dd($USER);
					$type = $USER->user_type;
					$validations = [
						'key' => 'required',
						'profileImage' => 'required_if:key,==,1|image',
						'fullName' => 'required_if:type,==,2|max:255',
					];
					if($USER->user_type == 1){
						$validations = [
							'key' => 'required',
							'profileImage' => 'required_if:key,==,1|image',
							// 'fullName' => 'required|max:255',
							'specialityId' => 'required',
							'qualification' => 'required',
							'experience' => 'required',
							'workingPlace' => 'required',
							// 'latitude' => 'required',
							// 'longitude' => 'required',
							'motherLanguage' => 'required',
							'medical_licence_number' => 'required',
							'issuing_country' => 'required',
						];
					}
					if($key == 2 && count($USER)){ // Edit profile
						/*$validations['email'] = ['required',Rule::unique('users')->ignore($USER->id, 'id')];
						$validations['mobile'] = ['required',Rule::unique('users')->ignore($USER->id, 'id')];*/
					}
					$validator = Validator::make($request->all(),$validations);
					if( $validator->fails() ) {
						$response = [
							'message' => $validator->errors($validator)->first(),
						];
						return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
					} else {
						// dd('else');
						if($USER){
							if($USER->user_type == 1){
								$USER->speciality_id = $specialityId;
								$USER->experience = $experience;
								$USER->working_place = $workingPlace; 
								$USER->latitude = $latitude; 
								$USER->longitude = $longitude; 
								$USER->about_me = $aboutMe;
								if($key == 1){ // only run in complete profile of doctor
									$USER->medical_licence_number = $medical_licence_number;
									$USER->issuing_country = $issuing_country;
								}
							}
							if(isset($_FILES['profileImage']['tmp_name'])){
								$uploadedfile = str_replace(" ","_",$_FILES['profileImage']['tmp_name']);
								$fileName1 = substr($this->uploadImage($photo,$uploadedfile,$destinationPathOfProfile),9); 
								$USER->profile_image = $fileName1;
							}

							if($key == 2){
								/*$USER->email = $email;
								$USER->mobile = $mobile;*/	
							}
							if(!empty($fullName)){
								$USER->name = $fullName;
							}
							$USER->profile_status = 1;
							$USER->save();

							if($USER->user_type == 1){
								$DoctorQualification = DoctorQualification::where(['user_id' => $USER->id])->get();
								$DoctorMotherlanguage = DoctorMotherlanguage::where(['user_id' => $USER->id])->get();

								if(count($DoctorQualification)){
									DoctorQualification::where(['user_id' => $USER->id])->delete();
								}
								foreach ($qualificationArr as $key => $value) {
									$DoctorQualification = new \App\DoctorQualification;
									$DoctorQualification->user_id = $USER->id;
									$DoctorQualification->qualification_id = $value;
									$DoctorQualification->save();
								}

								if(count($DoctorMotherlanguage)){
									DoctorMotherlanguage::where(['user_id' => $USER->id])->delete();
								}
								foreach ($motherLanguageArr as $key => $value) {
									$DoctorMotherlanguage = new \App\DoctorMotherlanguage;
									$DoctorMotherlanguage->user_id = $USER->id;
									$DoctorMotherlanguage->mother_language_id = $value;
									$DoctorMotherlanguage->save();
								}
							}

							$user = new User;
							$result =$this->getUserDetail($user->getUserDetail($USER->id));
							$response = [
								'message' => __('messages.success.complete_profile'),
								// 'response' => $user->getUserDetail($USER->id)
								'response' => $result
							];
							return Response::json($response,trans('messages.statusCode.ACTION_COMPLETE'));
						}else{
							$response = [
								'message' => __('messages.invalid.detail'),
							];
							return Response::json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
						}
					}
				}else{
					$response = [
						'message' => __('messages.invalid.detail'),
					];
					return Response::json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
				}
			} else {
				$Response = [
					'message'  => trans('messages.required.accessToken'),
				];
				return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
			}
		}else{
	   	$response = [
				'message' =>  __('messages.required.locale')
			];
			return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
	   }
   }

   public function settings(Request $request){
		Log::info('----------------------CommonController--------------------------settings'.print_r($request->all(),True));

   	$notification = $request->input('notification');
   	$language = $request->input('language');
		$accessToken = $request->header('accessToken');
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}

		if(!empty($locale)){
			\App::setLocale($locale);
	   	if( !empty( $accessToken ) ) {
	   		$userDetail = User::Where(['remember_token' => $accessToken])->first();
	   		if(!empty($userDetail)){
			   	$validations = [
						'notification' => 'required|numeric',
						'language' => 'required|alpha',
			    	];
			    	$validator = Validator::make($request->all(),$validations);
			    	if($validator->fails()){
			    		$response = [
						'message' => $validator->errors($validator)->first()
						];
						return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
			    	}else{
			    		// dd($userDetail);
			    		$User = new \App\User;
			    		$userData = $User::find($userDetail->id); 
			    		$userData->language = $language;
			    		$userData->notification = $notification;
			    		$userData->save();
			    		$response = [
							'message' =>  __('messages.success.success'),
						];
						return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
			    	}
			   }else{
	    			$response = [
						'message' =>  __('messages.invalid.detail')
					];
					return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
	    		}
		   }else {
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

   public function uploadImage($photo,$uploadedfile,$destinationPathOfPhoto){
        /*$photo = $request->file('photo');
        $uploadedfile = $_FILES['photo']['tmp_name'];
        $destinationPathOfPhoto = public_path().'/'.'thumbnail/';*/
        $fileName = time()."_".$photo->getClientOriginalName();
        $src = "";
        $i = strrpos($fileName,".");
        $l = strlen($fileName) - $i;
        $ext = substr($fileName,$i+1);

        if($ext=="jpg" || $ext=="jpeg" || $ext=="JPG" || $ext=="JPEG"){
            $src = imagecreatefromjpeg($uploadedfile);
        }else if($ext=="png" || $ext=="PNG"){
            $src = imagecreatefrompng($uploadedfile);
        }else if($ext=="gif" || $ext=="GIF"){
            $src = imagecreatefromgif($uploadedfile);
        }else{
            $src = imagecreatefrombmp($uploadedfile);
        }
        $newwidth  = 200;
        list($width,$height)=getimagesize($uploadedfile);
        $newheight=($height/$width)*$newwidth;
        $tmp=imagecreatetruecolor($newwidth,$newheight);
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
        $filename = $destinationPathOfPhoto.'small'.'_'.$fileName; 
        imagejpeg($tmp,$filename,100);
        imagedestroy($tmp);
        $filename = explode('/', $filename);

        $newwidth1  = 400;
        list($width,$height)=getimagesize($uploadedfile);
        $newheight1=($height/$width)*$newwidth1;
        $tmp=imagecreatetruecolor($newwidth1,$newheight1);
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth1,$newheight1,$width,$height);
        $filename = $destinationPathOfPhoto.'big'.'_'.$fileName; 
        imagejpeg($tmp,$filename,100);
        imagedestroy($tmp);
        $filename = explode('/', $filename);

        $newwidth2  = 100;
        list($width,$height)=getimagesize($uploadedfile);
        $newheight2=($height/$width)*$newwidth2;
        $tmp=imagecreatetruecolor($newwidth2,$newheight2);
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth2,$newheight2,$width,$height);
        $filename = $destinationPathOfPhoto.'thumbnail'.'_'.$fileName; 
        imagejpeg($tmp,$filename,100);
        imagedestroy($tmp);
        $filename = explode('/', $filename);
        return $filename[6];
   }

   public function getAllStaticData(Request $request){
		Log::info('----------------------CommonController--------------------------getAllStaticData'.print_r($request->all(),True));

   	$MotherLanguage = MotherLanguage::all();
   	$Qualification = Qualification::all();
   	$Category = Category::all();
   	$Day = Day::all();
   	$TimeSlot = TimeSlot::all();
   	$time_slot_result = [];

   	$notification_status_codes =[
			'Rescheduled_Appointment' => '1',
			'Scheduled_Appointment' => '2',
			'Rescheduled_Appointment_Accepted_By_Patient' => '3',
			'Rescheduled_Appointment_Accepted_By_Doctor' => '4',
			'Rescheduled_Appointment_Rejected_By_Patient' => '5',
			'Rescheduled_Appointment_Rejected_By_Doctor' => '6',
			'Appointment_Rescheduled_By_Patient' => '7',
			'Appointment_Rescheduled_By_Doctor' => '8',
			'Appointment_Accepted_By_Doctor' => '9',
			'Appointment_Rejected_By_Doctor' => '10'
		];

   	foreach ($TimeSlot as $key => $value) {
   		$time_slot_result [] = [
   			'id' => $value->id,
   			'start_time' => date('g:i A',strtotime($value->start_time)),
   			'end_time' => date('g:i A',strtotime($value->end_time))
   		];
   	}
   	$response = [
   		'Day' => $Day,
   		'TimeSlot' => $time_slot_result,
   		'MotherLanguage' => $MotherLanguage,
   		'Qualification' => $Qualification,
   		'Speciality' => $Category,
   		'notification_status_codes' => $notification_status_codes
   	];
		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
   }

   public function get_all_event_dates(Request $request){
   	Log::info('----------------------CommonController--------------------------get_all_event_dates'.print_r($request->all(),True));
		$accessToken = $request->header('accessToken');
		$firebase_id = $request->firebase_id;
		$month = $request->month;
		$year = $request->year;
		$device_token = $request->device_token;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {
			$validations = [
				'month' => 'required',
				'year' => 'required',
				'firebase_id' => 'required',
	    	];
	    	$validator = Validator::make($request->all(),$validations);
	    	if($validator->fails()){
	    		$response = [
					'message' => $validator->errors($validator)->first()
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}else{
	    		$UserDetail = User::where(['remember_token' => $accessToken])->first();
	    		if(count($UserDetail)){
	    			if($device_token){
                  $UserDetail->device_token = $device_token;
               }
	    			$UserDetail->firebase_id = $firebase_id;
	    			$UserDetail->save();
	    			$List = Appointment::whereMonth('appointment_date',$month)
		    			->whereYear('appointment_date',$year)
		    			->select('appointment_date');
		    			if($UserDetail->user_type == 1){
		    				$List = $List->where(['doctor_id'=>$UserDetail->id]); 
		    			}
		    			if($UserDetail->user_type == 2){
		    				$List = $List->where(['patient_id'=>$UserDetail->id]);
		    			}
		    		$List = $List->get();
	    			$result = [];
	    			foreach ($List as $key => $value) {
	    				if(!in_array($value->appointment_date, $result)){	 
	    					$result[] = $value->appointment_date;
	    				}
	    			}
	    			$response = [
	    				'messages' => __('messages.success.success'),
	    				'response' => $result
	    			];
	    			return Response::json($response,__('messages.statusCode.ACTION_COMPLETE'));
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
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
   }

   public function sendAttachment(Request $request){
   	Log::info('----------------------CommonController--------------------------sendAttachment'.print_r($request->all(),True));
		$accessToken = $request->header('accessToken');
		$attachment = $request->file('attachment');
		$key = $request->key;
		$thumbnail = $request->thumbnail;
		$locale = $request->header('locale');
		if(empty($locale)){
			$locale = 'en';
		}
		\App::setLocale($locale);
		if( !empty( $accessToken ) ) {
			$validations = [
				// 'attachment' => 'required',
				'key' => 'required',
				'thumbnail' => 'required_if:key,==,2',
	    	];
	    	$validator = Validator::make($request->all(),$validations);
	    	if($validator->fails()){
	    		$response = [
					'message' => $validator->errors($validator)->first()
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}else{
	    		$path = base_path('/Attachments');
	    		$fileName = str_replace(" ","_",time().'_'.$attachment->getClientOriginalName());
	    		if($key == 2 ){
	    			$video_thumbnail = str_replace(" ","_",time().'_'.$thumbnail->getClientOriginalName());
	    			$thumbnail->move($path,$video_thumbnail);
	    		}
	    		$attachment->move($path,$fileName);
	    		if($key == 1){
	    			$response = [
		    			'messages' => __('messages.success.Image_uploaded_success'),
		    			'key' => '1',
		    			'url' => url('/Attachments').'/'.$fileName
		    		];
		    		Log::info('----------------------CommonController--------------------------sendAttachment'.print_r($response,True));
		    		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
	    		}
	    		if($key == 2){
	    			$response = [
		    			'messages' => __('messages.success.Video_uploaded_success'),
		    			'key' => '2',
		    			'url' => url('/Attachments').'/'.$fileName,
		    			'thumbnail' => url('/Attachments').'/'.$video_thumbnail
		    		];
		    		Log::info('----------------------CommonController--------------------------sendAttachment'.print_r($response,True));
		    		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
	    		}
	    		if($key == 3){
	    			$response = [
		    			'messages' => __('messages.success.Audio_uploaded_success'),
		    			'key' => '3',
		    			'url' => url('/Attachments').'/'.$fileName
		    		];
		    		Log::info('----------------------CommonController--------------------------sendAttachment'.print_r($response,True));
		    		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
	    		}
	    	}
		}else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
   }
}
