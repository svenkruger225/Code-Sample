<?php

class Referrer extends Eloquent {
	
	protected $table = 'referrers';

	protected $guarded = array();

	public static $rules = array(
		);
	
	public function logs()
	{
		return $this->hasMany('ReferrerLog');
	}
	
}