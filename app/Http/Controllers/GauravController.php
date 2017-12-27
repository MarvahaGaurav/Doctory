<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Gaurav;
use Response;


class GauravController extends Controller
{
   public function testCase1(){
   	$result = array();
   	// $data = Gaurav::all();
   	// $data = Gaurav::find([1,2]);
   	// $data = Gaurav::firstOrCreate(['name' => 'gaurav']);

   	/*$data = Gaurav::chunk(2,function($data){
   		foreach ($data as $key => $value) {
   			$result[] = $value->id;
   		}
   	});*/



   	////////////////////////////////////////////////////////////////
   	///// firstOrNew  
   	///////////////////////////////////////////////////////////////

	   	/*$data = Gaurav::firstOrNew(['name' => 'ankit1','email' => 'gauravmrvh1@gmail.com1']);
	   	$data->save();*/

	   	// it will find data in db if find , return data else u have to run save() to save data in db
		////////////////////////////////////////////////////////////////
   	///// firstOrNew End
   	///////////////////////////////////////////////////////////////


	   ////////////////////////////////////////////////////////////////
   	///// firstOrCreate  
   	///////////////////////////////////////////////////////////////

	   	/*$data = Gaurav::firstOrCreate(['name' => 'gaurav','email' => 'gauravmrvh1@gmail.com']);*/
	   	
	   	// if data in table having name = gaurav email = gauravmrvh1@gmail.com it will return data otherwise it will create data into DB but in this i have to do ( protected $fillable = ['name','email'] ) in model

		////////////////////////////////////////////////////////////////
   	///// firstOrCreate End
   	///////////////////////////////////////////////////////////////

         
   	
   }

   public function checkBeforeFunction(Request $request){
      dd($request->all());
   }
}
