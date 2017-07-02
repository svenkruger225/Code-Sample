<?php

class MarketingSession extends Eloquent {
	protected $table = 'marketing_sessions';
	protected $guarded = array();

	
	public function message()
	{
		return $this->belongsTo('Marketing', 'message_id');
	}

}