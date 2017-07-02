<?php

class ItemType extends Eloquent {
	protected $table = 'itemtypes';

	protected $guarded = array();

	public static $rules = array(
		'name' => 'required'
		);
	
	public function items()
	{
		return $this->hasMany('Item');
	}


}