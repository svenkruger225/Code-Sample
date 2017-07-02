<?php namespace App\Services;

use DB, Courseinstance, Customer, GroupBooking;
use App\Services\AbstractBooking;

class GroupBookingService extends AbstractBooking
{
	protected $group;

	public function __construct()
	{
		parent::__construct();
		
		$this->IsGroupBooking = true;
		$this->IsPublicBooking = false;
		$this->IsProductPurchase = false;
		$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=true&IsPublicBooking=false&IsProductPurchase=false';
		$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=true&IsPublicBooking=false&IsProductPurchase=false';
	
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
	
	public function transactionalPurchase()
	{	
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
		
	}
	
	public function createGroupBooking()
	{
		$match = null;
		foreach($this->instances as &$input) 
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

			$this->group = GroupBooking::create($group_data);
			$input['groupId'] = $this->group->id;
			
		}	
	}
	
}