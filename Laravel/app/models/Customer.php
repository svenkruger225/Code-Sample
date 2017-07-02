<?php

class Customer extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'first_name' => 'required',
		'last_name' => 'required',
		'dob' => 'required',
		'email' => 'required',
		'mobile' => 'required',
		'mail_out_email' => 'required',
		'mail_out_sms' => 'required'
	);

	public function setFirstNameAttribute($value)
	{
		$this->attributes["first_name"] = trim($value);
	}
	public function setLastNameAttribute($value)
	{
		$this->attributes["last_name"] = trim($value);
	}
	public function setEmailAttribute($value)
	{
		$this->attributes["email"] = trim($value);
	}	
	public function setPhoneAttribute($value)
	{
		$this->attributes["phone"] = str_replace(" ", "", $value);
	}
	public function setMobileAttribute($value)
	{
		$this->attributes["mobile"] = str_replace(" ", "", $value);
	}

	public function getNameAttribute()
	{
		return "{$this->first_name} {$this->last_name}";
	}	
	public function getFullNameAttribute()
	{
		return "{$this->first_name} {$this->last_name}";
	}

	public function classes()
	{
		return $this->belongsToMany('CourseInstance');
	}

	public function rosters()
	{
		return $this->hasMany('Roster', 'customer_id');
	}

	public function onlinerosters()
	{
		return $this->hasMany('OnlineRoster', 'customer_id');
	}

	public function setRosterIdAttribute($value)
	{
		$this->attributes['roster_id'] =  $value;
	}

	public function IsRosterOfAccreditedCourse($roster_id)
	{
		//\Log::info($this->rosters->toJson());
		$roster =  $this->rosters->filter(function($r) use($roster_id){
				return $r->id == $roster_id;
			})->first();
		
		return $roster && $roster->is_course_accredited;
	}
	
	public function IsEnrolled($course_id)
	{
		//\Log::info($this->onlinerosters->toJson());
		$course =  $this->onlinerosters->filter(function($roster) use($course_id){
				return $roster->course_id == $course_id;
			});
		
		//\Log::info($exist->toJson());
		
		return $course && $course->count();
	}
	
	public function stateObj()
	{
		return $this->hasOne('AvetmissState', 'id', 'state');
	}

	public function countryBirth()
	{
		return $this->hasOne('AvetmissCountry', 'id', 'country_of_birth');
	}

	public function countryResidence()
	{
		return $this->hasOne('AvetmissCountry', 'id', 'country_of_residence');
	}
	
	public function orders()
	{
		return $this->hasMany('Order');
	}
	
	public function last_order()
	{
		return $this->hasMany('Order')->orderBy('order_date', 'desc')->first();
	}

	public function certificates()
	{
		return $this->hasMany('Certificate');
	}

	public function documents()
	{
		return $this->hasMany('ExternalDocuments');
	}

	public function vouchers()
	{
		return $this->hasMany('Voucher');
	}

	public function agent()
	{
		return $this-belongsTo('Agent');
	}
	
	public function user()
	{
		if($this->use_id === null || $this->user_id <= 0)
		{
			$this->use_id = 3;
		}
		return $this->belongsTo('User','user_id');
	}

	public function setCurrentOnlineRosterAttribute($course_id)
	{
		$roster = $this->onlinerosters->filter(function($roster) use($course_id){
				return $roster->course_id == $course_id;
			})->first();
		
		if ($roster && $roster->history)
		{
			foreach ($roster->history->steps as $step)
			{
				$step->roster_id = $roster->id;
			}
		}
		if($roster) {
			$this->roster_id = $roster->id;
			if(!$roster->course->roster_id) {
				$roster->course->roster_id = $roster->id;
			}
		}
		$this->attributes['current_online_roster'] = $roster;
	}

	public function CurrentOnlineRoster($course_id)
	{
		$roster = $this->onlinerosters->filter(function($roster) use($course_id){
				return $roster->course_id == $course_id;
			});
		
		return $roster ? $roster->first() : null;
	}

}