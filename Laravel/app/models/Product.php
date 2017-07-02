<?php

class Product extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'price' => 'required'
		);
	

	public function getIsMachineHireAttribute()
	{
		if( stripos($this->name,'Machine Hire') !== false )
		{
			return true;
		} 
		
		return false;

	}
	
}
