<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gaurav extends Model
{
	protected $table ="test";
	// protected $dateFormat = 'Y-d-m'; 
   // const created_at = 'creation_date';
   // const updated_at = 'last_update';

	protected $fillable = ['name','email'];
}
