<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use \App\Admin;
use \App\Category;
use \App\Qualification;
use Auth;
use Hash;
use Session;
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
    			return view('Admin/login');
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
            return view('Admin/login');
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
            // dd($AdminDetail);
            return view('Admin/addQualification',compact('AdminDetail'));
         }else{
            return view('Admin/login');
         }
      } 

      if($request->method() == "POST"){

      } 
   }

   public function approve_list(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/approveList',compact('AdminDetail'));
         }else{
            return view('Admin/login');
         }
      }  
   }

   public function pending_list(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/pendingList',compact('AdminDetail'));
         }else{
            return view('Admin/login');
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
                  return redirect('Admin/speciality_management')->with('speciality_added','Speciality added successfully.');

               }else{
                  return redirect('Admin/speciality_management')->withInput()->withErrors($Validator);
               }
            }
         }else{
            return view('Admin/login');
         }
   }  

   public function add_language(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/addLanguage',compact('AdminDetail'));
         }else{
            return view('Admin/login');
         }
      }  
   }

   public function patient_list(Request $request){
      if($request->method() == "GET"){
         $loggedIn = Session::get('Dr_Admin_loggedIn');
         if($loggedIn){
            $id = Session::get('Dr_Admin_Id');
            $role = Session::get('Dr_Admin_Role');
            $AdminDetail = $this->getAdminDetail(['id'=>$id,'role'=>$role]);
            // dd($AdminDetail);
            return view('Admin/patientList',compact('AdminDetail'));
         }else{
            return view('Admin/login');
         }
      }  
   }

   public function getAdminDetail($query){
   	$AdminDetail = Admin::Where($query)->first();
   	return $AdminDetail;
   }
}
