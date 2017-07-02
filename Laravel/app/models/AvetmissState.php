<?php

class AvetmissState extends Eloquent {
	
	protected $table = 'avetmiss_state_codes';

	protected $guarded = array();

	public static $rules = array(
		'code' => 'required',
		'name' => 'required'
		);

	
}