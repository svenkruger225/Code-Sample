<?php

class Order extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'customer_id' => 'required',
		'order_date' => 'required',
		'comments' => 'required',
		'status_id' => 'required',
		'gst' => 'required',
		'total' => 'required'
		);
	
	public function invoices()
	{
		return $this->hasMany('Invoice');
	}
	
	public function groups()
	{
		return $this->hasMany('GroupBooking');
	}

    public function getAgentNameAttribute()
    {
        if(!empty($this->agent_id) && !is_null($this->agent))
        {
            return $this->agent->name;
        }
        else
        {
            return '--Agent Removed--';
        }

        if(!empty($this->company_id) && !is_null($this->company))
        {
            return $this->company->name;
        }
        else
        {
            return '--Company Removed--';
        }
        return '';
    }
	
	public function getCurrentInvoiceAttribute()
	{
		//if ($this->invoices->count())
		//{
			return $this->invoices->last();
			//$invoice = null;
			//foreach($this->invoices as $inv)
			//{
			//	if ($inv->status->name != 'Credit Note')
			//	{
			//		$invoice = $inv;
			//		break;
			//	}
			//}
			//return $invoice;
		//}
		//return null;
	}
	
	public function getPreviousInvoiceAttribute()
	{
		if ($this->invoices->count() > 1)
		{
			$index = $this->invoices->count() - 2;
			return $this->invoices[$index];
		}
		return null;
	}
	
	public function location()
	{
		return $this->belongsTo('Location');
	}
	
	public function purchase()
	{
		return $this->hasOne('Purchase');
	}
	
	public function items()
	{
		return $this->hasMany('Item');
	}
	
	public function getActiveItemsAttribute()
	{
		return $this->items->filter(function($item) { if ($item->active) { return $item; } });
		//return $this->items->sortBy(function($i) {$i->class_date_time_start; });
	}	
	
	public function getPublicBookingItemsAttribute()
	{
		return $this->items->filter(function($item) 
		{
			if (!empty($item->course_instance_id) && $item->active)
			{
				return $item;
			}
		});
	}
	
	public function getGroupBookingItemsAttribute()
	{
		return $this->items->filter(function($item) 
		{
			if (!empty($item->group_booking_id) && $item->active)
			{
				return $item;
			}
		});
	}
	
	public function getVoucherItemsAttribute()
	{
		return $this->items->filter(function($item) 
		{
			if ($item->item_type_id == \Utils::ItemTypeId('Voucher') && $item->active)
			{
				return $item;
			}
		});
	}
	
	public function getCoursesListAttribute()
	{
		$list = array();
		foreach( $this->active_items as $item) 
		{
			if (!empty($item->course_instance_id) && $item->item_type_id == Utils::ItemTypeId('Course'))
			{
				array_push($list, $item->instance->course_id);
			}
			elseif (!empty($item->course_instance_id) && $item->item_type_id == Utils::ItemTypeId('OnlineCourse'))
			{
				array_push($list, $item->course_instance_id);
			}
			elseif  (!empty($item->group_booking_id))
			{
				array_push($list, $item->groupbooking->course_id);
			}
		}
		return $list;
	}
	
	public function getClassesListAttribute()
	{
		$list = array();
		foreach( $this->active_items as $item) 
		{
			if (!empty($item->course_instance_id))
			{
				array_push($list, $item->course_instance_id);
			}
			elseif  (!empty($item->group_booking_id))
			{
				array_push($list, $item->group_booking_id);
			}
		}
		return $list;
	}
	
	public function vouchers()
	{
		return $this->hasMany('Voucher');
	}
	
	public function rosters()
	{
		return $this->hasMany('Roster');
	}
	
	public function getStudentsAttribute()
	{
		$students = array();
		foreach ($this->rosters as $roster) 
		{
			if ($roster->customer && $roster->is_course_accredited)
				$students[$roster->customer_id] = $roster->customer;
		}
		
		return $students;

	}		
	
	public function onlinerosters()
	{
		return $this->hasMany('OnlineRoster');
	}
	
	public function customer()
	{
		return $this->belongsTo('Customer');
	}
	
	public function agent()
	{
		return $this->belongsTo('Agent', 'agent_id');
	}
	
	public function company()
	{
		return $this->belongsTo('Company', 'company_id');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}
		
	public function referrer()
	{
		return $this->hasOne('ReferrerLog', 'order_id');
	}
	
	public function status()
	{
		return $this->belongsTo('Status');
	}
	
	public function payments()
	{
		return $this->hasMany('Payment');
	}
	
	public function getLastPaymentMethodAttribute()
	{
		return $this->payments->last();
	}		
	
	public function getPaymentMethodAttribute()
	{
		$payments = $this->payments->filter(function($payment) 
			{
				if ($payment->status_id == \Utils::StatusId('Payment','OK'))
				{
					return $payment;
				}
			});
		
		if (count($payments) > 1)
		{
			$text = '';
			foreach ($payments as $payment)
			{
				$text .= $text != '' ? ', ' : '';
				$text .= $payment->method ? $payment->method->name : '';
			}
			return $text;
		}
		else if (count($payments) > 0)
		{
			$payment = $payments->first();
			return $payment->method ? $payment->method->name : '';
		}
		else
		{
			return '';
		}

	}		

	public function setCurrentPaymentMethodAttribute($value)
	{
		$this->attributes['current_payment_method'] = $value;
	}
		
	public function getValidPaymentsAttribute()
	{
		return $this->payments->filter(function($payment) 
			{
				if ($payment->status_id == \Utils::StatusId('Payment','OK'))
				{
					return $payment;
				}
			});
	}		
	

	public function getPaidAttribute()
	{
		$sum =  0.00;
		if ($this->valid_payments->count())
		{
			foreach($this->valid_payments as $payment)
			{
				if ($payment && $payment->status && $payment->status->name == 'OK')
					$sum = $sum + floatval($payment->total);
			}
		}
		return number_format($sum, 2, '.', '');
	}
	
	public function getOwingAttribute()
	{
		$paid = str_replace(',', '', $this->paid);
		return number_format(floatval($this->total) - floatval($paid), 2, '.', '');
	}
	
	public function getIsAgentToPayAttribute()
	{
		$paid = str_replace(',', '', $this->paid);
		return number_format(floatval($this->total) - floatval($paid), 2, '.', '');
	}
	
	public function isPaid()
	{
		return $this->paid == $this->total;
	}
	
	public function isCancelled()
	{
		return $this->status->name == 'Cancelled';
	}
	
	public function isOnlineBooking()
	{
		//return true if starts_with with Online
		return (substr($this->order_type, 0, strlen("Online")) === "Online");
		//return $this->order_type == 'Online';
	}
	
	public function isPublicBooking()
	{
		//return true if starts_with with Public
		return (substr($this->order_type, 0, strlen("Public")) === "Public");
		//return $this->order_type == 'Public';
	}
	
	public function isGroupBooking()
	{
		return $this->order_type == 'Group';
	}
	
	public function isPurchaseBooking()
	{
		return $this->order_type == 'Purchase';
	}
	
	public function isBackend()
	{
		return $this->backend == '1';
	}

	//public function getOrderTypeAttribute()
	//{
	//	if (count($this->items) == 0)
	//		return '';
	//	if (!empty($this->active_items->first()->group_booking_id))
	//		return 'Group';
	//	if (!empty($this->active_items->first()->course_instance_id) || !empty($this->active_items->first()->vouchers_ids))
	//		return 'Public';
	//	if (!empty($this->active_items->first()->product_id))
	//		return 'Purchase';
	//	return 'Public';

	//}

	public function getGatewayResponsesAttribute()
	{
		$results = array();
		$sql = "SELECT `id`,`order_id`,`session_id`,`session_content`,`paypal_response` as `gateway_response`,`returned_page`,`created_at`,`updated_at`
				FROM `paypalsessions`
				WHERE order_id = '" . $this->id . "' OR
				session_content LIKE '%\"OrderId\":" . $this->id . "%'";

		$paypalsessions = DB::select($sql);
		if (count($paypalsessions) > 0)
			foreach($paypalsessions as $session) 
			{
				$session_content = json_decode($session->session_content, true);
				if(is_array($session_content))
				{
					foreach ($session_content as $key => &$value) {	$value = strlen($value) >= 36 ? $key . '=' . \Utils::wrapText($value, 36, '<br>') : $key . '=' . $value . '<br>';}
					$response = rawurldecode(implode($session_content, ''));
				}
				else { $response = $session_content; }
				$session->session_content = $response;
			
				$gateway_response = json_decode($session->gateway_response, true);
				if(is_array($gateway_response))
				{
					foreach ($gateway_response as $key => &$value) {	$value = strlen($value) >= 36 ? $key . '=' . \Utils::wrapText($value, 36, '<br>') : $key . '=' . $value . '<br>';}
					$response = rawurldecode(implode($gateway_response, ''));
				}
				else { $response = $session_content; }
				$session->gateway_response = $response;
				array_push($results, $session);			
			}

		$sql = "SELECT `id`,`order_id`,`session_id`,`session_content`,`payway_response` as `gateway_response`,`returned_page`,`created_at`,`updated_at`
				FROM `paywaysessions`
				WHERE order_id = '" . $this->id . "' OR
				session_content LIKE '%\"OrderId\":" . $this->id . "%'";

		$paywaysessions = DB::select($sql);
		if (count($paywaysessions) > 0)
			foreach($paywaysessions as $session) 
			{
				$session_content = json_decode($session->session_content, true);
				if(is_array($session_content))
				{
					foreach ($session_content as $key => &$value) {	$value = strlen($value) >= 36 ? $key . '=' . \Utils::wrapText($value, 36, '<br>') : $key . '=' . $value . '<br>';}
					$response = rawurldecode(implode($session_content, ''));
				}
				else { $response = $session_content; }
				$session->session_content = $response;
			
				$gateway_response = json_decode($session->gateway_response, true);
				if(is_array($gateway_response))
				{
					foreach ($gateway_response as $key => &$value) {	$value = strlen($value) >= 36 ? $key . '=' . \Utils::wrapText($value, 36, '<br>') : $key . '=' . $value . '<br>';}
					$response = rawurldecode(implode($gateway_response, ''));
				}
				else { $response = $session_content; }
				$session->gateway_response = $response;
				array_push($results, $session);			
			}

		return $results;
	}

	public function getGroupBookingAttribute()
	{
		return \GroupBooking::where('order_id', $this->id)->where('active', 1)->first();
	}

	public function getRebookFeeAttribute()
	{
		$sum =  0.00;
		if ($this->items->count())
		{
			foreach($this->items as $item)
			{
				if ($item && $item->itemtype && $item->itemtype->name == 'RebookFee')
					$sum = $sum + floatval($item->total);
			}
		}
		return number_format($sum, 2);
	}

	public function getDiscountAttribute()
	{
		$sum =  0.00;
		if ($this->items->count())
		{
			foreach($this->items as $item)
			{
				if ($item && $item->itemtype && $item->itemtype->name == 'Discount')
					$sum = $sum + floatval($item->total);
			}
		}
		return number_format($sum, 2);
	}

	public function getAgentEmployeeAttribute()
	{
		return 'AgentEmployee';
	}


	public function updateOrderTotal($cancel = true)
	{
		$gst = 0;
		$total = 0;	
		$items = \Item::where('order_id', $this->id)->where('active', 1)->get();
		foreach ($items as $item)
		{
			$gst += $item->gst;
			$item_total = $item->isDiscount() ? ($item->total * -1) : $item->total;
			$total += $item_total;
		}		

		$this->gst = $gst;
		$this->total = $total;
		
		if ($cancel && $total <= 0) {
			$this->status_id = \Utils::StatusId('Order', 'Cancelled');
		}
		
		$this->update();
	}


}