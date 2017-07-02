<?php

class Purchase extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		);
	
	
	public function location()
	{
		return $this->belongsTo('Location');
	}	
	
	public function customer()
	{
		return $this->belongsTo('Customer');
	}	
	
	public function order()
	{
		return $this->belongsTo('Order');
	}

	
	public function getIsMachineHireAttribute()
	{
		$isMachineHire = false;
		foreach($this->order->active_items as $item) 
		{
			if ($item->product->is_machine_hire)
			{
				$isMachineHire = true;
				break;
			}
		};
		return $isMachineHire;
		
	}


	public function getMyobCodeAttribute()
	{
		return '49999';
	}
	
	public function getMyobJobCodeAttribute()
	{
		return 'Admin';
	}

	
}
