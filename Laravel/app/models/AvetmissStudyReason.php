<?php

class AvetmissStudyReason extends Eloquent {
	
	protected $table = 'avetmiss_study_reasons';

	protected $guarded = array();

	public static $rules = array(
		'id' => 'required',
		'name' => 'required'
		);

	
}