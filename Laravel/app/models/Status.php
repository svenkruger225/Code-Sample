<?php

class Status extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'status_type' => 'required'
		);

	public function invoices()
	{
		return $this->hasMany('Invoice');
	}

	public function orders()
	{
		return $this->hasMany('Order');
	}


}