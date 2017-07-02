<?php

class Certificate extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'location_id' => 'required',
		'course_id' => 'required',
		'customer_id' => 'required',
		'certificate_date' => 'required',
		'description' => 'required',
		'user_id' => 'required',
		//'status_id' => 'required'
	);
	
	public function location()
	{
		return $this->belongsTo('Location');
	}
	
	public function customer()
	{
		return $this->belongsTo('Customer');
	}
	
	public function roster()
	{
		return $this->belongsTo('Roster');
	}
		
	public function getCourseDateAttribute()
	{
		if ($this->roster)
		{
			if ($this->roster->instance)
				return $this->roster->instance->course_date;
			elseif ($this->roster->groupbooking)
				return $this->roster->groupbooking->course_date;
		}
		else
		{
			\Log::error("No Roster for certificate id: ". $this->id . ", customer: " . $this->customer_id . ", name: " . $this->customer->name);
		}
		
		return date('Y-m-d');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}
	
	public function status()
	{
		return $this->belongsTo('Status');
	}


}