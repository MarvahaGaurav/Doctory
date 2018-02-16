<?php

namespace App\Http\Controllers;
use \App\User;
use \App\Models\Category;
use \App\Models\Country;
use \App\Models\City;
use \App\Models\Area;
use \App\Models\RecentStores;
use \App\Models\UserFavourite; // user Favourite stores
use \App\Models\StoreType; // cuisine type
use \App\Models\Voucher; 
use \App\Models\LatLong; 
use \App\Models\UsedVoucher; 
use \App\Models\StoreImages; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;
use Hash;
use DB;
use Log;
use Response;
use \Carbon\Carbon;
use Mail;
use App\Models\ReviewRating;
class UserPanelController extends Controller
{

	public function save_QR_Image(Request $request){
		if($request->link){
			// if(!file_exists(public_path('QR_IMAGES/').$request->store_id.'.png')){
			if(!file_exists(base_path('QR_IMAGES/').$request->store_id.'.png')){
				$content = file_get_contents($request->link);
				// $fp = fopen(public_path('QR_IMAGES/').$request->store_id.'.png', "w");//local
				$fp = fopen(base_path('QR_IMAGES/').$request->store_id.'.png', "w");
				fwrite($fp, $content);
				fclose($fp);
			}
		}else{
			return 'false';
		}
	}

	public function login(Request $request){
		// session()->pull('mai_store_loggedin');
		if($request->method() == 'GET'){
			if(session('mai_store_loggedin')){
				$category = Category::all();
				$country = Country::all();
				$StoreId = session('mai_user_id');
				$user_detail = User::find(['id' => $StoreId ,'user_type' => 2])->first();	
				// return $user_detail;
				$city_list = "";
				$area_list = "";
				if($user_detail->store_city){
					$city_list = City::where(['country_id' => $user_detail->store_country])->get();
					$area_list = Area::where(['country_id' => $user_detail->store_country,'city_id' => $user_detail->store_city])->get();
				}
				// return view('UserPanel/storeDetail',compact('category','country','user_detail','city_list','area_list'));
				return redirect('UserPanel/index');
			}else{
				return view('UserPanel/home');
			}
		}
		if($request->method() == 'POST'){
			$validations = [
				'login_email' => 'required|email',
				'login_password' => 'required',
			];
			$Validator = Validator::make($request->all(),$validations);
			if($Validator->passes()){
				$remember = $request->loginkeeping;
				$email = $request->login_email;
				$password = $request->login_password;
				$user_detail = User::where(['email' => $email,'user_type' => 2])->first();
				if($user_detail){
					if(Hash::check($password,$user_detail->password)){
						$session_data = [
							'user_detail' => $user_detail,
							'mai_store_loggedin' => true,
							'mai_user_type' => 2,
							'mai_user_id' => $user_detail->id,
						];
						if($remember=='on') {
							setcookie('mai_user_panel_login_email',$email, time() + (86400 * 30), "/");
							setcookie('mai_user_panel_login_password',$password, time() + (86400 * 30), "/");
							setcookie('mai_user_panel_loginkeeping','on',time() + (86400 * 30), "/");
						}
						if($remember!='on') {
							setcookie('mai_user_panel_login_email', null, -1, '/');
							setcookie('mai_user_panel_login_password', null, -1, '/');
							setcookie('mai_user_panel_loginkeeping', null, -1, '/');
						}
						Session::put($session_data);
						return redirect('UserPanel/index');
					}else{
						// return redirect()->back()->with('invalid_password','Invalid password.');
						return redirect('UserPanel/login')->with('invalid_password','Invalid password.');
					}
				}else{
					// return redirect()->back()->with('invalid_detail','Invalid email or password.');
					return redirect('UserPanel/login')->with('invalid_detail','Invalid email or password.');
				}
			}else{
				return redirect('UserPanel/login')->withErrors($Validator)->withInput();
			}
		}
	}

	public function index(Request $request){
		if($request->method() == 'GET'){
			if(Session::get('mai_store_loggedin') == true){
				$category = Category::all();
				$country = Country::all();
				$StoreId = session('mai_user_id');
				// dd($StoreId);
				$user_detail = User::where(['id' => $StoreId ,'user_type' => 2])->first();	
				// return $user_detail->store_description;
				$Store_nationality_Type = StoreType::all();
				return view('UserPanel/storeDetail',compact('category','country','user_detail','city_list','area_list','Store_nationality_Type'));
			}else{
				return redirect('UserPanel/login');
			}
		}
	}

	public function sign_up(Request $request){
		if($request->method() == 'GET'){
			if(session('mai_store_loggedin')){
				return redirect('UserPanel/index');
			}else{
				return view('UserPanel/home');
			}
		}

		if($request->method() == 'POST'){
			$email = $request->email;
			$password = $request->password;

			$validations = [
				'email' => 'required|email|unique:users',
				'password' => 'required',
				'confirm_password' => 'required|same:password',
			];
			$messages = [
				'email.unique' => 'This email id already registered with us .Please use another id to register.'
			];
			$Validator = Validator::make($request->all(),$validations,$messages);
			if($Validator->passes()){
				$user_detail = User::firstOrCreate(['email' => $email , 'user_type' => 2 , 'password' => Hash::make($password)]);
				if($user_detail){
					$user_detail->created_at = time();
					$user_detail->updated_at = time();
					$user_detail->save();
					$session_data = [
						'user_detail' => $user_detail,
						'mai_store_loggedin' => true,
						'mai_user_type' => 2,
						'mai_user_id' => $user_detail->id,
					];
					Session::put($session_data);
					if(Session::get('mai_store_loggedin')){
						return redirect('UserPanel/index');
					}else{

					}
				}else{
					return redirect('UserPanel/sign_up');
				}
			}else{
				return redirect('UserPanel/sign_up')->withErrors($Validator)->withInput();
			}
		}
	}

	public function logout(Request $request){
		/*$session_data = [
			'mai_store_loggedin' => false,
			'mai_user_type' => '',
			'mai_user_id' => '',
		];
		Session::put($session_data);*/
		$user_id = Session::get('mai_user_id');
		$userDetail = User::find($user_id);
		if($userDetail){
			$session_data = [
				'mai_store_loggedin' => false,
				'mai_user_type' => '',
				'mai_user_id' => '',
			];
			Session::put($session_data);
			if(Session::get('mai_store_loggedin') == false){
				return redirect('/UserPanel/login');
			}else{
				$session_data = [
					'mai_store_loggedin' => true,
					'mai_user_type' => 2,
					'mai_user_id' => $userDetail->id,
				];
				Session::put($session_data);
				return redirect('UserPanel/index');
			}
		}
	}

	public function forget_password(Request $request){
		if($request->method() == 'GET'){
			return view('UserPanel/home');
		}

		if($request->method() == 'POST'){
			$forget_email = $request->forget_email;

			$validations = [
				'forget_email' => 'required|email',
			];
			$Validator = Validator::make($request->all(),$validations);
			if($Validator->passes()){
				$detail = User::where(['email' => $forget_email , 'user_type' => 2])->first();
				if(!empty($detail) && count($detail)) {
					$subject = "Forget Password";
					$header = "change password request";
					$user_pass_at_email = rand(1000000,100000);
					$db_pass = Hash::make($user_pass_at_email);
					// $accessToken  = md5(uniqid(rand(), true));
					// $link=url('')."/"."UserPanel/reset_password"."/".$accessToken;

					// $data = array('link'=>$link , 'email' => $detail->email);
					$data = array('link'=>$user_pass_at_email , 'email' => $detail->email);
					$ok = Mail::send(['text'=>'forget_password'], $data, function($message) use ($data){
					   $message->to($data['email'])
					   		->subject ('Forget Password Link');
					   $message->from('techfluper@gmail.com');
					});
					if (Mail::failures()) {
						return 'response showing failed emails';
					}else{
						// $detail->remember_token = $accessToken;
						$detail->password = $db_pass;
						$detail->save();
						return redirect('UserPanel/login')
						->with('mail_send','Password has been sent at your email.');
						// ->with('mail_send','Reset password link has been sent at your email.');
					}	
				}else{
					return redirect('UserPanel/forget_password')
						->with('email_not_exist','Please use correct merchant email id to get new password.');
				}
			}else{
				return redirect()->back()->withErrors($Validator);
			}
		}
	}

	public function reset_password(Request $request){
		$remember_token = $request->user_token;
		if(empty($remember_token)){
			return redirect('UserPanel/forget_password')->with('forget_token','Token is required.');
		}else{
			$detail = User::where(['remember_token' => $remember_token , 'user_type' => 2])->first();
			if($detail){
				dd('correct');
			}else{
				return redirect('UserPanel/forget_password')->with('invalid_request',__('messages.invalid.request'));
			}
		}
	}

	public function store_detail(Request $request){
		if($request->method() == 'POST'){
			$destinationPathOfLogo = base_path().'/'.'UserPanel/store_logo';
			$destinationPathOfCoverPic = base_path().'/'.'UserPanel/store_cove_pic';
			$validations = [
				'store_name'=>'required',
				'owner_name'=>'required',
				'phone_number'=>'required',
				'country_name'=>'required',
				'city_name'=>'required',
				'area_name'=>'required',
				'store_cuisine_type' => 'required',
				'store_type' => 'required',
				'address'=>'required',
			];
			$Validator = Validator::make($request->all(),$validations);
			if($Validator->passes()){
				// dd($request->all());
				$city_table_detail = City::firstOrCreate(['name' => $request->city_name , 'country_id' => $request->country_name]);

				// $area_table_detail = Area::firstOrCreate(['name' => $request->area_name, 'country_id' => $request->country_name, 'city_id' => $city_table_detail->id , 'latitude' => $request->latitude , 'longitude' => $request->longitude]);

				$area_table_detail = Area::firstOrCreate(['name' => $request->area_name, 'country_id' => $request->country_name, 'city_id' => $city_table_detail->id]);

				$area_table_detail->latitude = $request->latitude;
				$area_table_detail->longitude = $request->longitude;
				$area_table_detail->save();

				$area_latlong = LatLong::firstOrCreate(['area_id' => $area_table_detail->id , 'latitude' => $request->latitude , 'longitude' => $request->longitude ]);

				$store_cuisine_type = StoreType::firstOrCreate(['name' => $request->store_cuisine_type,'store_type' => $request->store_type]);
				// dd($store_cuisine_type);

				$logo = $request->file('logo');
				$cover_pic = $request->file('cover_pic');
				$StoreId = session('mai_user_id');
				$user_detail = User::find(['id' => $StoreId])->first();
				// return $user_detail;
				$user_detail->store_name = ucfirst($request->store_name);
				$user_detail->store_owner_name = ucfirst($request->owner_name);
				$user_detail->mobile = $request->phone_number;
				$user_detail->store_country = $request->country_name;
				$user_detail->store_city = $city_table_detail->id;
				$user_detail->store_area = $area_table_detail->id;
				$user_detail->store_type = $request->store_type;
				$user_detail->complete_profile_status = 1;
				$user_detail->store_address = $request->address;
				$user_detail->store_cuisine_type = $store_cuisine_type->id;


				$user_detail->store_description = $request->description;
				$user_detail->store_fb_link = $request->facebook_link;
				$user_detail->store_twitter_link = $request->twitter_link;
				$user_detail->store_linkedin_link = $request->instagram_link;
				$user_detail->store_services = $request->store_services;
				$user_detail->store_product = $request->store_product;
				$user_detail->store_off_days = $request->store_off_days;
				$user_detail->store_open_time = $request->store_open_time;
				$user_detail->store_close_time = $request->store_close_time;

				$user_detail->store_lat_long_id = $area_latlong->id;

				// return time($request->store_open_time);
				if(!empty($logo)){
					if(!empty($user_detail->store_logo) && file_exists(base_path().'/'.'UserPanel/store_logo/'.$user_detail->store_logo) ){
						unlink(base_path().'/'.'UserPanel/store_logo/'.$user_detail->store_logo);
					}
					$fileName = time()."_".$logo->getClientOriginalName();
					$logo->move( $destinationPathOfLogo , $fileName );
					$user_detail->store_logo = $fileName;
				}
				if(!empty($cover_pic)){
					if(!empty($user_detail->store_cover_pic) && file_exists(base_path().'/'.'UserPanel/store_cove_pic/'.$user_detail->store_cover_pic) ){
						unlink(base_path().'/'.'UserPanel/store_cove_pic/'.$user_detail->store_cover_pic);
					}
					$fileName = time()."_".$cover_pic->getClientOriginalName();
					$cover_pic->move( $destinationPathOfCoverPic , $fileName );
					$user_detail->store_cover_pic = $fileName;
				}
				$user_detail->save();
				return redirect()->back()->with('detail_updated','Store Detail Updated Successfully.');
			}else{
				return redirect()->back()->withInput()->withErrors($Validator); 
			}
		}
	}


	/////////////////////////////////////////////////
	//////    API's
	/////////////////////////////////////////////////

	public function home_data(Request $request){
     	$category_list = Category::all();
     	$recent_stores = RecentStores::all();
     	$latitude = $request->latitude;
     	$longitude = $request->longitude;
     	$country_name = '';
     	$country_id = "";

     	$validations = [
			'latitude' => 'required',
			'longitude' => 'required',
		];
		$Validator = Validator::make($request->all(),$validations);
    	if($Validator->fails()){
    		$response = [
			'message' => $Validator->errors($Validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}

		$store_list = $this->store_list_by_latlong($latitude,$longitude);
		$result = [];
		foreach ($store_list as $key => $value) {
    		$distance_count = $this->distance($value->store_area->latitude,$value->store_area->longitude,$latitude,$longitude,"K");
    		$result []= $value;
    		$result [$key]['distance_count']= $distance_count;
    	}

     	$response = [
     		'message' => __('messages.success.success'),
     		// 'country_id' => $country_id,
     		'category_list' => $category_list,
     		'store_list' => $result,
     		'recent_stores' => $recent_stores
     	];
     	return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
 	}

 	public function get_data_by_category(Request $request){
 		// dd($request->all());
 		$category_id = $request->category_id;
 		$country_id = $request->country_id;
 		$latitude = $request->latitude;
     	$longitude = $request->longitude;

 		$validations = [
			'category_id' => 'required',
			'country_id' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
		];
		$Validator = Validator::make($request->all(),$validations);
    	if($Validator->fails()){
    		$response = [
			'message' => $Validator->errors($Validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}
    	$near_you = count($this->store_list_by_latlong($latitude,$longitude));
    	// $Store_list = User::where(['complete_profile_status' => 1 , 'user_type' => 2 , 'store_country' => $country_id])->get();
    	$city_list = City::where(['country_id' => $country_id])->get();
    	$result1 = [];
    	$result2 = [];
    	// return $city_list;
    	foreach ($city_list as $key => $value) {
    		$result1[]= [
    			'country_id' => $value->country_id,
    			'country_name' => Country::where('id',$value->country_id)->first()->name,
    			'city_id' => $value->id,
    			'city_name' => $value->name,
    			'store_under_city' => User::where([
					'complete_profile_status'=>1,
					'status' => 1,
					'user_type'=>2,
					'store_country'=>$value->country_id,
					'store_city'=>$value->id ,
					'store_type' => $category_id])->count(),
    			'area_list' => Area::where(['city_id' => $value->id, 
 					'country_id' => $value->country_id])
    				->select('id','name','latitude','longitude')->get()
    		];
    		// dd($value);
    	}

    	// return $result1;

    	foreach ($result1 as $key => $value) {
    		$area_detail_and_stores_count = [];
    		foreach ($value['area_list'] as $key => $value1) {

    			$area_detail_and_stores_count[] = [
	    			'area_id' => $value1->id,
	    			'area_name' => $value1->name,
	    			// 'latitude' => $value1->latitude, // needed
	    			'latitude' => LatLong::where(['area_id' => $value1->id])->first()->latitude,
	    			// 'longitude' => $value1->longitude, // needed
	    			'longitude' => LatLong::where(['area_id' => $value1->id])->first()->longitude,
	    			'store_under_area' => User::where([
	    				'complete_profile_status'=>1, 
	    				'status' => 1,
	    				'user_type'=>2,
	    				'store_country'=>$value['country_id'],
	    				'store_city'=>$value['city_id'],
	    				'store_area' => $value1->id , 
	    				'store_type' => $category_id])->count(),

	    			'store_list' => User::where([
    					'complete_profile_status'=>1, 
    					'status' => 1,'user_type'=>2,
    					'store_country'=>$value['country_id'],
    					'store_city'=>$value['city_id'],
    					'store_area' => $value1->id , 
    					'store_type' => $category_id])->get()
    			];
    		}
    		
    		$result2[] = [
    			'country_id' => $value['country_id'],
    			'country_name' => $value['country_name'],
    			'city_id' => $value['city_id'],
    			'city_name' => $value['city_name'],
    			'store_under_city' => $value['store_under_city'],
    			'area_list' => $area_detail_and_stores_count
    		];
    	}
    	// return $result2;
    	// return Area::get_city_area_stores($country_id,$latitude,$longitude);
    	// return City::get_city_area_stores($country_id,$latitude,$longitude);

 		$response = [
 			'message' => __('messages.success.success'),
 			'near_you' => $near_you,
 			'result' => $result2
 		];
		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
 	}

 	public function store_list_by_latlong($latitude,$longitude,$dis = null){
 		if($dis){
 			$distance = (int)$dis;
 		}else{
 			$distance = 2;
 		}
 		$DATA = DB::select("SELECT *,(3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos( radians(longitude) - radians($longitude)) + sin(radians($latitude)) * 
                    sin(radians(latitude))))  AS distance FROM  area HAVING distance < $distance ");
     	$countryArr = [];
     	$cityArr = [];
     	$areaArr = [];
     	foreach ($DATA as $key => $value) {
     		if(!in_array($value->country_id, $countryArr)){
     			array_push($countryArr, $value->country_id);
     		}
     		
     		if(!in_array($value->city_id, $cityArr)){
     			array_push($cityArr, $value->city_id);
     		}

     		if(!in_array($value->id, $areaArr)){
     			array_push($areaArr, $value->id);
     		}
     	}
     	$store_list = User::whereIn('store_country',$countryArr)
     		->whereIn('store_city',$cityArr)
     		->whereIn('store_area',$areaArr)
     		->where(['user_type' => 2 ,'complete_profile_status' => 1 ,'status' => 1])
     		->get();
     	return $store_list;
 	}

 	public function sort_data(Request $request){
 		$validations = [
			'category_id' => 'required',
			'country_id' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'city_id' => 'required',
			'area_id' => 'required',
			'sort_type' => 'required'
		];
		$Validator = Validator::make($request->all(),$validations);
    	if($Validator->fails()){
    		$response = [
				'message' => $Validator->errors($Validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
    	}

    	$latitude = $request->latitude;
    	$longitude = $request->longitude;
    	$category_id = $request->category_id;
    	$country_id = $request->country_id;
    	$city_id = $request->city_id;
    	$area_id = $request->area_id;
		$sort = [];
    	$area_id_arr = [];
    	$area_id_data = Area::find($area_id);

    	if($area_id_data){
    		$area_id_arr = Area::where(['name' => $area_id_data->name , 'country_id' => $country_id])->pluck('id');
    	}
    	$store_list = User::where([
    		'store_country' => $country_id, 
    		'store_city' => $city_id , 
    		'complete_profile_status' => 1 ,
    		'status' => 1,
    		'store_type' => $category_id])->whereIn('store_area',$area_id_arr)->get();
    	
    	$result = [];

    	if($request->sort_type == 1 ){ // store name sort
	    	foreach ($store_list as $key => $value) {
	    		$distance_count = $this->distance($value->store_area->latitude,$value->store_area->longitude,$latitude,$longitude,"K");
	    		$sort[$key] = $value->store_name;
	    		$result []= $value;
	    		$result [$key]['distance_count']= $distance_count;
	    	}
	    	array_multisort($sort, SORT_ASC, $result);
	    	$response = [
	    		'messages' => __('messages.success.success'),
	    		'response' => $result
	    	];
	    	return response()->json($response,200);
	   }

	   if($request->sort_type == 2 ){ // store cuisine sort
	    	foreach ($store_list as $key => $value) {
	    		$distance_count = $this->distance($value->store_area->latitude,$value->store_area->longitude,$latitude,$longitude,"K");

	    		$sort[$key] = $value->store_cuisine_type->name;
	    		$result []= $value;
	    		$result [$key]['distance_count']= $distance_count;
	    	}
	    	array_multisort($sort, SORT_ASC, $result);
	    	$response = [
	    		'messages' => __('messages.success.success'),
	    		'response' => $result
	    	];
	    	return response()->json($response,200);
	   }

		if($request->sort_type == 3 ){ // store cuisine sort
	    	foreach ($store_list as $key => $value) {
	    		$distance_count = $this->distance($value->store_area->latitude,$value->store_area->longitude,$latitude,$longitude,"K");

	    		$sort[$key] = $distance_count;
	    		$result []= $value;
	    		$result [$key]['distance_count']= $distance_count;
	    	}
	    	array_multisort($sort, SORT_ASC, $result);
	    	$response = [
	    		'messages' => __('messages.success.success'),
	    		'response' => $result
	    	];
	    	return response()->json($response,200);
	   }
 	}

 	public function make_And_Get_store_favourite(Request $request){
 		$current_lat = $request->header('lat');
		$current_lon = $request->header('lon');
		if(empty($current_lat)){
			$response = [
				'messages' => 'lat required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}
		if(empty($current_lon)){
			$response = [
				'messages' => 'lon required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}

 		if($request->method() == 'POST'){
 			$store_id = $request->store_id; 
 			$key = $request->key; 
	 		$validations = [
				'store_id' => 'required',
				'key' => 'required'
			];
			$Validator = Validator::make($request->all(),$validations);
	    	if($Validator->fails()){
	    		$response = [
					'message' => $Validator->errors($Validator)->first()
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
	    	}else{
	    		$userDetail = $request->userDetail;
	    		switch ($key) {
	    			case 1: // make favourite
	    				UserFavourite::firstOrCreate(['store_id' => $store_id , 'user_id' => $userDetail->id]);	
	    				$response = [
			    			'message' => __('messages.favourite_list'),
			    			'response' => UserFavourite::favourite_list($userDetail->id,$current_lat,$current_lon)
			    		];
			    		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
    				break;
	    			
	    			case 2: // un favourite
	    				UserFavourite::where(['store_id' => $store_id , 'user_id' => $userDetail->id])->delete();
	    				$response = [
			    			'message' => __('messages.favourite_list'),
			    			'response' => UserFavourite::favourite_list($userDetail->id,$current_lat,$current_lon)
			    		];
			    		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
    				break;
	    		}
	    	}
 		}

 		if($request->method() == 'GET'){

 			$userDetail = $request->userDetail;
 			$response_data = UserFavourite::favourite_list($userDetail->id,$current_lat,$current_lon);
 			$result = [];
 			foreach ($response_data as $key => $value) {

 				// return $value;
 			}
 			// return count($response_data);
 			$response = [
    			'message' => __('messages.favourite_list'),
    			'response' =>  UserFavourite::favourite_list($userDetail->id,$current_lat,$current_lon)
    		];
    		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
 		}
 	}


 	public function get_top_merchant(Request $request){
 		$current_lat = $request->header('lat');
		$current_lon = $request->header('lon');
		if(empty($current_lat)){
			$response = [
				'messages' => 'lat required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}
		if(empty($current_lon)){
			$response = [
				'messages' => 'lon required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}

		$Categories = Category::pluck('id');
		foreach ($Categories as $key => $value) {
			$Category = 'Category'.$value;
			$$Category = [];
		}  // Dynamic variable creation 

		$store_list = User::where(['status' => 1 , 'user_type' => 2 ,'complete_profile_status' => 1])->get();
		// return $store_list;
		$result1 = [];
		foreach ($store_list as $key => $value) {
			$result1[] = [
				'store_id' => $value->id,
				'store_name' => $value->store_name,
				'store_category_id' => $value->store_type->id,
				'store_category_name' => $value->store_type->name,
				'store_color_code' => $value->store_type->color_code,
				'store_logo' => $value->store_logo,
				'store_cover_pic' => $value->store_cover_pic,
				'store_latitude' => $value->store_area->latitude,
				'store_longitude' => $value->store_area->longitude,
				'store_rating' => ReviewRating::where(['store_id' => $value->id])->avg('rating'),
				'store_distance' => $this->distance($current_lat, $current_lon, $value->store_area->latitude, $value->store_area->longitude,'k')
			];
		}
		// return $result1;

		foreach ($result1 as $key => $value) { // push data if distance match
			$Category = 'Category'.$value['store_category_id'];
			// dd($Category);
			if($value['store_distance'] > 0){
				array_push($$Category, $value);
				$sort[$Category][] = $value['store_rating'];
			}
			
		}


		foreach ($Categories as $key => $value) {

			$Category = 'Category'.$value;
			// return $$Category[$key]['store_distance'];
			$cat_name = $value;
			// $sort[$key] = $$Category[$key]['store_rating'];;
			if(isset($sort[$Category]) && is_array($sort[$Category]))
			array_multisort($sort[$Category], SORT_DESC, $$Category);
			// $result[] = [$Category =>$$Category];
			$result[] = ['data' =>$$Category];
		}

		$response = [
    		'messages' => __('messages.success.success'),
    		'response' => $result
    	];
    	return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
 	}

 	public function get_store_detail(Request $request){
		$store_id = $request->store_id;
		$user_id = $request->user_id;
		$current_lat = $request->header('lat');
		$current_lon = $request->header('lon');
		if(empty($current_lat)){
			$response = [
				'messages' => 'lat required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}
		if(empty($current_lon)){
			$response = [
				'messages' => 'lon required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}
		if(empty($store_id)){
			$response = [
				'messages' => 'store_id required',
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}
		$validations = [
			'store_id'=>'required',
		];
		$Validator = Validator::make($request->all(),$validations);
		/////////////////////////////////////////////////////////////////////////
		//////////////////////////////////////////
		/////////////////////////////////////////////////////////////////////////
		$store_detail = User::where(['id' => $store_id , 'user_type' => 2 , 'status' => 1 , 'complete_profile_status' => 1])->first();
		$store_latitude = $store_detail->store_area->latitude;
		$store_longitude = $store_detail->store_area->longitude;
		if(empty($user_id)){
			$VOUCHERS = Voucher::where([
				'store_id' => $request->store_id ,
				'status' => 1, 
				'status_by_admin' => 1])->get();
			$VOUCHER_RESULT = [];
			foreach ($VOUCHERS as $key => $value) {
				if(Carbon::now()->format('Y-m-d') <= Carbon::parse($value->validity)->format('Y-m-d')){
					$VOUCHER_RESULT[] = [
						'voucher_id' => $value->id,
						'voucher_store_id' => $value->store_id,
						'voucher_code' => $value->code,
						'voucher_description' => $value->description,
						'voucher_point' => $value->point,
						'voucher_validity' => $value->validity,
					];
				}
			}
			$store_detail['voucher_list'] = $VOUCHER_RESULT;
		}else{
			$VOUCHERS = Voucher::where([
				'store_id' => $request->store_id ,
				'status' => 1, 
				'status_by_admin' => 1])->get();
			$VOUCHER_RESULT = [];
			foreach ($VOUCHERS as $key => $value) {
				if(Carbon::now()->format('Y-m-d') <= Carbon::parse($value->validity)->format('Y-m-d')){
					$VOUCHER_RESULT[] = [
						'voucher_id' => $value->id,
						'voucher_store_id' => $value->store_id,
						'voucher_code' => $value->code,
						'voucher_description' => $value->description,
						'voucher_point' => $value->point,
						'voucher_validity' => $value->validity,
						'voucher_used' => UsedVoucher::where(['voucher_id' => $value->id , 'user_id' => $user_id])->count(),
					];
				}
			}
			$store_detail['voucher_list'] = $VOUCHER_RESULT;
		}
		$store_detail['review_rating'] = ReviewRating::where(['store_id' => $request->store_id])->avg('rating');
		$store_detail['distance_count'] = $this->distance($current_lat, $current_lon, $store_latitude, $store_longitude,'K');
		$store_detail['store_images'] = StoreImages::where(['store_id' => $request->store_id])->pluck('image');
		$store_detail['is_favourite'] = UserFavourite::where(['store_id' => $request->store_id ,'user_id' => $user_id])->count();
		$response = [
			'messages' => __('messages.success.success'),
			'response' => $store_detail
		];
		return response()->json($response,__('messages.statusCode.ACTION_COMPLETE'));
	}

	public function scan_qr_code(Request $request){
		// return $request->all();
		Log::info('----------------scan_qr_code-------------'.print_r($request->all(),True));
		Log::info('----------------scan_qr_code-------------'.print_r($request->userDetail->getAttributes(),True));

		// dd();
		$user_id = $request->userDetail->id;
		$qr_value = $request->qr_value;
		$voucher_id = $request->voucher_id;

		$validations = [
			'qr_value' => 'required',
			'voucher_id' => 'required'
		];
		$Validator = Validator::make($request->all(),$validations);
		if($Validator->fails()){
			$response = [
				'message' => $Validator->errors($Validator)->first()
			];
			return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
		}else{
			$qr_array = explode('_', $qr_value);
			$store_id = end($qr_array);
			$trax_id = 'MAI'.uniqid().uniqid();
			$data = UsedVoucher::where(['user_id' => $user_id,'voucher_id' => $voucher_id,'store_id' => $store_id])->first();		
			if(!$data){
				UsedVoucher::insert(['user_id' => $user_id,'voucher_id' => $voucher_id,'store_id' => $store_id ,'transaction_id' => $trax_id ,'created_at' => time() , 'updated_at' => time()]);
				$response = [
					'trans_id' => $trax_id,
					'voucher_detail' => Voucher::where(['id' => $voucher_id])->first()
				];
				return response()->json($response,trans('messages.statusCode.ACTION_COMPLETE'));
			}else{
				$response = [
					'message' => 'Already Used'
				];
				return response()->json($response,trans('messages.statusCode.SHOW_ERROR_MESSAGE'));
			}	
		}
	}

 	function distance($lat1, $lon1, $lat2, $lon2, $unit) {
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
		if ($unit == "K") {
			return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
}
