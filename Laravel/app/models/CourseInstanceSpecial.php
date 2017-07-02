<?php

class CourseInstanceSpecial extends Eloquent {
	
	protected $table = 'courseinstance_specials';
	
	protected $guarded = array();

	public static $rules = array(
		'price_original' => 'required',
		'price_online' => 'required',
		'price_offline' => 'required'
		);

	public function instance()
	{
		return $this->belongsTo('CourseInstance');
	}


}