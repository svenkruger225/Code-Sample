<?php

class AvetmissLabourForce extends Eloquent {
	
	protected $table = 'avetmiss_labour_force_status';

	protected $guarded = array();

	public static $rules = array(
		'id' => 'required',
		'name' => 'required'
		);

	
}