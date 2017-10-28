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
		$otp = rand(100000,1000000);
		$language = $request->language;
		$accessToken  = md5(uniqid(rand(), true));
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
	}

	public function login(Request $request){
		Log::info('----------------------CommonController--------------------------login'.print_r($request->all(),True));
		$email = $request->input('email');
		$password = $request->input('password');
		$device_token = $request->device_token;
		$device_type = $request->device_type;
		$accessToken  = md5(uniqid(rand(), true));
		$language = $request->language;
		$validations = [
			'email' => 'required|email',
			'password' => 'required',
			'device_token' => 'required',
			'device_type' => 'required|numeric',
			'language' => 'required'
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
    			if(Hash::check($password,$userDetail->password)){
    				$User = new \App\User;
    				$UserDetail = $User::find($userDetail->id);
    				$UserDetail->device_token = $device_token;
    				$UserDetail->device_type = $device_type;
    				$UserDetail->remember_token = $accessToken;
    				$UserDetail->language = $language;
    				$UserDetail->save();
    				$result = $this->getUserDetail($User->getUserDetail($userDetail->id));
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
    	}
	}

	public function otpVerify( Request $request ) {
		Log::info('----------------------CommonController--------------------------otpVerify'.print_r($request->all(),True));
	   $otp  		 = $request->input('otp');
		$accessToken = $request->header('accessToken');
		$validations = [
			'otp'   => 'required'
		];
	  	$validator = Validator::make($request->all(),$validations);
	  	if( !empty( $accessToken ) ) {
			$user = new \App\User;
			$userDetail = User::where(['remember_token' => $accessToken])->first();
			if(count($userDetail)){
			  	if( $validator->fails() ) {
					$response = [
					 'message' => $validator->errors($validator)->first(),
					];
					return Response::json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
			   } else {
			    	$Exist =  $user->getUserDetail($userDetail->id);
			    	if( count($Exist) ) {
			    		if( $Exist->Otp_detail->otp == $otp || $otp == 123456 ){
			    			$OTP = Otp::find($Exist->id);
			    			$OTP->otp = "";
			    			$OTP->varified = 1;
			    			$OTP->save();
			    			$Response = [
		        			  'message'  => trans('messages.success.success'),
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
			  'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
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
		$accessToken = $request->accessToken;
		$otp = rand(100000,1000000);

		$validations = [
			'key' => 'required|numeric',
			'accessToken' => 'required_if:key,==,1',
		];
		$validator = Validator::make($request->all(),$validations);
		if( $validator->fails() ){
		   $response = [
		   	'message'=>$validator->errors($validator)->first()
		   ];
		   return Response::json($response,__('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}else{
			$userDetail=User::where(['remember_token' => $accessToken])->first();
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

		 			$Response = [
        			  'message'  => trans('messages.success.success'),
        			  'response' => $USER->getUserDetail($userDetail->id)
        			];
        			return Response::json( $Response , trans('messages.statusCode.ACTION_COMPLETE') );		
		 		}else{
					$response['message'] = trans('messages.invalid.detail');
					return response()->json($response,trans('messages.statusCode.INVALID_ACCESS_TOKEN'));
				}
		}

	}

	public function forgetPassword(Request $request) {
		Log::info('----------------------CommonController--------------------------resetPassword'.print_r($request->all(),True));
		/*$country_code = $request->country_code;
		$mobile = $request->mobile;*/
		$email = $request->email;
		$otp = rand(100000,1000000);
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
					'message' => trans('messages.success.success'),
		      	];
		      return Response::json($response,__('messages.statusCode.ACTION_COMPLETE'));
			} else {
				$response=[
					'message' => trans('messages.invalid.request'),
		      	];
		      return Response::json($response,400);
			}
		}
	}

	public function resetPassword(Request $request){
		Log::info('----------------------CommonController--------------------------resetPassword'.print_r($request->all(),True));
		$accessToken = $request->header('accessToken');
		$password = $request->password;
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
	    			  'message'  => trans('messages.success.success'),
	    			  'response' => $userDetail->getUserDetail($UserDetail->id)
	    			];
	        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
	    		}else{
	    			$Response = [
	    			  'message'  => trans('messages.invalid.detail'),
	    			];
	        		return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
	    		}
	    	}
	   }else {
	    	$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , __('messages.statusCode.SHOW_ERROR_MESSAGE') );
	   }
	}

	public function changeMobileNumber( Request $request ) {
		Log::info('----------------------CommonController--------------------------changeMobileNumber'.print_r($request->all(),True));
		$country_code = $request->country_code;
		$mobile 	 =  $request->mobile;
		$accessToken =  $request->header('accessToken');
   	$otp = rand(100000,1000000);
		$isChangedCountryCode = $request->isChangedCountryCode;
		$isChangedMobile = $request->isChangedMobile;
		$userDetail  = [];
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
			        			'response' => $User->getUserDetail($userDetail->id)
			        		];
			        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		            }
		        	} 

		        	else if( $isChangedMobile == 0 && $isChangedCountryCode == 1) {
		        		// dd( "isChangedCountryCode" );
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
		        			'response' => $User->getUserDetail($userDetail->id)
		        		];
		        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		        	}

		        	else if( $isChangedMobile == 1 && $isChangedCountryCode == 1){
		        		// dd("both");

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
		        			'message' => __('messages.success.success'),
		        			'response' => $User->getUserDetail($userDetail->id)
		        		];
		        		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
		        	}
		        	else {
		        		$User = new \App\User;
		        		$Response = [
							'message'  => trans('messages.same.same_number'),
							'response' =>  $User->getUserDetail($userDetail->id),
						];
		        		return Response::json($Response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		        	}
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

	public function completeProfileOrEditProfile(Request $request){
		Log::info('----------------------CommonController--------------------------completeProfileOrEditProfile'.print_r($request->all(),True));
		$accessToken = $request->header('accessToken');
		$photo = $request->file('profileImage');
		$destinationPathOfProfile = base_path().'/'.'userImages/';
		$specialityId = $request->specialityId;
		$qualificationArr = $request->qualification; // it would be array
		$experience = $request->experience;
		$workingPlace = $request->workingPlace;
		$latitude = $request->latitude;
		$longitude = $request->longitude;
		$motherLanguageArr = $request->motherLanguage;
		$aboutMe = $request->aboutMe;
		$key = $request->key;
		$email = $request->email;
		$mobile = $request->mobile;
		$USER = User::Where(['remember_token' => $accessToken])->first();
		// dd($motherLanguageArr);
		if( !empty( $accessToken ) ) {
			$validations = [
				'key' => 'required|numeric',
				'profileImage' => 'required_if:key,==,1|image',
				'specialityId' => 'required|numeric',
				'qualification' => 'required|array',
				'experience' => 'required|numeric',
				'workingPlace' => 'required|alpha',
				'latitude' => 'required|numeric',
				'longitude' => 'required|numeric',
				'motherLanguage' => 'required|array',
			];
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
				if($USER){
					if(isset($_FILES['profileImage']['tmp_name'])){
						$uploadedfile = $_FILES['profileImage']['tmp_name'];
						$fileName1 = $this->uploadImage($photo,$uploadedfile,$destinationPathOfProfile); 	
						$USER->profile_image = $fileName1;
					}
					if($key == 2){
						// $USER->email = $email;
						// $USER->mobile = $mobile;	
					}
					$USER->speciality_id = $specialityId;
					$USER->experience = $experience;
					$USER->working_place = $workingPlace; 
					$USER->latitude = $latitude; 
					$USER->longitude = $longitude; 
					$USER->about_me = $aboutMe;
					$USER->profile_status = 1;
					$USER->save();

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

					$user = new User;
					$result =$this->getUserDetail($user->getUserDetail($USER->id));
					$response = [
						'message' => __('messages.success.success'),
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
		} else {
			$Response = [
				'message'  => trans('messages.required.accessToken'),
			];
			return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE') );
		}
   }

   public function getUserDetail($data){
   	// dd($data);
   	$qualification = [];
   	$DoctorMotherlanguage = [];
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
   		// dd($data['mother_language']);
   		foreach ($data['mother_language'] as $key => $value) {
   			// dd($value);
   			$DoctorMotherlanguageDetail = MotherLanguage::Where(['id' => $value->mother_language_id])->first();
   			// dd($DoctorMotherlanguageDetail);
   			$DoctorMotherlanguage[]=[
   				'id' => $value->id,
   				'user_id' => $value->user_id,
   				'mother_language_id' => $value->mother_language_id,
   				'qualification_name' => $DoctorMotherlanguageDetail['name']
   			];
   		}
   	}

   	$result = [
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
   		'available_status' => $data['available_status'],
   		'notification' => $data['notification'],
   		'mother_language' => $data['mother_language'],
   		'language' => $data['language'],
   		'created_at' => $data['created_at'],
   		'updated_at' => $data['updated_at'],
   		'speciality' => $data['speciality'],
   		'otp_detail' => $data['Otp_detail'],
   		'qualification' => $qualification,
   		'mother_language' => $DoctorMotherlanguage,
   	];
   	return $result;
   }

   public function settings(Request $request){
		Log::info('----------------------CommonController--------------------------settings'.print_r($request->all(),True));

   	$notification = $request->input('notification');
   	$language = $request->input('language');
		$accessToken = $request->header('accessToken');
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
				return response()->json($response,__('messages.statusCode.INVALID_CREDENTIAL'));
    		}
	   }else {
	    	$Response = [
			  'message'  => trans('messages.required.accessToken'),
			];
	      return Response::json( $Response , trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
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

        if($ext=="jpg" || $ext=="jpeg" ){
            $src = imagecreatefromjpeg($uploadedfile);
        }else if($ext=="png"){
            $src = imagecreatefrompng($uploadedfile);
        }else if($ext=="gif"){
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
   	
   	$response = [
   		'Day' => $Day,
   		'TimeSlot' => $TimeSlot,
   		'MotherLanguage' => $MotherLanguage,
   		'Qualification' => $Qualification,
   		'Speciality' => $Category,
   	];
		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
   }
}
