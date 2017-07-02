<?php

class CoursePrice extends Eloquent {
	
	protected $table = 'course_prices';
	
	protected $guarded = array();

	public static $rules = array(
		'price_online' => 'required|integer',
		'price_offline' => 'required|integer'
		);

	public function course()
	{
		return $this->belongsTo('Course');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}


}