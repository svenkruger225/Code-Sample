<?php

class Voucher extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'customer_id' => 'required',
		'course_id' => 'required',
		'location_id' => 'required',
		'expiry_date' => 'required',
		'status_id' => 'required',
		'active' => 'required'
		);
	
	public function order()
	{
		return $this->belongsTo('Order');
	}
	
	public function customer()
	{
		return $this->belongsTo('Customer', 'customer_id');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function status()
	{
		return $this->belongsTo('Status');
	}
	
	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function getDescriptionAttribute()
	{
		return $this->id . ' | ' . $this->expiry_date;
	}
	
	public function isValid()
	{
		if ($this->status->name == 'Valid' && $this->expiry_date >= date('Y-m-d'))
			return true;
		else
			return false;
	}
	
	public function isCancelled()
	{
		if ($this->status->name == 'Cancelled')
			return true;
		else
			return false;
	}

	public function getMessageAttribute()
	{
		if ($this->status->name == 'Valid' && $this->expiry_date >= date('Y-m-d'))
			return 'Thank you for redeeming your voucher. You have a credit for any ' . $this->course->name . ' in ' . $this->location->name . ', Please select a course date';
		elseif ($this->status->name == 'Valid' && $this->expiry_date < date('Y-m-d'))
			return 'Your voucher has expired at ' . $this->expity_date;
		elseif ($this->status->name != 'Valid')
			return 'Your voucher has already been used';
		else
			return 'Your voucher is invalid';
	}

	public function getValueAttribute()
	{
		$total = 0;
		
		if ($this->course->prices->count())
			foreach ($this->course->prices as $price)
			{
				if($price->location_id == $this->location_id)
					$total = $price->price_online;
			}
			
		return $total;
	}
}