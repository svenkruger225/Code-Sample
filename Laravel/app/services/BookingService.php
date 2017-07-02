<?php namespace App\Services;

use DB, Courseinstance, Customer, Voucher;
use App\Services\AbstractBooking;

class BookingService extends AbstractBooking
{

	protected $group_courses;

	public function __construct()
	{
		parent::__construct();
		
		$this->IsGroupBooking = false;
		$this->IsPublicBooking = true;
		$this->IsProductPurchase = false;
		$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=false&IsPublicBooking=true&IsProductPurchase=false';
		$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=false&IsPublicBooking=true&IsProductPurchase=false';
	}

	public function initiatePayPalPurchase()
	{
		$that = $this;
		DB::transaction(function() use(&$that)
		{
			$that->updateCourseInstance();
			$that->updateInvoiceCustomer();
			$that->createVouchers();
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
			$that->updateCourseInstance();
			$that->updateInvoiceCustomer();
			$that->createVouchers();
			$that->createOrder();
			$that->updateRoster();
			$that->processVoucher();
			$that->processPayment();
			$that->updateVouchers();
		});

	}
	
	// Public Booking functions
	public function updateCourseInstance()
	{
		$match = null;
		foreach($this->instances as $input) 
		{
			// if we have an order we try to find a match for the current instance
			if ($this->order)
				$match = array_first($this->order->active_items->toArray(), function($key, $value) use($input)
				{
					return $value['course_instance_id'] == $input['courseInstance'];
				});	
			
			// if we do not have a match 		
			if (!$match)
				$match['qty'] = 0;			
			
			if($input['isVoucher'] == false)
			{
				$instance = Courseinstance::find($input['courseInstance']);
				$qty = $input['studentQty'] - $match['qty'];
				$new_total = $instance->students + $input['studentQty'] - $match['qty'];
				
				$instance->save(array('qty'=>$qty, 'new_total'=>$new_total));

			}
			
		}
	
	}
		
	public function updateInvoiceCustomer()
	{
		$this->customer = Customer::where('first_name', $this->payment['FirstName'])
			->where('last_name', $this->payment['LastName'])
			->where('email', $this->payment['Email'])
			->first();
		
		if (!$this->customer)
		{
			$input = array(
				'first_name' => $this->payment['FirstName'],
				'last_name' => $this->payment['LastName'],
				'dob' => !empty($this->payment['Dob']) ? $this->payment['Dob'] : null,
				'mobile' => $this->payment['Mobile'],
				'email' => $this->payment['Email'],
				'country_of_birth' => 'AU',
				'islander_origin' => '0',
				'mail_out_email' => '1',
				'mail_out_sms' => '1',
				'question1' => $this->booking['q1'] ? $this->booking['q1'] : '',
				'question2' => $this->booking['q2'] ? $this->booking['q2'] : '',
				'question3' => $this->booking['q3'] ? $this->booking['q3'] : '',
				'active' => '1'
			);
			$this->customer = Customer::create($input);
		}
				
	}
	
	public function createVouchers()
	{

		foreach($this->instances as &$input) 
		{
			if($input['isVoucher'])
			{
				$input['voucherId'] = '';
				$input['courseInstance'] = null;
				$voucher_data = array(
					'id'=> null,
					'customer_id' => $this->customer->id,
					'course_id' => $input['courseType'],
					'location_id' => $input['parentLocation'],
					'expiry_date' => date('Y-m-d', strtotime('+1 years')),
					'status_id' => Utils::StatusId('Voucher', 'Valid'),
					'active' => 0
					);
				$qty = $input['studentQty'];
				$vouchers_array = array();

				if ($this->order && $this->order->vouchers->count())
				{
					foreach($this->order->vouchers as $voucher) 
					{
						if ($voucher->location_id == $voucher_data['location_id'] && 
							$voucher->course_id == $voucher_data['course_id'] &&
							!$voucher->isCancelled()
						)
						{
							$qty = $qty - 1;
							if ($qty < 0)
								$voucher->update(array('status_id' => Utils::StatusId('Voucher', 'Cancelled'), 'active'=> 0));
							else
								array_push($vouchers_array,$voucher->id); 						
						}
					}	
				}

				for($i = 0; $i < $qty; $i++)
				{
					$voucher = Voucher::create($voucher_data);	
					array_push($vouchers_array,$voucher->id); 
				}
				$input['vouchersIds'] = $vouchers_array;
			}						
				
		}
		
	}
	
}