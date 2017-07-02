<?php

class Payment extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'order_id' => 'required',
		'payment_date' => 'required',
		'payment_method_id' => 'required',
		'instalment' => 'required',
		'status_id' => 'required',
		'total' => 'required'
	);
	
	public function order()
	{
		return $this->belongsTo('Order');
	}
	
	public function payment_method()
	{
		return $this->belongsTo('PaymentMethod', 'payment_method_id');
	}
	
	public function method()
	{
		return $this->belongsTo('PaymentMethod', 'payment_method_id');
	}
	
	public function status()
	{
		return $this->belongsTo('Status', 'status_id');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}
	
	public function getResponseAttribute()
	{
		$gateway_response = json_decode($this->gateway_response, true);

		$response = is_array($gateway_response) ? str_replace('&', '<br>', http_build_query($gateway_response, ', ')) : $gateway_response;
		
		return $response;
	}
	
}