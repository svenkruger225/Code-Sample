<?php

class Item extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		//'order_id' => 'required',
		'description' => 'required',
		//'qty' => 'required',
		'price' => 'required',
		//'gst' => 'required',
		//'total' => 'required'
	);
	
	public function item_type()
	{
		return $this->belongsTo('ItemType', 'item_type_id');
	}
	
	public function itemtype()
	{
		return $this->belongsTo('ItemType', 'item_type_id');
	}
	
	public function order()
	{
		return $this->belongsTo('Order');
	}
	
	public function rosters()
	{
		return $this->hasMany('Roster', 'item_id');
	}

	public function getIsCourseAccreditedAttribute()
	{
		if( !empty($this->course_instance_id) )
			return $this->instance->course->is_accredited;
		else if( !empty($this->group_booking_id) )
			return $this->groupbooking->course->is_accredited;
		
		return false;

	}	
	public function getItemDateAttribute()
	{
		return $this->order->order_date;
	}
	
	public function getClassDateTimeStartAttribute()
	{
		if (!$this->instance && $this->course_instance_id) {
			$date_time = 'Online';
		}
		elseif ($this->course_instance_id) {
			$date_time = date('Y-m-d H:i:s', strtotime($this->instance->course_date . ' ' . $this->instance->time_start));
		}
		elseif ($this->group_booking_id) {
			$date_time = date('Y-m-d H:i:s', strtotime($this->groupbooking->course_date . ' ' . $this->groupbooking->time_start));
		}
		else {
			$dt = new DateTime();
			$date_time = $dt->format('Y-m-d H:i:s');
		}
		return $date_time;	
	}
	
	public function instance()
	{
		return $this->belongsTo('CourseInstance', 'course_instance_id');
	}
	
	public function product()
	{
		return $this->belongsTo('Product');
	}
	
	public function groupbooking()
	{
		return $this->belongsTo('GroupBooking', 'group_booking_id');
	}
	
	public function getVouchersAttribute()
	{
		if($this->vouchers_ids != null && $this->vouchers_ids != '')
		{
			$ids = json_decode($this->vouchers_ids);
			
			$vouchers = \Voucher::whereIn('id',  $ids)->get();
			return $vouchers;
		} 
		else 
		{
			return null;
		}
	}

	public function getPaidAttribute()
	{

		$paid = 0;
		$order_paid = $this->order->paid;
		
		$items = $this->order->active_items->sortBy(function($item) { return $item->class_date_time_start; } );

		//foreach($this->order->active_items as $item)
		//{
		//	if ($this->id == $item->id ) { $paid = $order_paid < $this->total ? $order_paid : $this->total; }
		//	$order_paid = $order_paid >= $item->total ? (float)$order_paid - (float)$item->total : 0;
		//}
		foreach($this->order->active_items as $item)
		{
			//\Log::debug($this->id . ' : ' . $item->id . ' : ' . $item->order_id . ' : ' . $item->total . ' : ' . $order_paid . ' : ' . $paid);
			if ($this->id == $item->id ) { 
				$paid = $order_paid < $this->total ? $order_paid : $this->total; 
				//\Log::debug($this->id . ' : ' . $item->id . ' : ' . $item->order_id . ' : ' . $item->total . ' : ' . $order_paid . ' : ' . $paid);
			}
			
			$order_paid = $order_paid >= $item->total ? (float)$order_paid - (float)$item->total : 0;
		}
		//\Log::debug($this->id . ' : paid: ' . $paid);
		
		return \Utils::format_number($paid);
	}

	public function getOwingAttribute()
	{
		return \Utils::format_number($this->total - $this->paid);
	}

	public function isPaid()
	{
		return $this->paid == $this->total;
	}

	public function isFee()
	{
		return $this->itemtype && ($this->itemtype->name == 'RebookFee' || $this->itemtype->name == 'PaymentFee');
	}

	public function isDiscount()
	{
		return $this->itemtype && $this->itemtype->name == 'Discount';
	}
	
	public function filterByLocation($location_list = array())
	{
		if(count($location_list) > 0) 
		{

			return $query->wherein('location_id', $locations);
		}
		else
			return $query;
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}
        public static function getVoucher($orderId,$voucherId){
            $itemObj=Item::where('order_id', '=' ,$orderId)->where('vouchers_ids' ,'=', '['.$voucherId.']')->first();
            if($itemObj!= NULL){
                return $itemObj;
            }
            $itemObj=Item::where('order_id', '=' ,$orderId)->where('vouchers_ids' ,'LIKE', '['.$voucherId.',%')->first();
            if($itemObj!= NULL){
                return $itemObj;
            }
            $itemObj=Item::where('order_id', '=' ,$orderId)->where('vouchers_ids' ,'LIKE', '%,'.$voucherId.']')->first();
            if($itemObj!= NULL){
                return $itemObj;
            }
            $itemObj=Item::where('order_id', '=' ,$orderId)->where('vouchers_ids' ,'LIKE', '%,'.$voucherId.',%')->first();
            if($itemObj!= NULL){
                return $itemObj;
            }
            return $itemObj['price'] = 0.00;
        }

}