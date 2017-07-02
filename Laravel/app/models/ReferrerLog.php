<?php

class ReferrerLog extends Eloquent {
	
	protected $table = 'referrers_log';

	protected $guarded = array();

	public static $rules = array(
		);
	
	public function referrer()
	{
		return $this->belongsTo('Referrer', 'referrer_id');
	}
	
	public function order()
	{
		return $this->belongsTo('Order', 'order_id');
	}
	
}