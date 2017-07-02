<?php

class OnlineRoster extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'customer_id' => 'required',
		'certificate_id' => 'required',
		'comments' => 'required',
		'attendance' => 'required'
		);

	public function course()
	{
		return $this->belongsTo('OnlineCourse', 'course_id');
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
	
	public function history()
	{
		return $this->hasOne('OnlineHistory', 'roster_id');
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
			foreach ($this->order->active_items as $item)
			{
				if ($item->course_instance_id == $this->course_id && $item->item_type_id == Utils::ItemTypeId('OnlineCourse'))
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
		return 'Online';
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
	
	public function getLastHistoryStepAttribute()
	{
		if($this->history)
		{
			return $this->history->steps->last();
		}
		return null;
	}
	
	public function getLastHistoryStepQuestionAnsweredAttribute()
	{
		if($this->last_history_step)
		{
			return $this->last_history_step->last_question_answered;
		}
		return null;
	}

	public function getCurrentCourseAnswersAttribute()
	{
		$result = new \Illuminate\Database\Eloquent\Collection();
		if($this->history)
		{
			foreach ($this->history->steps as $step)
			{
				$step->roster_id = $this->id;
				$result->add($step);
			}
			//$result = $result->sortBy(function($h)
			//	{
			//		return $h->step->order;
			//	});
			$result = $result->sort(function ($a, $b) {
					return strcmp($a->step->module_id, $b->step->module_id)
					?: strcmp($a->step->order, $b->step->order);
				});
		}
		return $result;
	}
	
	public function setCurrentModuleAnswersAttribute($module_id)
	{
		$result = new \Illuminate\Database\Eloquent\Collection();
		if($this->history)
		{
			foreach ($this->history->steps as $step)
			{
				if($step->step && $step->step->module_id == $module_id && $step->answers->count())
				{
					$result->add($step);
				}
			}
			$result = $result->sortBy(function($h)
				{
					return $h->step->order;
				});
		}
		$this->attributes['current_module_answers'] = $result;
	}

	public function setCurrentHistoryStepAttribute($step_id)
	{
		$steps = null;
		if($this->history)
		{
			$steps =  $this->history->steps->filter(function($h) use($step_id){	
					return $h->step && $h->step->id == $step_id;
			});
		}
		$this->attributes['current_history_step'] = $steps && $steps->count() ? $steps->first() : null;
	}
	
	public function ModuleAnswers($module_id)
	{
		$course_id = $this->course_id;
		$result = new \Illuminate\Database\Eloquent\Collection();
		if($this->history)
		{
			foreach ($this->history->steps as $history)
			{
				if($history->answers->count())
				{
					$result->add($history);
				}
			}
			return $result->sortBy(function($h)
				{
					return $h->step->order;
				});
		}
		return $result;
		
	}

	public function CurrentHistoryStep($step_id)
	{
		if($this->history)
		{
			$steps =  $this->history->steps->filter(function($h) use($step_id){	
					return $h->step && $h->step_id == $step_id;
			});
			return $steps->first();
		}
		return null;
	}

	public function isPaid()
	{
		return (float)$this->item->price <= (float)$this->item->paid;
	}

}