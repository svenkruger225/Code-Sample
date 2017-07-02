<?php

class Message extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'message_id' => 'required',
		'subject' => 'required',
		'body' => 'required'
	);
	
	public function location()
	{
		return $this->belongsTo('Location');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function type()
	{
		return $this->belongsTo('MessageType', 'message_id');
	}
	
	public function attachments()
	{
		return $this->belongsToMany('Attachment');
	}

}