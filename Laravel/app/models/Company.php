<?php

class Company extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'email' => 'required',
		'phone' => 'required'
	);
	
	public function orders()
	{
		return $this->hasMany('Order');
	}

}