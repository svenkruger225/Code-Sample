<?php

class UsiBulkCreate extends Eloquent {
	
	protected $table = 'usi_bulk_create';
	
	protected $guarded = array();

	public static $rules = array(
		);


	public function applications()
	{
		return $this->hasMany('UsiCreate', 'usi_bulk_create_id');
	}



}