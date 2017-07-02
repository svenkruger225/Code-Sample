<?php

class Agent extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'email' => 'required',
		'phone' => 'required',
        'code' => 'required|unique:agents,code'
	);


	public function user()
	{
		return $this->belongsTo('User');
	}

	//public function courses()
	//{
	//	return $this->belongsToMany('Course')->withPivot('course_id', 'location_id', 'active');
	//}

	public function courses()
	{
		return $this->hasMany('AgentCourse');
	}
	
	public function orders()
	{
		return $this->hasMany('Order');
	}
	
	public function getFirstNameAttribute()
	{
		$first_name = '';
		if (!empty($this->agent_id))
		{
			$parts = explode(" ", $this->contact_name);
			$last_name = array_pop($parts);
			$first_name = implode(" ", $parts);
		}
		return $first_name;
	}
	
	public function getLastNameAttribute()
	{
		$last_name = '';
		if (!empty($this->agent_id))
		{
			$parts = explode(" ", $this->contact_name);
			$last_name = array_pop($parts);
		}
		return $last_name;
	}
	
	public function findCourse($agent_id, $course_id, $location_id)
	{
		$found = null;
		foreach ($this->courses as $course)
		{
			if ($agent_id == $course->agent_id && $course_id == $course->course_id && $location_id == $course->location_id) 
			{
				$found = $course; 
				break;
			}					
		}
		return $found;
	}
        public function bundles()
	{
		return $this->belongsToMany('Course')->withPivot('course_id', 'price_online', 'price_offline', 'active');
	}
}