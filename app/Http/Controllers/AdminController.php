<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use \App\Admin;
use \App\Category;
use \App\Qualification;
use \App\MotherLanguage;
use \App\DoctorQualification;
use \App\DoctorMotherlanguage;
use \App\User;
use Auth;
use Hash;
use Session;
use Url;
use Carbon\Carbon;
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
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $totalPatient = 0;
            $totalDoctor = 0;
            $todayRegistered = 0;
            $userData = User::all();

            foreach ($userData as $key => $value) {
               /*dd(Carbon::now()->format('Y-m-d'));
               dd(Carbon::parse($value->created_at)->format('Y-m-d'));*/
               if($value->user_type == 1 ){
                  $totalDoctor++;
               }
               if($value->user_type == 2 ){
                  $totalPatient++;
               }

               if(Carbon::now()->format('Y-m-d') == Carbon::parse($value->created_at)->format('Y-m-d')){
                  $todayRegistered++;
               }
            }
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/profile',compact('AdminDetail','totalPatient','totalDoctor','todayRegistered'));
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function edit_profile(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         $id = Session::get('Dr_Admin_Id');
         $role = Session::get('Dr_Admin_Role');
         if($request->method() == "GET"){
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            return view('Admin/editProfile',compact('AdminDetail'));
         }
         if($request->method() == "POST"){
            $validaions = [
               'name' => 'required|max:255',
               'mobile' =>'required|numeric',
               'location' => 'required'
            ];
            $Validator = Validator::make($request->all(),$validaions);
            if($Validator->passes()){
               $adminDetail = Admin::find($id);
               if($adminDetail){
                  $profile_image = $request->file('profile_image');
                  $adminDetail->name = $request->name;
                  $adminDetail->mobile = $request->mobile;
                  $adminDetail->location = $request->location;
                  $adminDetail->linkedin = $request->linkedin;
                  $adminDetail->twitter = $request->twitter;
                  // dd($profile_image);
                  if($profile_image){
                     $destinationPath = base_path('Admin/images');
                     $getClientOriginalName = $profile_image->getClientOriginalName();
                     $Name = time()."_".str_replace(' ','_',$getClientOriginalName);
                     $profile_image->move($destinationPath,$Name);
                     $adminDetail->profile_image = $Name;
                  }
                  $adminDetail->save();
                  return redirect('Admin/edit_profile')->with('Admin_profile_updated',__('messages.success.Admin_profile_updated'));
               }else{
                  dd('else');
               }
            }else{
               return redirect('Admin/edit_profile')->withErrors($Validator);
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function change_password(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         $id = Session::get('Dr_Admin_Id');
         $role = Session::get('Dr_Admin_Role');
         $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
         if($request->method() == "GET"){
            return view('Admin/changePassword',compact('AdminDetail'));
         }
         if($request->method() == "POST"){
            if(!Hash::check($request->old_password,$AdminDetail->password)){
               return redirect('Admin/change_password')->withInput()->with('invalid_old_password',__('messages.invalid_old_password'));
            }else{
               $validaions = [
                  'new_password' => 'required|min:8',
                  'confirm_password' => 'required|same:new_password'
               ];
               $Validator = Validator::make($request->all(),$validaions);
               if($Validator->passes()){
                  $AdminDetail->password = Hash::make($request->new_password);
                  $AdminDetail->save();
                  return redirect('Admin/change_password')->with('password_updated',__('messages.success.password_updated'));
               }else{
                  return redirect('Admin/change_password')->withInput()->withErrors($Validator);
               }
               
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function addQualification(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $QualificationList = Qualification::where(['status'=>1])->get();
            return view('Admin/addQualification',compact('AdminDetail','QualificationList'));
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
      }else{
         return redirect('Admin/login');
      }
   } 

   public function qualification_edit(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]); 
            $Qualification = Qualification::find($request->qualification_id); 
            if($Qualification){
               return view('Admin/editQualification',compact('AdminDetail','Qualification'));
            }else{
               return redirect('Admin/add_qualification')->with('invalid_detail',__('messages.invalid.detail'));
            } 
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function save_qualification(Request $request){
      $qualification_name = $request->qualification_name;
      $qa_id = $request->qa_id;
      $validaions = [
         'qualification_name' => 'required|max:255',
         'qa_id' => 'required|numeric'
      ];
      $Validator = Validator::make($request->all(),$validaions);
      if($Validator->passes()){
         $Exist = Qualification::where('id','<>',$qa_id)->where('name',$qualification_name)->first();
         if(!$Exist){
            $Qualification = Qualification::find($qa_id);
            $Qualification->name = $qualification_name;
            $Qualification->save();
            return redirect('Admin/qualification_edit/'.$qa_id)->with('qualificationy_updated',__('messages.success.qualificationy_updated'));
         }else{
            return redirect('Admin/qualification_edit/'.$qa_id)->with('qualificationy_already_exist',__('messages.qualificationy_already_exist'));
         }
      }else{
         return redirect('Admin/qualification_edit/'.$qa_id)->withErrors($Validator);
      }
   }

   public function qualification_delete(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $qualification_detail = Qualification::find($request->qualification_id);
            if($qualification_detail){
               $DoctorQualification = DoctorQualification::Where(['qualification_id' => $request->qualification_id])->first();
               if(!$DoctorQualification){
                  $qualification_detail->delete();
                  return redirect('Admin/add_qualification')->with('QA_deleted',__('messages.success.QA_deleted'));
               }else{
                  return redirect('Admin/add_qualification')->with('QA_exist_under_doctor',__('messages.QA_exist_under_doctor'));
               }
            }else{
               return redirect('Admin/add_qualification')->with('invalid_detail',__('messages.invalid.detail'));
            }
         }
      }else{
         return redirect('Admin/login');
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
            // dd($Pending_doctor_list);
            return view('Admin/pendingList',compact('AdminDetail','Pending_doctor_list'));
         }else{
            return redirect('Admin/login');
         }
      }  
   }

   public function approve_doctor(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $doctor_id = $request->doctor_id;
            $doctor_detail = User::find($doctor_id);
            if($doctor_detail){
               $doctor_detail->status = 1;
               $doctor_detail->save();
               return redirect('Admin/approve_list')->with('docotr_approved',__('messages.success.docotr_approved'));
            }else{
               return redirect('Admin/pending_list')->with('invalid_detail',__('messages.invalid.detail'));
            }
         }else{
            return redirect('Admin/login');
         }
   }

   public function edit_speciality_management(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $speciality_id = $request->speciality_id;
            $speciality_detail = Category::find($speciality_id);
            if($speciality_detail){
               // dd($speciality_detail);
               return view('Admin/editSpecialityMgt',compact('AdminDetail','speciality_detail'));
            }else{
               return redirect('Admin/speciality_management')->with('invalid_detail',__('messages.invalid.detail'));
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function save_speciality(Request $request){
      $speciality_id = $request->sp_id;
      $speciality_name = $request->speciality_name;
      $iconImage = $request->file('iconImage');
      $desc = $request->desc;
      $validaions = [
         'speciality_name' => 'required|max:255',
         'sp_id' => 'required|numeric'
      ];
      $Validator = Validator::make($request->all(),$validaions);
      if($Validator->passes()){
         $Exist = Category::where('id','<>',$speciality_id)->where('name',$speciality_name)->first();
         if(!$Exist){
            $Speciality = Category::find($speciality_id);
            $Speciality->name = $speciality_name;
            $Speciality->description = $desc;
            if($iconImage){
               $name = time()."_".str_replace(' ','_',$iconImage->getClientOriginalName());
               $iconImage->move(base_path('iconImages'),$name);
               $Speciality->icon_path = $name;
            }
            $Speciality->save();
            return redirect('Admin/speciality/edit/'.$speciality_id)->with('speciality_updated',__('messages.success.speciality_updated'));
         }else{
            return redirect('Admin/speciality/edit/'.$speciality_id)->with('speciality_already_exist',__('messages.speciality_already_exist'));
         }
      }else{
         return redirect('Admin/speciality/edit/'.$speciality_id)->withErrors($Validator);
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
                     $destinationPath = base_path('/iconImages');
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

   public function delete_speciality(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $speciality_detail = Category::find($request->speciality_id);
            if($speciality_detail){
               $DoctorSpeciality = User::where(['speciality_id'=>$request->speciality_id])->first();
               if(!$DoctorSpeciality){
                  $speciality_detail->delete();
                  return redirect('Admin/speciality_management')->with('SP_deleted',__('messages.success.SP_deleted'));   
               }else{
                  return redirect('Admin/speciality_management')->with('SP_exist_under_doctor',__('messages.SP_exist_under_doctor'));
               }
            }else{
               return redirect('Admin/speciality_management')->with('invalid_detail',__('messages.invalid.detail'));
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

   public function mother_language_delete(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $mother_language_detail = MotherLanguage::find($request->mother_language_id);
            if($mother_language_detail){
               $DoctorMotherLanguage = DoctorMotherlanguage::Where(['mother_language_id' => $request->mother_language_id])->first();
               if(!$DoctorMotherLanguage){
                  $mother_language_detail->delete();
                  return redirect('Admin/add_mother_language')->with('ML_deleted',__('messages.success.ML_deleted'));
               }else{
                  return redirect('Admin/add_mother_language')->with('ML_exist_under_doctor',__('messages.ML_exist_under_doctor'));
               }
            }else{
               return redirect('Admin/add_mother_language')
                  ->with('invalid_detail',__('messages.invalid.detail'));
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function edit_mother_language(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $mother_language_id = $request->mother_language_id;
            $mother_language_detail = MotherLanguage::find($request->mother_language_id);
            if($mother_language_detail){
               return view('Admin/editLanguage',compact('AdminDetail','mother_language_detail'));
            }else{
               return redirect('Admin/mother_language/edit/'.$mother_language_id)->with('invalid_detail',__('messages.invalid.detail'));
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function save_mother_language(Request $request){
      $lg_id = $request->lg_id;
      $Language_name = $request->Language_name;
      $validaions = [
         'Language_name' => 'required|max:255',
         'lg_id' => 'required|numeric'
      ];
      $Validator = Validator::make($request->all(),$validaions);
      if($Validator->passes()){
         $Exist = MotherLanguage::where('id','<>',$lg_id)->where('name',$Language_name)->first();
         if(!$Exist){
            $MotherLanguage = MotherLanguage::find($lg_id);
            $MotherLanguage->name = $Language_name;
            $MotherLanguage->save();
            return redirect('Admin/mother_language/edit/'.$lg_id)->with('mother_language_updated',__('messages.success.mother_language_updated'));
         }else{
            return redirect('Admin/mother_language/edit/'.$lg_id)->with('mother_language_already_exist',__('messages.mother_language_already_exist'));
         }
      }else{
         return redirect('Admin/mother_language/edit/'.$lg_id)->withErrors($Validator);
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

   public function doctor_profile(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $User = New User;
            $Doctor_detail = $this->getUserDetail($User->getUserDetail($request->doctor_id));
            if($Doctor_detail){
               // dd($Doctor_detail);
               return view('Admin/docProfile',compact('AdminDetail','Doctor_detail'));
            }else{
               
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }

   public function pending_doctor_profile(Request $request){
      $loggedIn = Session::get('Dr_Admin_loggedIn');
      if($loggedIn){
         if($request->method() == "GET"){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            $User = New User;
            $Doctor_detail = $this->getUserDetail($User->getPendingDoctorDetail($request->doctor_id));
            if($Doctor_detail){
               // dd($Doctor_detail);
               return view('Admin/docProfile',compact('AdminDetail','Doctor_detail'));
            }else{
               
            }
         }
      }else{
         return redirect('Admin/login');
      }
   }
}
