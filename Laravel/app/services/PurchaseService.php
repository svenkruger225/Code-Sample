<?php namespace App\Services;

use DB, Courseinstance, Customer, Purchase;
use App\Services\AbstractBooking;

class PurchaseService extends AbstractBooking
{

	protected $purchase;
	protected $products;
	

	public function __construct()
	{
	
		parent::__construct();
	
		$this->IsGroupBooking = false;
		$this->IsPublicBooking = false;
		$this->IsProductPurchase = true;
		$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=false&IsPublicBooking=false&IsProductPurchase=true';
		$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=false&IsPublicBooking=false&IsProductPurchase=true';
		
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
				'dob' => $this->booking['dob'],
				'mobile' => $this->booking['mobile'],
				'email' => $this->booking['email'],
				'phone' => $this->booking['phone'],
				'address' => $this->booking['address'],
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