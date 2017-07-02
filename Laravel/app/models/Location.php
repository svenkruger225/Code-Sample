<?php

class Location extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		//'parent_id' => 'required',
		'name' => 'required',
		'short_name' => 'required',
		'address' => 'required',
		'city' => 'required',
		'state' => 'required',
		'post_code' => 'required',
		'email' => 'required',
		'phone' => 'required',
		'mobile' => 'required'
	);

	public function children() 
	{
		return $this->hasMany('Location', 'parent_id'); 
	}
	
	public function parent()
	{
		if($this->parent_id !== null && $this->parent_id > 0)
		{
			return $this->belongsTo('Location','parent_id');
		} 
		else 
		{
			return null;
		}
	}
	
	public function isParent()
	{
		if($this->parent_id !== null && $this->parent_id > 0)
			return false;
		else 
			return true;
	}
	
	public function getParentNameAttribute() 
	{
		if($this->parent_id !== null && $this->parent_id > 0)
		{
			return $this->parent->name;
		} 
		else 
		{
			return $this->name;
		}
	}
	
	public function getCompleteAddressAttribute() 
	{
		return $this->address . ' ' .$this->city . ' ' . $this->state . ', ' . $this->post_code;
	}

	public function instances()
	{
		return $this->hasMany('CourseInstance');
	}

	public function bundles()
	{
		return $this->hasMany('CourseBundle');
	}

	public function repeats()
	{
		return $this->hasMany('CourseRepeat');
	}

	public function orders()
	{
		return $this->hasMany('Order');
	}

	public function emails()
	{
		return $this->hasMany('Email');
	}


}