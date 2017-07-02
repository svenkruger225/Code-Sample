<?php

class Roster extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'customer_id' => 'required',
		'certificate_id' => 'required',
		'comments' => 'required',
		'attendance' => 'required'
		);

	public static function boot()
	{
		parent::boot();

		static::saved( function($roster) 
		{
			if (!is_null($roster->course_instance_id))
			{
					$result = \DB::select("SELECT COUNT(*) as students FROM rosters WHERE course_instance_id = '" . $roster->course_instance_id . "'");
					\DB::statement(
							\DB::raw("UPDATE courseinstances SET students = ?, full = (CASE WHEN (maximum_students <= ? AND maximum_auto = 1) THEN 1 ELSE 0 END) WHERE id = ?"), 
						array($result[0]->students, $result[0]->students, $roster->course_instance_id)
					);	
			}
		});
		
	}

	public function instance()
	{
		return $this->belongsTo('CourseInstance', 'course_instance_id');
	}

	public function groupbooking()
	{
		return $this->belongsTo('GroupBooking', 'group_booking_id');
	}

	public function customer()
	{
		return $this->belongsTo('Customer');
	}

	public function order()
	{
		return $this->belongsTo('Order');
	}
	
	public function item()
	{
		return $this->belongsTo('Item');
	}

	public function certificate()
	{
		return $this->hasOne('Certificate');
	}

	public function getFoodHygieneAttribute()
	{
		$fh = '';
		foreach ($this->customer->certificates as $certificate)
		{
			if ($certificate->course_id == '6') 
			{
				if ($certificate->course_date == date("Y-m-d")) 
					$fh = 'MUST BRING';
				else
					$fh = 'COMPLETED';
				break;
			}
		}
		if (empty($fh))
		{
			foreach ($this->customer->documents as $document)
			{
				if ($document->document_type == 'certificate' && $document->course_id == '6') 
				{
					$fh = 'SIGHT ORIG. REC';
					break;
				}
			}
		}
		return empty($fh) ? 'SIGHT ORIG.' : $fh;
	}

	public function getLocationIdAttribute()
	{
		if( ($this->group_booking_id !== null && $this->group_booking_id > 0))
		{
			return $this->groupbooking->location_id;
		} 
		else if( ($this->course_instance_id !== null && $this->course_instance_id > 0))
		{
			return $this->instance->location_id;
		} 
		
		return null;

	}

	public function getValueAttribute()
	{
		$value = 0;
		if($this->item)
		{
			$value = $this->item->price / $this->item->qty;
		}	
		else if($this->order)
		{
			$field_name = $this->roster_type == 'Public' ? 'course_instance_id' : 'group_booking_id';
			$instance_id = $this->$field_name;
			foreach ($this->order->active_items as $item)
			{
				if ($item->$field_name == $instance_id)
				{
					$value = $item->price / $item->qty;
					break;
				}
			}	
		}	
		return \Utils::format_number($value);
	}

	public function getPriceAttribute()
	{
		return \Utils::format_number($this->item->price);
	}

	public function getPaidAttribute()
	{
		return \Utils::format_number($this->item->paid / $this->item->qty);
	}

	public function getOwingAttribute()
	{
		return \Utils::format_number($this->item->owing / $this->item->qty);
	}

	public function getRosterTypeAttribute()
	{
		if( ($this->course_instance_id === null || $this->course_instance_id == 0) &&
			($this->group_booking_id !== null && $this->group_booking_id > 0))
		{
			return 'Group';
		} 
		else if( ($this->group_booking_id === null || $this->group_booking_id == 0) &&
			($this->course_instance_id !== null && $this->course_instance_id > 0))
		{
			return 'Public';
		} 
		
		return 'Public';

	}

	public function getCourseIdAttribute()
	{
		if( !empty($this->course_instance_id) )
			return $this->instance->course_id;
		else if( !empty($this->group_booking_id) )
			return $this->groupbooking->course_id;
		
		return null;

	}

	public function getIsCourseAccreditedAttribute()
	{
		if( !empty($this->course_instance_id) )
			return $this->instance->course->is_accredited;
		else if( !empty($this->group_booking_id) )
			return $this->groupbooking->course->is_accredited;
		
		return false;

	}

	public function getIsAgentBookingAttribute()
	{
		if( !empty($this->order->agent_id) ) return true;
		
		return false;

	}
	public function getIsAgentToPayAttribute()
	{
		if( !empty($this->order->agent_id) ) {
			return strpos($this->order->payment_method, "Agent To Pay") !== false;
		}		
		return false;

	}
	
	public function getCourseDateAttribute()
	{
		if( !empty($this->course_instance_id) )
		{
			return $this->instance->course_date;
		}
		else if( !empty($this->group_booking_id) )
		{
			return $this->groupbooking->course_date;
		}
		
		return null;

	}

	public function getMyobCodeAttribute()
	{
		if( !empty($this->course_instance_id) )
			return $this->instance->course->myob_code;
		else if( !empty($this->group_booking_id) )
			return $this->groupbooking->course->myob_code;
		
		return 'NoCode';

	}
	
	public function getMyobJobCodeAttribute()
	{
		if( !empty($this->course_instance_id) )
			return $this->instance->course->myob_job_code;
		else if( !empty($this->group_booking_id) )
			return $this->groupbooking->course->myob_job_code;
		
		return 'NoCode';

	}

	public function isPaid()
	{
		return (float)$this->paid >= (float)$this->price;
		//return (float)$this->order->paid == (float)$this->order->total;
	}

}