<?php

class Course extends Eloquent {
	protected $table = 'courses';
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'short_name' => 'required',
		'description' => 'required'
	);

	public function instances()
	{
		return $this->hasMany('CourseInstance');
	}

	public function prices()
	{
		return $this->hasMany('CoursePrice', 'course_id');
	}
	
	public function priceForLocation($loc_id)
	{
		return $this->prices->filter(function($price) use($loc_id)
			{
				if ($price->location_id == $loc_id)
				{
					return $price;
				}
			})->first();
	}

	public function instructors()
	{
		return $this->belongsToMany('User', 'course_instructor');
	}

	public function bundles()
	{
		return $this->hasMany('CourseBundle');
	}

	public function repeats()
	{
		return $this->belongsToMany('CourseRepeat');
	}

	public function messages()
	{
		return $this->hasMany('Message');
	}

	public function modules()
	{
		return $this->hasMany('OnlineModule', 'course_id');
	}
	
	public function emails()
	{
		return $this->messages->filter(function($message) 
			{
				if ($message->type->name == 'Email')
				{
					return $message;
				}
			});
	}

	public function sms()
	{
		return $this->messages->filter(function($message) 
			{
				if ($message->type->name == 'Sms')
				{
					return $message;
				}
			});
	}
	
	public function getIsAccreditedAttribute()
	{
		return !empty($this->certificate_code);
	}

    public function getCourseDates($locationId)
    {
        $list_of_locations = DB::table('locations')
            ->where('id', '=',  $locationId)
            ->orWhere('parent_id', '=',  $locationId)
            ->remember(Config::get('cache.minutes', 1))->lists('id');
        $dateStart = date("Y-m-d");
        $dateEnd = date("Y-m-d", strtotime("+3 month", time()));


        $course = Course::with(Array('prices', 'instances' => function($query) use($dateStart, $dateEnd, $list_of_locations){
            return $query->wherein('location_id', $list_of_locations)
                ->whereBetween('course_date', array($dateStart, $dateEnd))
                ->where('active', 1)
                ->orderBy('course_date')
                ->orderBy('location_id');
        }))
            ->where('id', '!=', 9)
            ->where('id','=',$this->id)
            ->where('type', 'FaceToFace')
            ->where('active', 1)
            ->orderBy('order')->get();

        //$queries = DB::getQueryLog();
        //$last_query = end($queries);
        //@TODO comment before deployment
        return $course;
    }
}