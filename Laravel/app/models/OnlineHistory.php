<?php

class OnlineHistory extends Eloquent {
	
	protected $table = 'online_history';

	protected $guarded = array();

	public static function boot()
	{
		parent::boot();    
		
		static::deleted(function($history)
			{
				$history->steps()->delete();
			});
	}   
	public function roster()
	{
		return $this->belongsTo('OnlineRoster', 'roster_id');
	}

	public function customer()
	{
		return $this->belongsTo('Customer', 'customer_id');
	}

	public function course()
	{
		return $this->hasOne('Course', 'id', 'course_id');
	}

	public function steps()
	{
		return $this->hasMany('OnlineHistoryStep', 'online_history_id');
	}
	
	public function getModulesAttribute()
	{
		//$collection =  new Illuminate\Database\Eloquent\Collection();
		$existing_modules = array();
		foreach ($this->steps as $h_step)
		{
			//if (!in_array($h_step->step->module->id, $existing_modules)) {
			//	$collection->add(new OnlineModule($h_step->step->module));
			//}			
			$existing_modules[$h_step->step->module->id] = $h_step->step->module->id;
		}
		sort($existing_modules);
		return $existing_modules;
	}

	public function getLastStepAttribute()
	{
		if($this->steps->count())
		{
			$steps =  $this->steps->sortBy(function($step) { 
					return $step->step->order; 
				})->reverse();
			return $steps->first();
		}
		return null;
	}
		
}