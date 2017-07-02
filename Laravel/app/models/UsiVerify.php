<?php

class UsiVerify extends Eloquent {
	
	protected $table = 'usi_verify';
	
	protected $guarded = array();

	public static $rules = array(
		);


	public function customer()
	{
		return $this->belongsTo('Customer', 'customer_id');
	}

}

