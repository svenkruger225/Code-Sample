<?php

class OnlineStep extends Eloquent {
	
	protected $table = 'online_steps';
	
	protected $guarded = array();

	public static $rules = array(
		);

	public static function boot()
	{
		parent::boot();    
		
		static::deleted(function($step)
			{
				$step->questions()->delete();
			});
	}   

	public function module()
	{
		return $this->belongsTo('OnlineModule', 'module_id');
	}

	public function questions()
	{
		return $this->hasMany('OnlineQuestion', 'step_id')->orderBy('order');
	}

	public function setRosterIdAttribute($value)
	{
		$this->attributes["roster_id"] = $value;
		foreach ($this->questions as $question) {
			$question->roster_id = $value;
		}
	}

	public function getRosterIdAttribute()
	{
		return $this->attributes["roster_id"];
	}

	public function getLastQuestionOrderAttribute()
	{
		return $this->questions->count() ? $this->questions->last()->order : 0;
	}

	public function getNextIdAttribute()
	{
		$result = \DB::table('online_steps')
			->where('module_id', $this->module_id)
			->where('order', '>', $this->order)
			->first(array('id'));
		return count($result) > 0 ? $result->id : 0;
	}

	public function getPreviousIdAttribute()
	{
		$result = \DB::table('online_steps')
			->where('module_id', $this->module_id)
			->where('order', '<', $this->order)
			->first(array('id'));
		return  count($result) > 0 ? $result->id : 0;
	}
	
	public function IsCompleted()
	{
		$completed = true;
		foreach ($this->questions as $question)
		{
			if (!$question->IsCompleted())
			{
				$completed = false;
				break;
			}
		}
		if($this->questions->count() == 0) {
			$h_id = DB::table('online_history as oh')
				->join('online_history_steps as ohs', 'oh.id', '=', 'ohs.online_history_id')
				->where('oh.roster_id',$this->roster_id)
				->where('ohs.step_id',$this->id)
				->pluck('ohs.id');
			\Log::info( 'h_id: ' . $h_id);
			
			if(!$h_id) {
				$completed = false;
			}
		}
		
		
		return $completed;
	}


}