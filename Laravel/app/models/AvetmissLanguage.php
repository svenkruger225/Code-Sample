<?php

class AvetmissLanguage extends Eloquent {
	
	protected $table = 'avetmiss_language_codes';

	protected $guarded = array();

	public static $rules = array(
		'id' => 'required',
		'name' => 'required'
		);

	
}