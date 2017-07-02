<?php

class OnlineQuestion extends Eloquent {
	
	protected $table = 'online_questions';
	
	protected $guarded = array();

	public static $rules = array(
		);

	public static function boot()
	{
		parent::boot();    
		
		static::deleted(function($question)
			{
				$question->answers()->delete();
			});
	}   

	public function step()
	{
		return $this->belongsTo('OnlineStep', 'step_id');
	}

	public function answers()
	{
		return $this->hasMany('OnlineAnswer', 'question_id')->orderBy('order');
	}
	
	public function getRandomOrderedAnswersAttribute()
	{
		$answers = $this->answers->toArray();
		shuffle($answers);
		$collection =  new Illuminate\Database\Eloquent\Collection();
		foreach ($answers as $answer)
		{
			$online_answer = new OnlineAnswer($answer);
			$collection->add($online_answer);
		}
		return $collection;
	}

	public function setRosterIdAttribute($value)
	{
		$this->attributes["roster_id"] = $value;
	}
	
	public function getLastAnswerOrderAttribute()
	{
		return $this->answers->count() ? $this->answers->last()->order : 0;
	}

	public function getIndexAttribute()
	{
		$index = 0;
		$qs = $this->step->questions->sortBy(function($q){return $q->order;}); 
		foreach ($qs as $q) 
		{
			$index++;
			if ($q->order == $this->order)
			{
				break;
			}
		};
		
		return $index;
	}

	public function getNextAttribute()
	{
		$next = null;
		$qs = $this->step->questions->sortBy(function($q){return $q->order;}); 
		foreach ($qs as $q) 
		{
			if ($q->order > $this->order)
			{
				$next = $q;
				break;
			}
		};
		
		return $next;
	}

	public function getPreviousAttribute()
	{
		$previous = null;
		$qs = $this->step->questions->sortByDesc(function($q){return $q->order;}); 
		foreach ($qs as $q) 
		{
			if ($q->order < $this->order)
			{
				$previous = $q;
				break;
			}
		};
		
		return $previous;
	}

	public function getNextOrderAttribute()
	{
		return $this->next ? $this->next->order : 0;
	}

	public function getPreviousOrderAttribute()
	{
		return $this->previous ? $this->previous->order : 0;
	}

	public function getCorrectAnswersAttribute()
	{
		return $this->answers->filter(function($answer) { return $answer->correct; });
	}
	
	public function IsCompleted()
	{
		$correct = 0;
		$sql = 'SELECT oha.* 
		FROM online_history_step_answers oha 
		JOIN online_history_steps ohs ON ohs.id = oha.online_history_step_id 
		JOIN online_history oh ON oh.id = ohs.online_history_id 
		WHERE oh.roster_id = ' . $this->roster_id . ' AND ohs.step_id = ' . $this->step_id;		
		$questions = DB::select( $sql );
		foreach ($questions as $question)
		{
			if ($question->result == 1)
			{
				$correct++;
			}
		}
		return $this->step->questions->count() == $correct ? true : false;	
		
	}
}