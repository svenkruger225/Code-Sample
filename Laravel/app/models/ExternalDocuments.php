<?php

class ExternalDocuments extends Eloquent {

	protected $table = 'external_documents';

	protected $guarded = array();

	public static $rules = array(
		'customer_id' => 'required',
		'document_type' => 'required',
		'course_id' => 'required_if:document_type,"certificate"',
		'document_file' => 'required',
		'user_id' => 'required',
	);
	
	public function customer()
	{
		return $this->belongsTo('Customer');
	}
	
	public function course()
	{
		return $this->belongsTo('Course');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}


}