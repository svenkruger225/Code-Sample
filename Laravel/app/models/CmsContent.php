<?php

class CmsContent extends Eloquent {
	
	protected $table = 'cms_contents';
	
	protected $guarded = array();

	public static $rules = array(
		);


	public function page()
	{
		return $this->belongsTo('CmsPage');
	}


}