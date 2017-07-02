<?php

class GroupBooking extends Eloquent {
	
	protected $table = 'groupbookings';
	
	protected $guarded = array();

	public static $rules = array(
		'course_id' => 'required|integer',
		'location_id' => 'required|integer',
		'customer_id' => 'required|integer',
		'course_date' => 'required|date',
		'time_start' => 'required',
		'time_end' => 'required',
		'students' => 'required|integer',
		'group_name' => 'required'
		);

	public function course()
	{
		return $this->belongsTo('Course');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function customer()
	{
		return $this->belongsTo('Customer');
	}

	public function instructors()
	{
		return $this->belongsToMany('Instructor', 'group_booking_instructor', 'group_booking_id', 'user_id');
	}

	public function order()
	{
		return $this->belongsTo('Order');
	}

	public function rosters()
	{
		return $this->hasMany('Roster');
	}
	
	public function getStartTimeAttribute()
	{
		return date('h:i A', strtotime($this->time_start));
	}
	
	public function getEndTimeAttribute()
	{
		return date('h:i A', strtotime($this->time_end));
	}
	
	public function getCourseDateTimeAttribute()
	{
		$date_time_str =  $this->course_date . ' ' . $this->time_start;
		$date_time = new \DateTime($date_time_str);
		
		return $date_time->format('Y-m-d H:i');
	}
	
	public function getMyobJobCodeAttribute()
	{
		$code = '';
		if ($this->location->isParent())
			$code = $this->location->myob_job_code . '-';
		else
			$code = $this->location->parent->myob_job_code . '-';
		
		$code .= $this->course->myob_job_code;
		
		return $code;		
		
	}
	
	public function scopeFromLocation($query, $location_id)
	{
		if(isset($location_id) && !empty($location_id)) 
		{
			$locations = \DB::table('locations')
				->where('id', '=',  $location_id)
				->orWhere('parent_id', '=',  $location_id)
				->lists('id');
			return $query->wherein('location_id', $locations);
		}
		else
			return $query;
	}
	
	public function scopeForCourse($query, $course_id)
	{
		if(isset($course_id) && !empty($course_id))
			return $query->where('course_id', '=', $course_id);
		else
			return $query;
	}
	
	public function scopeFromDate($query, $fromDate, $toDate)
	{
		if(!isset($toDate) || empty($toDate))
		{
			$toDate = date("Y-m-d");
		}

		if(isset($fromDate) && !empty($fromDate))
			return $query->whereBetween('course_date',  array($fromDate, $toDate));
		else
			return $query;
	}
	
	public function scopeToDate($query, $toDate)
	{
		if(isset($toDate) && !empty($toDate))
			return $query->where('course_date', '<=', $toDate);
		else
			return $query;
	}
	
	public function getCourseDateDescriptionAttribute()
	{
		$description =  date('M-d-Y (D)', strtotime($this->course_date));
		if ($this->isCourseCompleted())
			$description .=  "  " . $this->time_start . " - COMPLETED";
		else if ($this->full)
			$description .=  "  " . $this->time_start . " - FULL";
		else 
			$description .= "  " . $this->time_start . '-' . $this->time_end;
		
		return $description;
	}

	public function getIsCourseAccreditedAttribute()
	{
		return $this->course->is_accredited;
	}

	public function isNextCourse() 
	{
		if (date('Ymd', strtotime($this->course_date)) == date("Ymd"))
			return true;
		
		return false;
	}

	public function isCourseCompleted() 
	{
		if (date('YmdHis', strtotime($this->course_date . ' ' . $this->time_end)) < date("YmdHis"))
			return true;
		
		return false;
	}


}