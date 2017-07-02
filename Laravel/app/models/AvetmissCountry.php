<?php

class AvetmissCountry extends Eloquent {
	
	protected $table = 'avetmiss_country_codes';

	protected $guarded = array();

	public static $rules = array(
		'code' => 'required',
		'name' => 'required'
		);

	
}