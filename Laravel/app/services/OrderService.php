<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Email, DB;
use Order, Roster, OnlineRoster, Item, CourseInstance, Voucher, Log;
use App\Services\payments\PaymentFactory;
use App\Services\BookingPublic;
use App\Services\BookingGroup;
use App\Services\BookingPurchase;
use App\Services\BookingOnline;

class OrderService {

	public function __construct()
	{
	}

	public function activateOrder($order_id)
	{

		Log::debug("Activate Order: " . $order_id);
		$order = Order::find($order_id);
		if ($order->order_type != '')
		{      
			//if we don't have any active items we reactivate the last 
			if (count($order->active_items) == 0)
			{
				$last_item = $order->items->last();
				$last_item->update(array('active' => 1));
			}
			
			foreach($order->active_items as $item)
			{
				if ($order->order_type != 'Purchase')
				{
					for ($x = 1; $x <= $item->qty; $x++) {
						$roster_data = array(
							'id'=> null,
							'order_id' => $order->id,
							'item_id' => $item->id,
							'course_instance_id' => $order->order_type == 'Public' ? $item->course_instance_id : null,
							'group_booking_id' =>  $order->order_type == 'Public' ? null : $item->group_booking_id,
							'customer_id' => $order->customer_id,
							'certificate_id' => null,
							//'description' => $input['description'],
							'notes_admin' =>  $item->notesAdmin,
							'notes_class' => $item->notesClass
							);
						$roster = Roster::create($roster_data);	
					}
				}			
				if ($order->order_type == 'Public' && $item->course_instance_id)
				{
					// get the c urrent instance and the no show instance
					$ci = CourseInstance::find($item->course_instance_id);
					Log::debug("Activate Order Update Instance: " . $ci->id . ", current students: " . $ci->students . ", add students: " . $item->qty);
					$ci->students += $item->qty;
					$ci->save();
					// update current instance qty
					//$ci->update(array('students' => ($ci->students - 1)));
				}
			}

			$order->updateOrderTotal();	
			$order->update(array('status_id' => Utils::StatusId('Order', 'Open')));
		}
		else
		{
			$msg = "Problem activating this order";
			throw new Exception($msg);				
		}


	}

	public function deactivateOrder($order_id)
	{
		Log::debug("Deactivate Order : " . $order_id);
		$order = Order::find($order_id);
			if ($order->paid > 0 && count($order->active_items) > 0)
			{
				$msg = "You can't deactivate a paid order";
				throw new Exception($msg);				
			}
		
			if ($order->rosters->count())
				foreach($order->rosters as $roster)
					$roster->delete();

			if ($order->groups->count())
				foreach($order->groups as $group)
					$group->update(array('students' => 0, 'active' => 0));

			foreach($order->active_items as $item)
			{
				if ($item->course_instance_id)
				{
					// get the c urrent instance and the no show instance
					$ci = CourseInstance::find($item->course_instance_id);
				Log::debug("Deactivate Order Update Instance: " . $ci->id . ", current students: " . $ci->students . ", subtract students: " . $item->qty);
					$ci->students -= $item->qty;
					$ci->save();
					// update current instance qty
					//$ci->update(array('students' => ($ci->students - 1)));
				}
			}

			$order->update(array('status_id' => Utils::StatusId('Order', 'Cancelled')));

	}

	public function updateNoShow($roster_id)
	{
		$user = Sentry::getUser();
		$roster = Roster::find($roster_id);
		Log::debug("Update NoShow roster_id: " . $roster_id . ", order id: " . $roster->order_id);
		$old_item_id = $roster->item_id;
		$new_item = null;
		foreach($roster->order->active_items as $item)
		{
			if ($item->course_instance_id == $roster->course_instance_id) 
			{
				if ($item->qty > 1)
				{
					// create new item with qty and total updated
					$newqty = $item->qty - 1;
					$newTotal = $newqty * $item->price;
					$item_data = $item->toArray();
					$item_data['id'] = null;
					$item_data['qty'] = $newqty;
					$item_data['gst'] = $item->gst > 0 ? round($newTotal / 11, 2) : 0;
					$item_data['total'] = $newTotal;
					$new_item = Item::create($item_data);
				}
						
				// deactivate the current item
				$item->update(array('active' => 0));

				// get the current instance and the no show instance
				$ci = CourseInstance::find($item->course_instance_id);
				$ci_parent_location_id = $ci->location->parent_id == 0 ? $ci->location->id : $ci->location->parent_id;
				$ci_year = date('Y' , strtotime($ci->course_date));
				$ci_month = date('m' , strtotime($ci->course_date));
				$noshow_instance = CourseInstance::where('course_id', 9)
					->where('location_id', $ci_parent_location_id)
					->where(DB::raw('YEAR(course_date)'), '=', $ci_year)
					->where(DB::raw('MONTH(course_date)'), '=', $ci_month)
					->orderBy('course_date', 'desc')
					->first();
				//	
				//var_dump(\Utils::q());
				//exit();
				
				
				if (! $noshow_instance) {
					$msg = "No valid No Show instance for location '$ci->location_id' , month '$ci_month', please update";
					throw new Exception($msg);
				}	
						
				// now create a no show item
				$item_data = array(
					'order_id' => $roster->order_id,
					'course_instance_id' => $noshow_instance->id,
					'item_type_id' => 1,
					'description' => 'No Show for ' . $item->instance->course->name . ' location: ' . $item->instance->location->name . ' -- ' . $item->description,
					'qty' => 1,
					'price' => $item->price,
					'gst' => $item->gst > 0 ? round($item->price / 11, 2) : 0,
					'total' => $item->price,
					'user_id' => $user ? $user->id : null,
					'active' => 1
				);
				$noshow_item = Item::create($item_data);
				// update no show instance qty
				$noshow_instance->update(array('students' => ($noshow_instance->students + 1)));
				// update current instance qty
				$ci->students -= 1;
				$ci->save();
				Log::debug("Update NoShow Update Instance: " . $ci->id . ", current students: " . $ci->students);

				//$ci->update(array('students' => ($ci->students - 1)));
				break;
			}	
		}						
		//update roster		
		$notes = "NO-SHOW. " . $roster->notes_admin;
		$roster->update(array('item_id' => $noshow_item->id, 'course_instance_id' => $noshow_instance->id , 'notes_admin'=> $notes));
		
		if($new_item)
		{
			$rosters = Roster::where('item_id', $old_item_id)->get();
			foreach($rosters as $roster)
			{
				$roster->update(array('item_id' => $new_item->id));
			}
			
		}
		
	}

	public function deactivateRoster($roster_id)
	{
		$roster = Roster::find($roster_id);
		Log::debug("Deactivate Roster roster_id: " . $roster_id . ", order id: " . $roster->order_id);
		$order_id = $roster->order_id;
		if ($roster->order->paid >= $roster->order->total)
		{
			$msg = "You can't deactivate from a fully paid order";
			throw new Exception($msg);				
		}
		
		$old_item_id = $roster->item_id;
		$new_item = null;
		DB::transaction(function() use($roster, &$new_item)
		{
			foreach($roster->order->active_items as $item)
			{
				if ($item->course_instance_id == $roster->course_instance_id) 
				{
					if ($item->qty > 1)
					{
						// create new item with qty and total updated
						$newqty = $item->qty - 1;
						$newTotal = $newqty * $item->price;
						$item_data = $item->toArray();
						$item_data['id'] = null;
						$item_data['qty'] = $newqty;
						$item_data['gst'] = $item->gst > 0 ? round($newTotal / 11, 2) : 0;
						$item_data['total'] = $newTotal;
						$new_item = Item::create($item_data);
					}
						
					// deactivate the current item
					$item->update(array('active' => 0));

					// get the c urrent instance and the no show instance
					$ci = CourseInstance::find($item->course_instance_id);
					$ci->students -= 1;
					$ci->save();
					Log::debug("Deactivate Roster Update Instance: " . $ci->id . ", current students: " . $ci->students);
					// update current instance qty
					//$ci->update(array('students' => ($ci->students - 1)));
						
					break;
				}
			}
				
		});
		
		$roster->order->updateOrderTotal();	
							
		$roster->delete();
		
		if($new_item)
		{
			$rosters = Roster::where('item_id', $old_item_id)->get();
			foreach($rosters as $roster)
			{
				$roster->update(array('item_id' => $new_item->id));
			}
			
		}
		
	}


	public function updateOrderPayment($roster_id)
	{
		$roster = Roster::find($roster_id);
		// and we have a currentInvoice
		// create a credit note
		if ($roster->order->current_invoice)
		{		
			Utils::CreateCreditNote($roster->order, 'Updating Order Details');
		}
		
		$pay = 0;
		foreach($roster->order->active_items as $item)
		{
			if ($item->course_instance_id == $roster->course_instance_id) 
			{
				// get the amount to pay
				$pay = $item->price;
				break;
			}
		}
		$payment = array(
			'PaymentMethod' => 'CASH',
			'PaymentStatus' => true,
			'Backend' => 1,
			'GatewayResponse' => '',
			'TotalToPay' => $pay				
		);

		$payment_service = PaymentFactory::create($payment['PaymentMethod']);
		$payment_service->process($roster->order, $payment);
		
	}


	public function updateOpenOrders()
	{
		//$this->group_service = new BookingGroup;
		//$this->purchase_service = new BookingPurchase;
		$this->public_service = new BookingPublic;

		$results = array();
		$sql = "SELECT orders.id, paypalsessions.session_id 
				FROM  `orders` ,  `paypalsessions` 
				WHERE (comments = 'Submitted to Paypal' OR comments = 'Submited to Paypal') AND
				session_id IS NOT NULL AND
				session_content LIKE CONCAT(  '%\"OrderId\":', orders.id,  '%' )";

		$open_orders = DB::select($sql);
		foreach($open_orders as $order) 
		{
			$payment = $this->public_service->processOpenPayPalPurchase($order->session_id);		
			array_push($results, $payment);			
		}

		return $results;

	}


	public function processPayWayServerResponse($parameters)
	{
		Log::info("Start processPayWayServerResponse");
		$service = null;

		if(isset($parameters['payway_session_id']))
		{
			$order = Order::lockForUpdate()->find($parameters['payment_reference']);			
			switch ($order->order_type) {
				case 'Public':
					$service = new BookingPublic;
					break;
				case 'Group':
					$service = new BookingGroup;
					break;
				case 'Purchase':
					$service = new BookingPurchase;
					break;
				default:
					$service = null;
			}
			
			if($service)
				return $service->processPayWayResponse($order, $parameters);
			else
				return false;
					
		}	
		return false;	

	}




	// Helpers
	public function getBookingMessage($order_id)
	{
		$result = array();
		$order = Order::find($order_id);
		$vouchers = Voucher::where('order_id', $order_id)->lists('id');
		$message = '';
		if ($order && ($order->isPaid() || stripos($order->last_payment_method, 'Pay Later') !== false)  )
		{
			//$message .= "<p class='text-info'><b>The booking has been made.</b></p>";
            if($order->backend)
            {
                $message .= "<p class='text-info'>To make changes use booking ID: <a href='/backend/booking/search/". $order->id ."' style='color: black;'>" . $order->id . "</a></p>";
            }
            else
            {
                $message .= "<p class='text-info'>To make changes use booking ID: " . $order->id . ".</p>";
            }
			$message .= "<p class='text-info'>Invoice: <a href='/api/invoices/download/" . $order->current_invoice->id . "' target='_blank' style='color: black;'>Download</a></p>";
		}
		else if (!$order )
		{
			$message .= "<p class='text-warning'><b>There was a problem with this booking.</b></p>";
			$message .= "<p class='text-warning'>Please contact our nearest office and quote the Name used to make the booking.</p>";
		}
		else
		{
			$message .= "<p class='text-warning'><b>The booking has been made.</b></p>";
			$message .= "<p class='text-warning'><b>but there seems to be a problem with your payment.</b></p>";
			$message .= "<p class='text-warning'>Please contact our nearest office and quote your booking ID: " . $order->id . ".</p>";
			if( $order->current_invoice)
				$message .= "<p class='text-warning'>Invoice: <a href='/api/invoices/download/" . $order->current_invoice->id . "' target='_blank' style='color: black;'>Download</a></p>";
		}
		if (count($vouchers) > 0)
		{
			if($order->backend)
			{
				$message .= "<p class='text-info'>Voucher(s):</p> <ul>";
				foreach ($vouchers as $voucher_id) {
					$message .= "<li>Voucher: <a href='/api/vouchers/download/" . $voucher_id . "' target='_blank' style='color: black;'>Download</a></li>";
				}
				$message .= "</ul>";
			}
			else
			{
				$message .= "<p class='text-info'>Voucher(s):</p> <ul>";
				foreach ($vouchers as $voucher_id) {
					$message .= "<li>Voucher: " . $voucher_id . "</li>";
				}
				$message .= "</ul>";
				$message .= "<p class='text-info'>An email has been sent to your address, You will find attached the Gift Voucher(s) along with your invoice</p>";
			}
			$message .= "<p class='text-info'>Regards</p>";
			$message .= "<p class='text-info'>Coffee School</p>";
		}
		
		if($order)
		{
			$last_payment = $order->payments->last();
			$order->current_payment_method = $last_payment && $last_payment->method ? $last_payment->method->code : 'N/A';
		}

		$result = array('order' => $order, 'message' => $message);
		return $result;
	}


	
}