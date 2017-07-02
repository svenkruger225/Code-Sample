<?php

class OnlineHistoryStep extends Eloquent {
	
	protected $table = 'online_history_steps';

	protected $guarded = array();

	public static function boot()
	{
		parent::boot();    
		
		static::deleted(function($step)
			{
				$step->answers()->delete();
			});
	}   
	
	public function history()
	{
		return $this->belongsTo('OnlineHistory', 'online_history_id');
	}
	
	public function step()
	{
		return $this->hasOne('OnlineStep', 'id', 'step_id');
	}
	
	public function answers()
	{
		return $this->hasMany('OnlineHistoryAnswer', 'online_history_step_id');
	}	

	public function setRosterIdAttribute($value)
	{
		$this->attributes["roster_id"] = $value;
		//$this->step->roster_id = $value;
		foreach ($this->answers as $ans) {
			$ans->roster_id = $value;
		}
	}
	
	public function getModuleAttribute()
	{
		return $this->step->module;
	}
		
	public function getCorrectAnswersAttribute()
	{
		return $this->answers->filter(function($a) { return $a->result == 1; });
	}
	
	public function getWrongAnswersAttribute()
	{
		return $this->answers->filter(function($a) { return $a->result == 0; });
	}
	
	public function getToBeMarkedAnswersAttribute()
	{
		return $this->answers->filter(function($a) { return $a->result == 2; });
	}
	
	public function getLastQuestionAnsweredAttribute()
	{
		$last_question_answered = null;
		foreach( $this->step->questions as $question) 
		{
			$question_answered = false;
			foreach($this->answers as $answer)				
			{
				if ($answer->question_id == $question->id) {
					$question_answered = true;
					$last_question_answered = $answer->question;
					break;
				}	
			}
			if (!$question_answered)
				break;
		}
		
		return $last_question_answered;
	}
	
	public function getNextQuestionToAnswerAttribute()
	{
		$next_question = null;
		if ($this->last_question_answered) {
			$last = $this->last_question_answered;
			$questions = $this->step->questions->sortBy(function($q) { return $q->order; });
			foreach ($questions as $q) {			
				if ( $q->order > $last->order) {
					$next_question = $q;
					break;
				}
			}
		}
		//print_r($next_question);
		//exit();
		return $next_question;
	}
	
	public function getLastQuestionOnStepAttribute()
	{
		$questions = $this->step->questions->sortBy(function($q) { 
			return $q->order; 
		});
		return $questions->last();
	}
	
	public function getDisplayResultsAttribute()
	{
		//print $this->answers->count() . " --- answers\n";
		//print $this->step->questions->count() . " --- questions\n";
		//print $this->next_question_to_answer . " --- next\n";
		//print ($this->answers->count() && $this->answers->count() == $this->step->questions->count() && !$this->next_question_to_answer) . " --- if\n";
		return	$this->answers->count() && 
				$this->answers->count() == $this->step->questions->count() && 
				!$this->next_question_to_answer ? 1 : 0;
		
		//return 	$this->last_question_on_step && 
		//		$this->last_question_answered && 
		//		$this->last_question_on_step->id ==  $this->last_question_answered->id;
	}
	
	public function getDisplayModuleResultsAttribute()
	{
		return 	$this->last_question_on_step && 
			$this->last_question_answered && 
			$this->last_question_on_step->id == $this->last_question_answered->id;
	}

	
	public function IsCompleted()
	{
		return $this->answers->count() && $this->next_question_to_answer ? 1 : 0;
	}

	
}