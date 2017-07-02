<?php

class Attachment extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'path' => 'required'
	);

	public static $attachment_rules = array(
		'attachment' => 'required|mimes:pdf,doc'
		);
	public static $attachment_marketing_rules = array(
		'attachment' => 'required|mimes:pdf,doc,jpeg,bmp,png'
		);
	
	public function emails()
	{
		return $this->belongToMany('Email');
	}

}