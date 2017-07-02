<?php namespace App\Services;

use DB, Courseinstance, Customer, Purchase, Log;
use App\Services\AbstractBooking;

class BookingPurchase extends AbstractBooking
{

	protected $purchase;
	protected $products;
	

	public function __construct()
	{
	
		parent::__construct();
	
		$this->IsGroupBooking = false;
		$this->IsOnlineBooking = false;
		$this->IsPublicBooking = false;
		$this->IsProductPurchase = true;
		$this->OrderType = 'Purchase';
		if (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] != 'artisan')
		{
			$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=false&IsPublicBooking=false&IsProductPurchase=true&IsOnlineBooking=false';
			$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=false&IsPublicBooking=false&IsProductPurchase=true&IsOnlineBooking=false';
		}
	}

	public function initiatePayPalPurchase()
	{
		$that = $this;
		DB::transaction(function() use(&$that)
		{
			$that->updatePurchaseCustomer();
			$that->createPurchase();
			$that->createOrder();
		});
		return $this->order->id;

	}

	public function payWayPurchase()
	{
		Log::info("Start Purchase Order and TOKEN creation on initiate PayWayPurchase");
		
		$that = $this;
		DB::transaction(function() use(&$that)
			{
				$that->updatePurchaseCustomer();
				$that->createPurchase();
				$that->createOrder();
				$that->submitToPayWay();
			});
		Log::info("Order: " . $this->order->id . ", opening Payway form now");
		return array('id' => $this->order->id, 'token' => $this->payway_token, 'url' => $this->payway_url);
	}

	public function transactionalPurchase()
	{	
		$that = $this;
		DB::transaction(function() use(&$that)
			{
				$that->updatePurchaseCustomer();
				$that->createPurchase();
				$that->createOrder();
				$that->processPayment();

			});
	}
	
	public function updatePurchaseCustomer()
	{
		$this->customer = Customer::where('first_name', $this->booking['first_name'])
			->where('last_name', $this->booking['last_name'])
			->where('email', $this->booking['email'])
			->first();

		$input = array(
			'first_name' => $this->booking['first_name'],
			'last_name' => $this->booking['last_name'],
			'dob' => empty($this->booking['dob']) ? null : $this->booking['dob'],
			'mobile' => $this->booking['mobile'],
			'email' => $this->booking['email'],
			'phone' => $this->booking['phone'],
			'address_building_name' => $this->booking['address_building_name'],
            'address_unit_details' => $this->booking['address_unit_details'],
            'address_street_number' => $this->booking['address_street_number'],
			'address_street_name' => $this->booking['address_street_name'],
			'city' => $this->booking['city'],
			'state' => $this->booking['state'],
			'post_code' => $this->booking['post_code'],
			'country_of_birth' => 'AU',
			'islander_origin' => '0',
			'mail_out_email' => '1',
			'mail_out_sms' => '1',
			'active' => '1'
		);
		
		if (!$this->customer)
		{
			$this->customer = Customer::create($input);
		}
		else
		{
			$this->customer->update($input);
		}
		
	}

	public function createPurchase()
	{
		$match = null;
		foreach($this->instances as &$input) 
		{
			
			$purchase_data = array(
				'id'=> null,
				'location_id' => $input['location'],
				'customer_id' => $this->customer->id,
				'notes' => $this->booking['Notes'],
				'description' => $this->booking['Description'],
				'active' => '1'
				);

			$purchase = Purchase::create($purchase_data);
			$this->booking['purchaseId'] = $purchase->id;
			
		}	
	}
	
	
}