<?php

class PaymentMethod extends Eloquent {

	protected $table = 'payment_methods';

	protected $guarded = array();

	public static $rules = array(
		'code' => 'required',
		'name' => 'required',
		'fee' => 'required',
		'active' => 'required'
	);
}