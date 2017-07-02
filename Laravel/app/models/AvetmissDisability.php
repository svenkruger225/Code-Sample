<?php

class AvetmissDisability extends Eloquent {
	
	protected $table = 'avetmiss_disability_codes';

	protected $guarded = array();

	public static $rules = array(
		'code' => 'required',
		'name' => 'required'
		);

	
}