<?php

class AgentCourse extends Eloquent {
	
	protected $table = 'agent_course';

	protected $guarded = array();
	
	public static $rules = array(
		);


	public function agent()
	{
		return $this->belongsTo('Agent');
	}

	public function location()
	{
		return $this->belongsTo('Location');
	}

	public function course()
	{
		return $this->belongsTo('Course');
	}

}