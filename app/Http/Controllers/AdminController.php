<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use \App\Admin;
use \App\Category;
use \App\Qualification;
use \App\MotherLanguage;
use \App\User;
use Auth;
use Hash;
use Session;
use Url;
use Illuminate\Support\Facades\Input;
class AdminController extends Controller
{
    
   public function index(Request $request){
    	if($request->method() == "GET"){
    		$loggedIn = Session::get('Dr_Admin_loggedIn');
    		if($loggedIn){
    			return redirect('Admin/dashboard');
    		}else{
    			return view('Admin/login');
    		}
    	}

    	if($request->method() == "POST"){
    		$email = $request->email;
    		$password = $request->password;
         $remember = $request->remember;
    		$AdminDetail = $this->getAdminDetail(['email'=>$email,'role'=>1]);
    		if(!empty($AdminDetail) && Hash::check($password,$AdminDetail->password))
    		{
    			$sessionData = [
    				'Dr_Admin_Id' => $AdminDetail->id,
    				'Dr_Admin_Role' => $AdminDetail->role,
    				'Dr_Admin_loggedIn' => 'true'
    			];
    			session()->put($sessionData);
    			if($remember=='on') {
					 setcookie('Dr_Admin_Email',$email, time() + (86400 * 30), "/");
					 setcookie('Dr_Admin_Password',$password, time() + (86400 * 30), "/");
					 setcookie('Dr_Admin_Remember','on',time() + (86400 * 30), "/");
				}
				if($remember!='on') {
					  setcookie('Dr_Admin_Email', null, -1, '/');
					  setcookie('Dr_Admin_Password', null, -1, '/');
					  setcookie('Dr_Admin_Remember', null, -1, '/');
				}
    			return redirect('Admin/dashboard');
    		}else{
    			return redirect('Admin/login')
            ->withInput()->with('invalid_credentials',__('messages.invalid.credentials'));
    		}
    	}
   }

   public function dashboard(){
   	$loggedIn = Session::get('Dr_Admin_loggedIn');
   	$AdminId = Session::get('Dr_Admin_Id');
   	$Role = Session::get('Dr_Admin_Role');
   	$query = [
   		'id' => $AdminId
   	];
   	if($loggedIn){
   		$AdminDetail = $this->getAdminDetail($query);
   		// dd($AdminDetail);
   		return view('Admin/index',compact('AdminDetail'));
   	}else{
   		return redirect('Admin/login');
   	}
   }

   public function logout(){
   	Session::forget(['Dr_Admin_Id','Dr_Admin_Role','Dr_Admin_loggedIn']);
   	return redirect('Admin/login');
   } 

   public function profile(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/docProfile',compact('AdminDetail'));
         }else{
            return redirect('Admin/login');
         }
      }
   }


   public function addQualification(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $QualificationList = Qualification::where(['status'=>1])->get();
            return view('Admin/addQualification',compact('AdminDetail','QualificationList'));
         }else{
            return redirect('Admin/login');
         }
      } 

      if($request->method() == "POST"){
         $Qualification = $request->qualification_name;
         $validaions = [
            'qualification_name' => 'required|max:255',
         ];
         $Validator = Validator::make($request->all(),$validaions);
         if($Validator->passes()){
            $Exist = Qualification::where(['name' => $Qualification])->first();
            if(!$Exist){
               $QA = new Qualification;
               $QA->name = $Qualification;
               $QA->save();
               return redirect('Admin/add_qualification')
                  ->with('QA_added',__('messages.success.QA_added'));
            }else{
                return redirect('Admin/add_qualification')
                  ->withInput()
                  ->with('QA_already_exist',__('messages.success.QA_already_exist'));
            }
         }else{
            return redirect('Admin/add_qualification')->withInput()->withErrors($Validator);
         }
      } 
   }

   public function approved_list(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $Approved_doctor_list = User::where(['status'=>1,'user_type'=>1])->get();
            return view('Admin/approveList',compact('AdminDetail','Approved_doctor_list'));
         }

      }else{
         return redirect('Admin/login');
      }
   }

   public function pending_list(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $Pending_doctor_list = User::where(['status'=>0,'user_type'=>1])->get();
            return view('Admin/pendingList',compact('AdminDetail','Pending_doctor_list'));
         }else{
            return redirect('Admin/login');
         }
      }  
   }

   public function speciality_management(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $CategoryList = Category::where(['status'=>1])->get();
            // dd($CategoryList);
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/specialityMgt',compact('AdminDetail','CategoryList'));
         }
         if($request->method() == "POST"){
            $name = $request->name;
            $iconImage = $request->file('iconImage');
            $desc = $request->desc;

            $validaions = [
               'name' => 'required|max:255',
               'iconImage' => 'image|mimes:jpg,png,jpeg|required',
            ];
            $Validator = Validator::make($request->all(),$validaions);
            if($Validator->passes()){

               $Exist = Category::where(['name' => $name])->first();
               if(!$Exist){
                  $SP = new Category;
                  if(isset($iconImage)){
                     $imageName = time()."_".$iconImage->getClientOriginalName();
                     $destinationPath = base_path('public/iconImages');
                     $iconImage->move($destinationPath,$imageName);
                     $SP->icon_path = $imageName;
                  }
                  $SP->name = $name;
                  $SP->description = $desc;
                  $SP->status = 1;
                  $SP->save();
                  return redirect('Admin/speciality_management')->with('speciality_added',__('messages.success.speciality_added'));
               }else{
                   return redirect('Admin/speciality_management')
                     ->withInput()
                     ->with('SP_already_exist',__('messages.success.speciality_already_exist'));
               }
            }else{
               return redirect('Admin/speciality_management')->withInput()->withErrors($Validator);
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }  

   public function add_mother_language(Request $request){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            if($request->method() == "GET"){
               $id = Session::get('Dr_Admin_Id');
               $role = Session::get('Dr_Admin_Role');
               $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
               $MotherLanguage = MotherLanguage::all();
               return view('Admin/addLanguage',compact('AdminDetail','MotherLanguage'));
            }
            if($request->method() == "POST"){
               $language_name = $request->language_name;
               $validaions = [
                  'language_name' => 'required|max:255',
               ];
               $Validator = Validator::make($request->all(),$validaions);
               if($Validator->passes()){
                  $Exist = MotherLanguage::where(['name' => $language_name])->first();
                  if(!$Exist){
                     $ML = new MotherLanguage;
                     $ML->name = $language_name;
                     $ML->save();
                     return redirect('Admin/add_mother_language')
                        ->with('mother_language_added',__('messages.success.mother_language_added'));
                  }else{
                      return redirect('Admin/add_mother_language')
                        ->withInput()
                        ->with('mother_language_already_exist',__('messages.success.mother_language_already_exist'));
                  }
               }else{
                  return redirect('Admin/add_mother_language')->withInput()->withErrors($Validator);
               }
            }
         }else{
            return redirect('Admin/login');
         }
   }

   public function patient_list(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $patientList = User::where(['user_type'=>2,'profile_status'=>1])->get();
            return view('Admin/patientList',compact('AdminDetail','patientList'));
         }else{
            return redirect('Admin/login');
         }
      }  
   }

   public function block_patient(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         $patient_id = $request->patient_id;
         $status = $request->status;
         $Patient_detail = User::where(['user_type'=>2,'id'=>$patient_id])->first();
         if($Patient_detail){
            $Patient_detail->status = $status;
            $Patient_detail->save();
            if($status == 1){
               return redirect('Admin/patient_list')->with('patient_unblocked',__('messages.success.patient_unblocked'));
            }
            if($status == 0){
               return redirect('Admin/patient_list')->with('patient_blocked',__('messages.success.patient_blocked'));
            }
         }else{
            return redirect('Admin/patient_list');
         }
      }else{
         return redirect('Admin/login');
      }
      
   }

   public function getAdminDetail($query){
   	$AdminDetail = Admin::Where($query)->first();
   	return $AdminDetail;
   }
}
