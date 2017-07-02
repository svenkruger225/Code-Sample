<?php namespace App\Services;

use DB, Courseinstance, Customer, GroupBooking, Log;
use App\Services\AbstractBooking;

class BookingGroup extends AbstractBooking
{
	protected $group;

	public function __construct()
	{
		parent::__construct();
		
		$this->IsGroupBooking = true;
		$this->IsOnlineBooking = false;
		$this->IsPublicBooking = false;
		$this->IsProductPurchase = false;
		$this->OrderType = 'Group';
		if (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] != 'artisan')
		{
			$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=true&IsPublicBooking=false&IsProductPurchase=false&IsOnlineBooking=false';
			$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=true&IsPublicBooking=false&IsProductPurchase=false&IsOnlineBooking=false';
		}	
	}

	public function initiatePayPalPurchase()
	{
		$that = $this;
		DB::transaction(function() use(&$that)
		{
			$that->updateGroupCustomer();
			$that->createGroupBooking();
			$that->createOrder();
			$that->updateRoster();
		});
		return $this->order->id;

	}

	public function payWayPurchase()
	{
		Log::info("Start Gourp Order and TOKEN creation on initiate PayWayPurchase");
		
		$that = $this;
		DB::transaction(function() use(&$that)
			{
				$that->updateGroupCustomer();
				$that->createGroupBooking();
				$that->createOrder();
				$that->updateRoster();
				$that->submitToPayWay();
			});
		Log::info("Order: " . $this->order->id . ", opening Payway form now");
		return array('id' => $this->order->id, 'token' => $this->payway_token, 'url' => $this->payway_url);
	}
	
	public function transactionalPurchase()
	{	
		Log::debug("start Group Booking transactionalPurchase");

		$that = $this;
		DB::transaction(function() use(&$that)
			{
				$that->updateGroupCustomer();
				$that->createGroupBooking();
				$that->createOrder();
				$that->updateRoster();
				$that->processPayment();

			});

	}
	
	public function updateGroupCustomer()
	{
		$this->customer = Customer::where('first_name', $this->payment['FirstName'])
			->where('last_name', $this->payment['LastName'])
			->where('email', $this->payment['Email'])
			->first();
		
		if (!$this->customer)
		{
			$input = array(
				'first_name' => $this->booking['FirstName'],
				'last_name' => $this->booking['LastName'],
				'phone' => $this->booking['Phone'],
				'mobile' => $this->booking['Mobile'],
				'email' => $this->booking['Email'],
				'country_of_birth' => 'AU',
				'islander_origin' => '0',
				'mail_out_email' => '1',
				'mail_out_sms' => '1',
				'active' => '1'
				);
			$this->customer = Customer::create($input);
		}
		else
		{
			$input = array(
				'phone' => $this->booking['Phone'],
				'mobile' => $this->booking['Mobile'],
				);
			$this->customer->update($input);
		}
		
	}
	
	public function createGroupBooking()
	{
		$to_delete = array();
		// if we already have an order
		if ($this->order && $this->order->groups->count() > 0)
		{
			foreach($this->order->groups as $group)
			{
				$found = false;
				foreach($this->instances as $inst) 
				{
					if ($group->id == $inst['groupId'])
					{
						$found = true;
						break;
					}
				}
				if(!$found)
					$to_delete[] = $group->id;		
			}
		}
		
		if (count($to_delete) > 0)
		{
			foreach($to_delete as $id) 
			{
				$group = GroupBooking::find($id);
				$group->update(array('active' => 0));			
			}			
		}		
		
		foreach($this->instances as &$input) 
		{
			$group = null;

			if(!empty($input['groupId']))
			{
				$group = GroupBooking::find($input['groupId']);			
			}

			if ($group)
			{
				$group_data = array(
					'course_id' => $input['courseType'],
					'location_id' => $input['location'],
					'customer_id' => $this->customer->id,
					'course_date' => $input['courseDate'],
					'time_start' => $input['time_start'],
					'time_end' => $input['time_end'],
					'students' => $input['studentQty'],
					'group_name' => $this->booking['GroupName'],
					'notes' => $this->booking['Notes'],
					'description' => $this->booking['Description'],
					'active' => '1'
					);

				$group->update($group_data);
			}
			else
			{
				$group_data = array(
					'id'=> null,
					'course_id' => $input['courseType'],
					'location_id' => $input['location'],
					'customer_id' => $this->customer->id,
					'course_date' => $input['courseDate'],
					'time_start' => $input['time_start'],
					'time_end' => $input['time_end'],
					'students' => $input['studentQty'],
					'group_name' => $this->booking['GroupName'],
					'notes' => $this->booking['Notes'],
					'description' => $this->booking['Description'],
					'active' => '1'
					);

				$group = GroupBooking::create($group_data);
			}
			$input['applyGst'] = $group->course->gst == '1' ? true : false;
			$input['groupId'] = $group->id;
			Log::debug("finish createGroupBooking [" . $input['groupId'] ."]");
			//Log::debug(json_encode($input));
			
		}	
	}
	
}