<?php

class CreditNote extends Eloquent {
	
	protected $table = 'creditnotes';

	protected $guarded = array();

	public static $rules = array(
		'invoice_id' => 'required'
		);
	
	
	public function invoice()
	{
		return $this->belongsTo('Invoice');
	}
	
}