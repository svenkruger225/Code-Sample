<?php

class CourseRepeat extends Eloquent {
	
	protected $table = 'courserepeats';

	protected $guarded = array();

	public static $rules = array(
		'course_id' => 'required',
		'location_id' => 'required',
		//'monday' => 'required_without:tuesday,wednesday,thursday,friday,saturday,sunday',
		//'tuesday' => 'required_without:monday,wednesday,thursday,friday,saturday,sunday',
		//'wednesday' => 'required_without:monday,tuesday,thursday,friday,saturday,sunday',
		//'thursday' => 'required_without:monday,tuesday,wednesday,friday,saturday,sunday',
		//'friday' => 'required_without:monday,tuesday,wednesday,thursday,saturday,sunday',
		//'saturday' => 'required_without:monday,tuesday,wednesday,thursday,friday,sunday',
		//'sunday' => 'required_without:monday,tuesday,wednesday,thursday,friday,saturday',
		'time_start' => 'required',
		'time_end' => 'required',
		'start_date' => 'required'
	);

	public function course()
	{
		return $this->belongsTo('Course');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function getLastInstanceDateAttribute()
	{
		$instance = \CourseInstance::where('course_id', $this->course_id)
		->where('location_id', $this->location_id)
		->orderBy('course_date', 'desc')
		->first();
		return $instance ? $instance->course_date : '';
	}

	public function getStartTimeAttribute()
	{
		return date('h:i A', strtotime($this->time_start));
	}
	
	public function getEndTimeAttribute()
	{
		return date('h:i A', strtotime($this->time_end));
	}

}