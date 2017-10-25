<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function subcategories(){
    	return $this->hasMany('\App\Subcategory')->where(['status' => 1]);
    	// return $this->hasMany(\App\Subcategory::class);
    }

    public static function getSubCatByCatId($categoryId){
    	$data = Self::Where(['id' => $categoryId,'status' => 1])->with('subcategories')->get();
    	return $data;
    }

    public static function getSubCatAndCat(){
    	$data = Self::Where(['status' => 1])->with('subcategories')->get();
    	return $data;
    }
}
