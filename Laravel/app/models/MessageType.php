<?php

class MessageType extends Eloquent {
	protected $table = 'messagetypes';

	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'active' => 'required'
	);
}