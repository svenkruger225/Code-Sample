<?php

class Supplier extends User {
	
	//protected $guarded = array();

	public static $rules = array(
		'username' => 'required|min:3|unique:users',
		'email' => 'required',
		'business_name' => 'required'
		//'active' => 'required'
	);
	
}