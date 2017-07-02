<?php

class CourseInstance extends Eloquent {
	
	protected $table = 'courseinstances';
	
	protected $guarded = array();

	public static $rules = array(
		'course_id' => 'required|integer',
		'location_id' => 'required|integer',
		'course_date' => 'required|date',
		'time_start' => 'required',
		'time_end' => 'required',
		'students' => 'required|integer',
		'maximum_students' => 'required|integer'
	);

	public function course()
	{
		return $this->belongsTo('Course');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}
	
	public function getParentLocationAttribute()
	{
		if ($this->location->parent_id == 0)
		{
			return $this->location;
		}
		else
		{
			return \Location::find($this->location->parent_id);
		} 
	}

	public function instructors()
	{
		return $this->belongsToMany('Instructor', 'course_instance_instructor', 'course_instance_id', 'user_id');
	}

	public function rosters()
	{
		return $this->hasMany('Roster');
	}

	public function special()
	{
		return $this->hasOne('CourseInstanceSpecial', 'course_instance_id');
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

	public function getVacanciesAttribute()
	{
		return $this->maximum_students - $this->students;
	}
	
	public function getStartTimeAttribute()
	{
		return date('h:i A', strtotime($this->time_start));
	}
	
	public function getEndTimeAttribute()
	{
		return date('h:i A', strtotime($this->time_end));
	}
	
	public function getPairClassIdToAddAttribute()
	{
        //return 100;
		$class_id = '';
		if (!empty($this->course->pair_course_id_to_add))
        {
			$location_id = $this->location_id;
			$parent_id = $this->parent_location->id;
			$course_date = $this->course_date;
			$time_end = $this->time_end;
			$class_id = \DB::table('courseinstances')
				->where('course_id',$this->course->pair_course_id_to_add)
			->where( function ($query) use ($location_id,$parent_id) 
					{
						$query->where('location_id', $location_id)
						->orWhereIn( 'location_id', function($q) use ($parent_id) 
							{
								$q->select (DB::raw('id'))
								->from('locations')
								->where('parent_id', $parent_id);
							});
					})
				->where(
					DB::raw('STR_TO_DATE(CONCAT(course_date, \' \', time_end), \'%Y-%m-%d %H:%i\')'), '>',
					DB::raw('STR_TO_DATE(\'' .$this->course_date. ' ' . $this->time_end . '\', \'%Y-%m-%d %H:%i\')')
				)
			->orderBy(DB::raw('STR_TO_DATE(CONCAT(course_date, \' \', time_end), \'%Y-%m-%d %H:%i\')'))
			->pluck('id');
		}
		return $class_id;
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
	
	public function getPaidTotalAttribute()
	{
		
		$sql = "SELECT sum(
						case 
						when (o.total <= (select sum(total) from payments p where p.order_id = r.order_id and p.status_id = 8 )) then 1 
						when EXISTS(select 1 from payments p where p.order_id = r.order_id and p.payment_method_id = 7 limit 1 ) then 1
						else 0 
						end) as paid 
				FROM rosters r 
				JOIN orders o on r.order_id = o.id 
				WHERE r.course_instance_id = '" . $this->id . "' and o.status_id != 4";
		$result = \DB::select( $sql );

		return $result[0]->paid ? $result[0]->paid : 0;		
		
		//$paid = 0;
		//foreach($this->rosters as $roster) {
		//	$paid += $roster->isPaid() || $roster->Item->order->agent_id ? 1 : 0;
		//}
		//return $paid;		
		
	}
	
	public function getNotPaidTotalAttribute()
	{
		//$notpaid = 0;
		//foreach($this->rosters as $roster) {
		//	$notpaid += $roster->isPaid() || $roster->Item->order->agent_id ? 0 : 1;
		//}
		//return $notpaid;
				
		return $this->students - $this->paid_total;		
		
	}

	
	public function getCourseDateTimeAttribute()
	{
		$date_time_str =  $this->course_date . ' ' . $this->time_start;
		$date_time = new \DateTime($date_time_str);
		
		return $date_time->format('Y-m-d H:i');
	}
	
	public function getCourseDateDescriptionAttribute()
	{
		$description =  date('d M Y (D)', strtotime($this->course_date));
		$status = "  " . $this->start_time . '-' . $this->end_time;

		if ($this->isCourseStarted())
			$status =  "  " . $this->start_time . " - STARTED";
		if ($this->full)
			$status =  "  " . $this->start_time . " - FULL";
		if ($this->isCourseCompleted())
			$status =  "  " . $this->start_time . " - COMPLETED";
		
		return $description . $status;
	}


	public function getIsCourseAccreditedAttribute()
	{
		return $this->course->is_accredited;
	}
	
	public function getOnlineprice($loc = null)
	{
		$val = 0;
		
		foreach($this->course->prices as $price) {
			if ($price->location_id  == $loc)
			{
				$val = $price->price_online;
				break;
			}                                                          
		}
		
		return $val;
	}
	
	public function getOfflineprice($loc = null)
	{
		$val = 0;
		
		foreach($this->course->prices as $price) {
			if ($price->location_id  == $loc)
			{
				$val = $price->price_offline;
				break;
			}                                                          
		}
		
		return $val;
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
	
	public function isCourseStarted() 
	{
		if (date('YmdHis', strtotime($this->course_date . ' ' . $this->time_start)) < date("YmdHis"))
			return true;
		
		return false;
	}
	
	
	public function validate($options)
	{

		if(empty($options['is_update']) || $options['is_update'] == false)
		{
			if (!$this->active)
			{
				$msg = "This class [" . $this->course->name . ' ' . date('d/m/Y', strtotime($this->course_date)) . ' ' . $this->start_time . '-' . $this->end_time . "] is inactive, please contact our office for assitance";
				throw new \CourseInstanceValidationException($msg);				
			}
			if ( $this->full ) 
			{
				$msg = "This class [" . $this->course->name . ' ' . date('d/m/Y', strtotime($this->course_date)) . ' ' . $this->start_time . '-' . $this->end_time . "] is already full, please conatct our office for assitance";
				throw new \CourseInstanceValidationException($msg);				
			}
		}
		//if  (isset($options['new_total']) && $options['new_total'] > $this->maximum_students) 
		//{
		//	$vacancies = $this->maximum_students - $this->students;
		//	$msg = sprintf("This class [" . $this->course->name . ' ' . date('d/m/Y', strtotime($this->course_date)) . ' ' . $this->start_time . '-' . $this->end_time . "] can accomodate %s student(s) out of the requested %s students(s), please conatct our office for assitance", $vacancies, $options['qty']);
		//	throw new \CourseInstanceValidationException($msg);				
		//}
		
		$vacancies = $this->maximum_students - $this->students + intval($options['qty']);
		if (isset($options['pre_validation']) && $options['pre_validation'] == 1) {
			$vacancies = $this->maximum_students - $this->students;
		}
		
		if  ($vacancies < intval($options['qty'])) 
		{
			$msg = sprintf("This class [" . $this->course->name . ' ' . date('d/m/Y', strtotime($this->course_date)) . ' ' . $this->start_time . '-' . $this->end_time . "] can accomodate %s student(s) out of the requested %s students(s), please conatct our office for assitance", $vacancies, $options['qty']);
			throw new \CourseInstanceValidationException($msg);				
		}

		if  ($this->isCourseStarted()) 
		{
			$msg = sprintf("This class [" . $this->course->name . ' ' . date('d/m/Y', strtotime($this->course_date)) . ' ' . $this->start_time . '-' . $this->end_time . "] has started already, please select an open class");
			throw new \CourseInstanceValidationException($msg);				
		}

		return true;
	}

	public function save(array $options = array())
	{
		if (count($options) > 0 && (empty($options['overrideValidation']) || !$options['overrideValidation']))
			$this->validate($options);
		
		//if ($this->maximum_auto && !empty($options['new_total'])) // if it is maximum auto then we check for class full
		//	$this->full = ($options['new_total'] >= $this->maximum_students) ? 1 : 0;				
		if ($this->maximum_auto) { // if it is maximum auto then we check for class full
			$this->full = ($this->students >= $this->maximum_students) ? 1 : 0;				
			\Log::debug("process full if needed for instance: " . $this->id . " : " . $this->full);
		}
		//\Log::debug("process full if needed for instance2: " . $this->id);

		//$this->students = isset($options['new_total']) ? $options['new_total'] : $this->students;				
		//$result = \DB::select("SELECT COUNT(*) as students FROM rosters WHERE course_instance_id = '" . $this->id . "'");
		//$this->students = $result[0]->students;
		//$instance->full = $instance->maximum_students <= $result[0]->students ? 1 : 0;



		return parent::save();
	}


}