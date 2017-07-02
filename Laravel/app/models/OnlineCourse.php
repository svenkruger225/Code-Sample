<?php

class OnlineCourse extends Course {
	
	protected $appends = array('item_type');

	public function instances()
	{
		return $this->hasMany('CourseInstance', 'course_id');
	}
		
	public function modules()
	{
		return $this->hasMany('OnlineModule', 'course_id')->orderBy('order');
	}
	
	public function rosters()
	{
		return $this->hasMany('OnlineRoster', 'course_id');
	}

	public function setRosterIdAttribute($value)
	{
		$this->attributes["roster_id"] = $value;
		foreach ($this->modules as $module) {
			$module->roster_id = $value;
		}
	}
	
	public function getLastModuleOrderAttribute()
	{
		return $this->modules->count() ? $this->modules->last()->order : 0;
	}
	
	public function getItemTypeAttribute()
	{
		if($this->type == 'Online')
			return $this->attributes['item_type'] = 'OnlineCourse';
		else
			return $this->attributes['item_type'] = 'Course';
	}

	public function getStepsAttribute()
	{
		$result = new \Illuminate\Database\Eloquent\Collection();		
		foreach ($this->modules as $module)
		{
			$result = $result->merge($module->steps);
		}
		
		return $result;
	}	

	public function getQuestionsAttribute()
	{
		$result = new \Illuminate\Database\Eloquent\Collection();
		foreach ($this->modules as $module)
		{
			foreach ($module->steps as $step)
			{
				$result = $result->merge($step->questions);
			}
		}
		return $result->sort(function ($a, $b) {
				return strcmp($a->step_id, $b->step_id)
					?: strcmp($a->order, $b->order);
			});
	}	
	
	public function getModulesCompletedAttribute()
	{
		$completed = 0;
		foreach ($this->modules as $module)
		{
			if ($module->IsCompleted())
			{
				$completed++;
			}
		}
		return $completed;
	}	
	
	public function getProgressAttribute()
	{
		return \Utils::GetPercentage($this->modules_completed, $this->modules->count());
	}		
	public function IsCompleted()
	{
		$completed = true;
		foreach ($this->modules as $module)
		{
			if (!$module->IsCompleted())
			{
				$completed = false;
				break;
			}
		}
		return $completed;
	}

}