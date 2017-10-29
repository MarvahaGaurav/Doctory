<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $hidden = [
        'created_at','updated_at'
    ];

    public function subcategories(){
    	return $this->hasMany('\App\Subcategory')->where(['status' => 1]);
    	// return $this->hasMany(\App\Subcategory::class);
    }

    public function speciality(){
        return $this->hasOne('\App\Subcategory','speciality_id','id')->where(['status' => 1]);
    }

    public static function getSubCatByCatId($categoryId = null ,$slug = null){
        if($slug){
            $data = Self::Where(['slug' => $slug,'status' => 1])->with('subcategories')->get();
            return $data;
        }
        if($categoryId){
            $data = Self::Where(['id' => $categoryId,'status' => 1])->with('subcategories')->get();
        	return $data;
        }
    }

    public static function getSubCatAndCat(){
    	$data = Self::Where(['status' => 1])->with('subcategories')->get();
    	return $data;
    }
}
