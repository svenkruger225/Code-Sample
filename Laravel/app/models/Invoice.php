<?php

class Invoice extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'order_id' => 'required',
		'invoice_date' => 'required',
		'comments' => 'required',
		'status_id' => 'required'
	);
	
	public function order()
	{
		return $this->belongsTo('Order');
	}
	
	public function creditnote()
	{
		return $this->hasOne('CreditNote', 'invoice_id');
	}
	
	public function status()
	{
		return $this->belongsTo('Status');
	}

}