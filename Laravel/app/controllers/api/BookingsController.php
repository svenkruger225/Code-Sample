<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Utils;
use Agent, Location, Course, CourseInstance, GroupBooking, Purchase, Order, Item, Invoice, Customer, DB, Status;
use BookingService, SearchService;

class BookingsController extends AdminController {
	
	public function getGroupBookingDetails($id) {

		$order = Order::find($id);
		$main_location = 0;
		
		//
		//dd(\Utils::q());
		//
		//exit;
		
		$invoice = array();
		if ($order && $order->current_invoice)
		{
			$invoice = array(
				'id'=> $order->current_invoice->id,
				'order_id' => $order->id,
				'invoice_date' => $order->current_invoice->invoice_date,
				'total' => $order->total,
				'paid' => $order->paid,
				'owing' => $order->owing
				);
		}
		
		$instances = array();
		if ($order && $order->group_booking_items->count())
		{
			foreach ($order->group_booking_items as $item)
			{
				if (!$item->active) 
					continue;
				
				$g_id = $item->group_booking_id;
				$group = GroupBooking::find($g_id);
				
				$notes_admin = '';
				$notes_class = '';
				$students = array();
				//$inst = GroupBooking::find($item->group_booking_id);
				if (Utils::ItemTypeName($item->item_type_id) == 'Course' )
				{
					$rosters = \Roster::with('customer')
						->where('order_id', $item->order_id)
						->where('group_booking_id',$group->id)
						->get();
					foreach ($rosters as $roster)
					{
						$student = array(
							'id'=> $roster->customer_id,
							'courseInstance' => $group->course_id,
							'FirstName' => $roster->customer->first_name,
							'LastName' => $roster->customer->last_name,
							'Dob' => $roster->customer->dob,
							'Phone' => $roster->customer->phone,
							'Mobile' => $roster->customer->mobile,
							'Email' => $roster->customer->email,
							'LangLevel' => !empty($roster->customer->lang_eng_level) ? $roster->customer->lang_eng_level : '.',
							'mail_out' => $roster->customer->mail_out_email
							);
						array_push($students, $student);
					}

					$instance = array(
						'id' => $item->id,
						'order_id' => $group->order_id,
						'courseType' => $group->course_id,
						'courseInstance' => $group->course_id,
						'groupId' => $item->group_booking_id,
						'itemType' => 'Course',
						'location_id' => $group->location_id,
						'courseName' => $group->course->name,
						'courseAddress' => $group->location->name,
						'courseDate' => date('Y-m-d', strtotime($group->course_date)),
						'time_start' => date('h:i A', strtotime($group->time_start)),
						'time_end' => date('h:i A', strtotime($group->time_end)),
						
						'studentQty' => $item->qty,
						'discount' => 0,
						'priceOffLine' => $item->price,
						'priceOnLine' => $item->price,
						'priceOff' => $item->price,
						'priceOn' => $item->price,
						'applyGst' => $group->course->gst == '1' ? true : false,
						'isPaid' =>  0, //$item->isPaid(),
						'feeRebook' => 0,
						'Students' => $students,
						'isVoucher' => false
						);
					array_push($instances, $instance);
				}
				else if(Utils::ItemTypeName($item->item_type_id) == 'RebookFee' )
				{
					foreach($instances as &$course)
					{
						if($course['groupId'] == $item->group_booking_id)
							$course['feeRebook'] = $item->price;	
					}
				}
				else if(Utils::ItemTypeName($item->item_type_id) == 'Discount' )
				{
					foreach($instances as &$course)
					{
						if($course['groupId'] == $item->group_booking_id)
							$course['discount'] = $item->price;	
					}
				}
			}
		}
		
		$payment = array();	
		if ($order && $order->customer)
		{
			$payment = array(
				'id'=> $order->customer->id,
				'FirstName'=> $order->customer->first_name,
				'LastName' => $order->customer->last_name,
				'Dob' => $order->customer->dob,
				'Mobile' => $order->customer->mobile,
				'Email' => $order->customer->email,
				'LangLevel' => !empty($order->customer->lang_eng_level) ? $order->customer->lang_eng_level : '.',
				'mail_out' => $order->customer->mail_out_email
				);
		}
		
		$result = array(
			'id' => $group->id,
			'order_id' => $group->order_id,
			'location_id' => $group->location_id,
			'group_name' => $group->group_name,
			'first_name' => $group->customer->first_name,
			'last_name' => $group->customer->last_name,
			'phone' => $group->customer->phone,
			'mobile' => $group->customer->mobile,
			'fax' => $group->customer->fax,
			'email' => $group->customer->email,
			'LangLevel' => !empty($group->customer->lang_eng_level) ? $group->customer->lang_eng_level : '.',
			'mail_out' => $group->customer->mail_out_email,
			'notes' => $group->notes,
			'description' => $group->description,
			'Instances' => $instances,
			'Payment' => $payment,
			'Invoice' => $invoice
			);
		
		if (!empty($order->agent_id))
			$result['agent'] = array('id'=>$order->agent->id, 'name'=>$order->agent->name);
		
		if (!empty($order->company_id))
			$result['company'] = array('id'=>$order->company->id, 'name'=>$order->company->name);

		//dd(\Utils::q());
		//
		//exit;
		
		echo json_encode($result);


	}
	
	public function getPurchaseDetails($id) {

		$order = Order::find($id);
		$main_location = 0;
		
		//
		//dd(\Utils::q());
		//
		//exit;
		
		$invoice = array();
		if ($order->current_invoice)
		{
			$invoice = array(
				'id'=> $order->current_invoice->id,
				'order_id' => $order->id,
				'invoice_date' => $order->current_invoice->invoice_date,
				'total' => $order->total,
				'paid' => $order->paid,
				'owing' => $order->owing
				);
		}
		
		$instances = array();
		if ($order->active_items->count())
		{
			$purchase = Purchase::find($order->purchase_id);
			foreach ($order->active_items as $item)
			{
				if (!$item->active) 
					continue;
				$instance = array(
					'id' => $item->product_id,
					'date_hire' => date('Y-m-d', strtotime($item->comments)),
					'qty' => $item->qty,
					'product_name' => $item->product ? $item->product->name : '',
					'product_description' => $item->product ? str_replace($item->product->name . ' ','',$item->description) : '',
					'price' => $item->price,
					);
				array_push($instances, $instance);
			}
		}
		
		$payment = array();	
		if ($order->customer)
		{
			$payment = array(
				'id'=> $order->customer->id,
				'first_name'=> $order->customer->first_name,
				'last_name' => $order->customer->last_name,
				'dob' => $order->customer->dob,
				'mobile' => $order->customer->mobile,
				'email' => $order->customer->email,
				);
		}
		
		$result = array(
			'id' => $purchase->id,
			'order_id' => $purchase->order_id,
			'location_id' => $purchase->location_id,
			'first_name'=> $order->customer->first_name,
			'last_name' => $order->customer->last_name,
			'dob' => $order->customer->dob,
			'mobile' => $order->customer->mobile,
			'email' => $order->customer->email,
			'phone' => $order->customer->phone,
			'address' => $order->customer->address,
			'city' => $order->customer->city,
			'state' => $order->customer->state,
			'post_code' => $order->customer->post_code,
			'notes' => $purchase->notes,
			'description' => $purchase->description,
			'Instances' => $instances,
			'Payment' => $payment,
			'Invoice' => $invoice
			);
		
		if (!empty($order->agent_id))
			$result['agent'] = array('id'=>$order->agent->id, 'name'=>$order->agent->name);
		
		if (!empty($order->company_id))
			$result['company'] = array('id'=>$order->company->id, 'name'=>$order->company->name);

		//dd(\Utils::q());
		//
		//exit;
		
		echo json_encode($result);


	}


}