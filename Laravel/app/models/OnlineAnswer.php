<?php

class OnlineAnswer extends Eloquent {
	
	protected $table = 'online_answers';
	
	protected $guarded = array();

	public static $rules = array(
		//'price_online' => 'required|integer',
		//'price_offline' => 'required|integer'
		);

	public function question()
	{
		return $this->belongsTo('OnlineQuestion', 'question_id');
	}


}