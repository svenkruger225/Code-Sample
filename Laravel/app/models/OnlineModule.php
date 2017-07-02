<?php

class OnlineModule extends Eloquent {
	
	protected $table = 'online_modules';
	
	protected $guarded = array();

	public static $rules = array(
		);

	public static function boot()
	{
		parent::boot();    
		
		static::deleted(function($module)
			{
				$module->steps()->delete();
			});
	}   

	public function course()
	{
		return $this->belongsTo('Course', 'course_id');
	}

	public function steps()
	{
		return $this->hasMany('OnlineStep', 'module_id')->orderBy('order');
	}

	public function setRosterIdAttribute($value)
	{
		$this->attributes["roster_id"] = $value;
		foreach ($this->steps as $step) {
			$step->roster_id = $value;
		}
	}
	
	public function getQuestionsAttribute()
	{
		$result = new \Illuminate\Database\Eloquent\Collection();
		foreach ($this->steps as $step)
		{
			$result = $result->merge($step->questions);
		}
		return $result->sort(function ($a, $b) {
				return strcmp($a->step_id, $b->step_id)
					?: strcmp($a->order, $b->order);
			});
	}

	public function getLastStepOrderAttribute()
	{
		return $this->steps->count() ? $this->steps->last()->order : 0;
	}

	public function getNextIdAttribute()
	{
		$result = \DB::table('online_modules')
			->where('order', '>', $this->order)
			->where('active', 1)
			->first(array('id'));
		return count($result) > 0 ? $result->id : 0;
	}

	public function getPreviousIdAttribute()
	{
		$result = \DB::table('online_modules')
			->where('order', '<', $this->order)
			->where('active', 1)
			->first(array('id'));
		return  count($result) > 0 ? $result->id : 0;
	}
	
	public function getStepsCompletedAttribute()
	{
		$completed = 0;
		foreach ($this->steps as $step)
		{
			if ($step->IsCompleted())
			{
				$completed++;
			}
		}
		return $completed;
	}	
	
	public function getProgressAttribute()
	{
		return \Utils::GetPercentage($this->steps_completed, $this->steps->count());
	}	
		
	public function IsCompleted()
	{
		$completed = true;
		foreach ($this->steps as $step)
		{
			if (!$step->IsCompleted())
			{
				$completed = false;
				break;
			}
		}
		return $completed;
	}
	
}