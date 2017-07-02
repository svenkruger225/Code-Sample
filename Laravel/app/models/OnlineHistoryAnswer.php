<?php

class OnlineHistoryAnswer extends Eloquent {
	
	protected $table = 'online_history_step_answers';
	
	protected $guarded = array();

	public function historystep()
	{
		return $this->belongsTo('OnlineHistoryStep', 'online_history_step_id');
	}

	public function question()
	{
		return $this->hasOne('OnlineQuestion','id','question_id');
	}

	public function setRosterIdAttribute($value)
	{
		$this->attributes["roster_id"] = $value;
		$this->question->roster_id = $value;
	}

}