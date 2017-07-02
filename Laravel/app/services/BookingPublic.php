<?php namespace App\Services;

use DB, Courseinstance, Customer, Voucher, Log;
use App\Services\AbstractBooking;

class BookingPublic extends AbstractBooking
{

	protected $group_courses;

	public function __construct()
	{
		parent::__construct();
		
		$this->IsGroupBooking = false;
		$this->IsOnlineBooking = false;
		$this->IsPublicBooking = true;
		$this->IsProductPurchase = false;
		$this->OrderType = 'Public';
		if (!empty($this->booking['OrderType'])) {$this->OrderType = $this->booking['OrderType'];}
		
		if (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] != 'artisan')
		{
			$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=false&IsPublicBooking=true&IsOnlineBooking=false&IsProductPurchase=false';
			$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=false&IsPublicBooking=true&IsOnlineBooking=false&IsProductPurchase=false';
		}
	}

	public function initiatePayPalPurchase()
	{
		Log::info("Start Public Order creation on initiatePayPalPurchase");
		$that = $this;
		DB::transaction(function() use(&$that)
		{
            $that->getVoucher();
			$that->updateCourseInstance();
			$that->updateInvoiceCustomer();
			$that->createVouchers();
			$that->createOrder();
			$that->updateRoster();
		});
		Log::info("Order: " . $this->order->id . ", opening Paypal form now");
		return $this->order->id;

	}

	public function payWayPurchase()
	{
		Log::info("Start Order and TOKEN creation on initiate PayWayPurchase");
		
		$that = $this;
		DB::transaction(function() use(&$that)
			{
                    $that->getVoucher();
				$that->updateCourseInstance();
				$that->updateInvoiceCustomer();
				$that->createVouchers();
				$that->createOrder();
				$that->updateRoster();
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
		$current_instances = array();
		$selected_instances = array();
		if($this->order)
			foreach($this->order->active_items->toArray() as $item) 
				if (!empty($item['course_instance_id'])) 
					array_push($current_instances, $item['course_instance_id']);
		
		foreach($this->instances as $input)
			array_push($selected_instances, $input['courseInstance']);
		
		
		//find what needs to be done
		$instances_to_add    = array_diff($selected_instances, $current_instances);
		$instances_to_remove = array_diff($current_instances, $selected_instances);
		$instances_to_update  = array_intersect($current_instances, $selected_instances);

		$all_instances = array_merge($instances_to_add, $instances_to_remove, $instances_to_update);
		foreach($all_instances as $the_instance) 
		{	
			Log::debug("prcessing instance: " . $the_instance);

			$instance = Courseinstance::find($the_instance);
			
			foreach($instances_to_remove as $to_remove) 
			{	
				Log::debug("prcessing to remove insatnce: " . $to_remove);
				$match = array_first($this->order->active_items->toArray(), function($key, $value) use($to_remove)
				{
					return $value['course_instance_id'] == $to_remove;
				});
			
				if($match && $instance && $match['id'] == $instance->id)
				{
					$instance->students -= $match['qty'];
					Log::debug("match remove insatnce: " . $to_remove . ", new students total: " . $instance->students);

				}

			}
			
			//check if matchs a to add item
			foreach($instances_to_add as $to_add) 
			{	
				Log::debug("prcessing to add insatnce: " . $to_add);
				$match = array_first($this->instances, function($key, $value) use($to_add)
				{
						Log::debug("array first: " . json_encode($value));
						return isset($value['courseInstance']) && $value['courseInstance'] == $to_add;
				});
				
				if($match && $instance && $match['id'] == $instance->id && $match['isVoucher'] == false)
				{
					$instance->students += $match['studentQty'];
					Log::debug("match add insatnce: " . $to_add . ", new students total: " . $instance->students);
					$data = array('qty'=>$match['studentQty'], 'is_update' => true);
					if(!filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN)) {
						Log::debug("validate: " . $to_add . ", data: " . json_encode($data));
						$instance->validate($data);
					}

				}
			}				
			
			// check if match a to update item
			foreach($instances_to_update as $to_update) 
			{	
				Log::debug("prcessing to update insatnce: " . $to_update);
				$active = array_first($this->order->active_items->toArray(), function($key, $value) use($to_update)
					{
						return $value['course_instance_id'] == $to_update;
					});	
	
				$match = array_first($this->instances, function($key, $value) use($to_update)
				{
						return isset($value['courseInstance']) && $value['courseInstance'] == $to_update;
				});
				
				if($match && $instance && $match['id'] == $instance->id && $match['isVoucher'] == false)
				{
					
					if(!is_null($active))
						$instance->students -= $active['qty'];
					
					$instance->students += $match['studentQty'];
					Log::debug("match update insatnce: " . $to_update . ", new students total: " . $instance->students);
					$data = array('qty' => $match['studentQty'], 'is_update' => true);
					if(!filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN)) {
						Log::debug("validate: " . $to_update . ", data: " . json_encode($data));
						$instance->validate($data);
					}
				}
			}
			if($instance)
				$instance->save();
		}

		Log::debug("finish updateCourseInstance");
		
	}
	
	// Public Booking functions
	public function updateCourseInstance_old()
	{
		
		$match = null;
		$current_instances = array();
		$selected_instances = array();
		if($this->order)
			foreach($this->order->active_items->toArray() as $item) 
				if (!empty($item['course_instance_id'])) 
					array_push($current_instances, $item['course_instance_id']);
		
		foreach($this->instances as $input)
			array_push($selected_instances, $input['courseInstance']);
		
		
		//find what needs to be done
		$instances_to_add    = array_diff($selected_instances, $current_instances);
		$instances_to_remove = array_diff($current_instances, $selected_instances);
		$instances_to_update  = array_intersect($current_instances, $selected_instances);

		// run throught the items to delete and process
		foreach($instances_to_remove as $to_remove) 
		{	
			$match = array_first($this->order->active_items->toArray(), function($key, $value) use($to_remove)
				{
					return $value['course_instance_id'] == $to_remove;
				});
			
			if($match)
			{
				$instance = Courseinstance::find($to_remove);
				$new_total = $instance->students - $match['qty'];

				$data = array('qty'=>$match['qty'], 'new_total'=>$new_total, 'is_update' => true);
				if(filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN))
					$data['overrideValidation'] = true;
				
				$instance->save($data);
			}
		}
		
		// run through the new selection
		foreach($this->instances as $input) 
		{
			//check if matchs a to add item
			foreach($instances_to_add as $to_add) 
			{	
				if($input['courseInstance'] == $to_add && $input['isVoucher'] == false)
				{
					$instance = Courseinstance::find($to_add);
					$new_total = $instance->students + $input['studentQty'];
					
					$data = array('qty'=>$input['studentQty'], 'new_total'=>$new_total, 'is_update' => true);
					if(filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN))
						$data['overrideValidation'] = true;
					
					$instance->save($data);
					break;
				}
			}	
			// check if match a to update item
			foreach($instances_to_update as $to_update) 
			{	
				if($input['courseInstance'] == $to_update && $input['isVoucher'] == false)
				{
					$match = array_first($this->order->active_items->toArray(), function($key, $value) use($to_update)
						{
							return $value['course_instance_id'] == $to_update;
						});	
					$instance = Courseinstance::find($to_update);
					$qty = $input['studentQty'] - $match['qty'];
					$new_total = $instance->students - $match['qty'] + $input['studentQty'];
					
					$data = array('qty'=>$qty, 'new_total'=>$new_total, 'is_update' => true);
					if(filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN))
						$data['overrideValidation'] = true;
					
					$instance->save($data);
					break;
				}
			}
			
		}


		Log::debug("finish updateCourseInstance");
		
	}
	
	// Public Booking functions
	public function updateCourseInstance_old1()
	{
		
		$match = null;
		$current_instances = array();
		$selected_instances = array();
		if($this->order)
			foreach($this->order->active_items->toArray() as $item) 
				if (!empty($item['course_instance_id'])) 
					array_push($current_instances, $item['course_instance_id']);
		
		foreach($this->instances as $input)
			array_push($selected_instances, $input['courseInstance']);
		
		
		//find what needs to be done
		$instances_to_add    = array_diff($selected_instances, $current_instances);
		$instances_to_remove = array_diff($current_instances, $selected_instances);
		$instances_to_update  = array_intersect($current_instances, $selected_instances);

		// run throught the items to delete and process
		foreach($instances_to_remove as $to_remove) 
		{	
			$match = array_first($this->order->active_items->toArray(), function($key, $value) use($to_remove)
				{
					return $value['course_instance_id'] == $to_remove;
				});
			
			if($match)
			{
				$instance = Courseinstance::find($to_remove);
				$new_total = $instance->students - $match['qty'];

				$data = array('qty'=>$match['qty'], 'new_total'=>$new_total, 'is_update' => true);
				if(filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN))
					$data['overrideValidation'] = true;
				
				$instance->save($data);
			}
		}
		
		// run through the new selection
		foreach($this->instances as $input) 
		{
			//check if matchs a to add item
			foreach($instances_to_add as $to_add) 
			{	
				if($input['courseInstance'] == $to_add && $input['isVoucher'] == false)
				{
					$instance = Courseinstance::find($to_add);
					$new_total = $instance->students + $input['studentQty'];
					
					$data = array('qty'=>$input['studentQty'], 'new_total'=>$new_total, 'is_update' => true);
					if(filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN))
						$data['overrideValidation'] = true;
					
					$instance->save($data);
					break;
				}
			}	
			// check if match a to update item
			foreach($instances_to_update as $to_update) 
			{	
				if($input['courseInstance'] == $to_update && $input['isVoucher'] == false)
				{
					$match = array_first($this->order->active_items->toArray(), function($key, $value) use($to_update)
						{
							return $value['course_instance_id'] == $to_update;
						});	
					$instance = Courseinstance::find($to_update);
					$qty = $input['studentQty'] - $match['qty'];
					$new_total = $instance->students - $match['qty'] + $input['studentQty'];
					
					$data = array('qty'=>$qty, 'new_total'=>$new_total, 'is_update' => true);
					if(filter_var($this->payment['DoNotValidateCourseClass'] , FILTER_VALIDATE_BOOLEAN))
						$data['overrideValidation'] = true;
					
					$instance->save($data);
					break;
				}
			}
			
		}


		Log::debug("finish updateCourseInstance");
		
	}

	public function updateInvoiceCustomer()
	{
		$this->customer = Customer::where('first_name', $this->payment['FirstName'])
			->where('last_name', $this->payment['LastName'])
			->where('email', $this->payment['Email'])
			->first();

		$mail_out = isset($this->payment['mail_out']) ? filter_var($this->payment['mail_out'], FILTER_VALIDATE_BOOLEAN) : true;
		
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
				'mail_out_email' => $mail_out ? 1 : 0,
				'mail_out_sms' => $mail_out ? 1 : 0,
				'question1' => !empty($this->booking['q1']) ? $this->booking['q1'] : '',
				'question2' => !empty($this->booking['q2']) ? $this->booking['q2'] : '',
				'question3' => !empty($this->booking['q3']) ? $this->booking['q3'] : '',
				'lang_eng' => !empty($this->booking['lang_eng']) ? $this->booking['lang_eng'] : null,
				'lang_eng_level' => !empty($this->booking['lang_eng_level']) ? $this->booking['lang_eng_level'] : null,
				'active' => '1'
			);
			$this->customer = Customer::create($input);
		}
		else
		{
			$input = array(
				'dob' => !empty($this->payment['Dob']) ? $this->payment['Dob'] : null,
				'mobile' => $this->payment['Mobile'],
				'email' => $this->payment['Email'],
				'mail_out_email' => $mail_out ? 1 : 0,
				'mail_out_sms' => $mail_out ? 1 : 0,
				'question1' => !empty($this->booking['q1']) ? $this->booking['q1'] : '',
				'question2' => !empty($this->booking['q2']) ? $this->booking['q2'] : '',
				'question3' => !empty($this->booking['q3']) ? $this->booking['q3'] : '',
				'lang_eng' => !empty($this->booking['lang_eng']) ? $this->booking['lang_eng'] : null,
				'lang_eng_level' => !empty($this->booking['lang_eng_level']) ? $this->booking['lang_eng_level'] : null,
				'active' => '1'
				);
			$this->customer->update($input);
		}

		Log::debug("finish updateInvoiceCustomer");
		
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
		
		Log::debug("finish createVouchers");
		
	}
	
}