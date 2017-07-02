<?php

class CourseBundle extends Eloquent {
	
	protected $table = 'coursebundles';
	
	protected $guarded = array();

	public static $rules = array(
		'location_id' => 'required',
		'date_from' => 'date',
		'total_online' => 'required',
		'total_offline' => 'required'
	);

	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function bundles()
	{
		return $this->belongsToMany('Course')->withPivot('course_id', 'price_online', 'price_offline', 'active');
	}
}