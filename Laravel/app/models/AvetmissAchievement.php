<?php

class AvetmissAchievement extends Eloquent {
	
	protected $table = 'avetmiss_achievement_codes';

	protected $guarded = array();

	public static $rules = array(
		'code' => 'required',
		'name' => 'required'
		);

	
}