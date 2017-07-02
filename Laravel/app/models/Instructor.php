<?php

class Instructor extends User {
	
	//protected $guarded = array();

	public static $rules = array(
		//'user_id' => 'required',
		//'active' => 'required'
	);
	
	public function getFullNameAttribute()
	{
		return "{$this->first_name} {$this->last_name}";
	}

	public function courses()
	{
		return $this->belongsToMany('Course', 'course_instructor', 'user_id');
	}

	public function instances()
	{
		return $this->belongsToMany('CourseInstance', 'course_instance_instructor', 'user_id');
	}
	
	public function group_bookings()
	{
		return $this->belongsToMany('GroupBooking', 'group_booking_instructor', 'user_id');
	}
	
	public function scopeTrainerForCourse($query, $course_id)
	{
		if(isset($course_id) && !empty($course_id)) 
		{
			return $query
			->join('course_instructor', 'users.id', '=', 'course_instructor.user_id')
			->where('course_instructor.course_id', $course_id);
		}
		else
			return $query;
	}
	
	public function scopeTrainerFromState($query, $state)
	{
		if(!empty($state)) 
		{
			return $query->where('business_state', $state);
		}
		else
			return $query;
	}
	
}