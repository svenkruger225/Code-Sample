<?php namespace App\Services;

use DB, Courseinstance, Customer, Voucher, Log, Sentry,Order, OnlineRoster, Roster, Certificate;
use App\Services\AbstractBooking;

class BookingOnline extends AbstractBooking
{

	protected $group_courses;

	public function __construct()
	{
		parent::__construct();
		
		$this->IsGroupBooking = false;
		$this->IsOnlineBooking = true;
		$this->IsPublicBooking = false;
		$this->IsProductPurchase = false;
		$this->OrderType = 'PublicOnline';
		if (!empty($this->booking['OrderType'])) {$this->OrderType = $this->booking['OrderType'];}
		
		if (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] != 'artisan')
		{
			$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=false&IsOnlineBooking=true&IsPublicBooking=false&IsProductPurchase=false';
			$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=false&IsOnlineBooking=true&IsPublicBooking=fals&IsProductPurchase=false';
		}
	}

	public function initiatePayPalPurchase()
	{
		Log::info("Start online Order creation on initiatePayPalPurchase");
		
		$that = $this;
		DB::transaction(function() use(&$that)
			{
				$that->updateCustomer();
				$that->updateCustomerUser();
				$that->createOrder();
				$that->updateOnlineRoster();
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
				$that->updateCustomer();
				$that->updateCustomerUser();
				$that->createOrder();
				$that->updateOnlineRoster();
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
				$that->updateCustomer();
				$that->updateCustomerUser();
				$that->createOrder();
				$that->updateOnlineRoster();
				$that->processPayment();
			});

	}
	
	// Online Booking functions

	public function updateOnlineRoster() 
	{
		$has_online_course = false;
		$has_facetoface_course = false;
		foreach($this->instances as $instance) 
		{			
			if(array_key_exists('itemType', $instance))
			{
				if ($instance['itemType'] == 'OnlineCourse')
				{
					$has_online_course = true;
				}
				elseif ($instance['itemType'] == 'OnlineFaceToFace')
				{
					$has_facetoface_course = true;
				}
			}
		}	
		$existing_online_rosters = array();
		if ($has_online_course) {	
			if ($this->order->onlinerosters->count())
			{
				$existing_online_rosters = $this->order->onlinerosters->toArray();
				foreach($this->order->onlinerosters as $roster)
				{
					$roster->delete();
				}
			}
		}
		
		$existing_facetoface_rosters = array();
		if ($has_facetoface_course) {
			if ($this->order->rosters->count())
			{
				$existing_facetoface_rosters = $this->order->rosters->toArray();
				foreach($this->order->rosters as $roster)
				{
					$roster->delete();
				}
			}
		}
		
		//Log::debug("online roster " . json_encode($existing_online_rosters));
		//Log::debug("facetoface roster " . json_encode($existing_facetoface_rosters));
		
		$mail_out = isset($this->payment['mail_out']) ? filter_var($this->payment['mail_out'], FILTER_VALIDATE_BOOLEAN) : true;

		// There is only one student so lets get its data from the first course instance		
		$input = $this->instances[0]['Students'][0]; // there is only one student
		
		// for Online courses the Invoice Customer must be the same as STudent customer
		if ($this->customer) 
		{
			$customer = $this->customer;
		}
		else 
		{
			$customer = Customer::where('first_name', $input['FirstName'])->where('last_name', $input['LastName'])->where('email', $input['Email'])->first(array('id'));		
		}	
		

		if (!$customer)
		{
			$cust_data = array(
				'first_name' => $input['FirstName'],
				'last_name' => $input['LastName'],
				'dob' => !empty($input['Dob']) ? $input['Dob'] : null,
				'mobile' => $input['Mobile'],
				'email' => $input['Email'],
				'country_of_birth' => 'AU',
				'islander_origin' => '0',
				'mail_out_email' => $mail_out ? 1 : 0,
				'mail_out_sms' => $mail_out ? 1 : 0,
				'lang_eng' => !empty($input['LangEng']) ? $input['LangEng'] : null,
				'lang_eng_level' => !empty($input['LangLevel']) ? $input['LangLevel'] : null,
				'active' => '1'
				);
			$customer = Customer::create($cust_data);
		}
		else
		{
			if (!empty($input['Dob']))
			$customer->dob = $input['Dob'];
			if (!empty($input['Mobile']))
			$customer->mobile = $input['Mobile'];
			if (!empty($input['Email']))
			$customer->email = $input['Email'];
			
			if (!empty($input['LangEng']))
			$customer->lang_eng = $input['LangEng'];
			if (!empty($input['LangLevel']))
			$customer->lang_eng_level = $input['LangLevel'];
			
			$customer->mail_out_email = $mail_out ? 1 : 0;
			$customer->mail_out_sms = $mail_out ? 1 : 0;
			$customer->active = 1;
			
			$customer->update();
		}

		$input['certificate_id'] = null;
		$input['notes_admin'] = '';
		$input['notes_class'] = '';

		foreach($this->instances as $instance) 
		{			
			if(array_key_exists('isVoucher', $instance) && $instance['isVoucher'] == false)
			{
				//Log::debug("current instance " . json_encode($instance));
				// not voucher create roster			
				$input = $instance['Students'][0]; // there is only one student
				$input['certificate_id'] = null;
				$input['notes_admin'] = '';
				$input['notes_class'] = '';
				
				//Update Online Rosters
				if ($has_online_course && $instance['itemType'] == 'OnlineCourse') 
				{	
					//Log::debug("online instance " . json_encode($instance));
					foreach ($existing_online_rosters as $e_roster)
					{				
						if ($e_roster['customer_id'] == $customer->id && ((isset($e_roster['course_id']) && $e_roster['course_id'] == $input['courseInstance']) ))
						{
							$input['certificate_id'] = $e_roster['certificate_id'];
							$input['notes_admin'] = $e_roster['notes_admin'];
							$input['notes_class'] = $e_roster['notes_class'];
							break;
						}	
					};
					
					$roster_data = array(
						'id'=> null,
						'order_id' => $this->order->id,
						'course_id' => $instance['courseInstance'],
						'customer_id' => $customer->id,
						'item_id' => !empty($instance['item_id']) ? $instance['item_id'] : null,
						'certificate_id' => $input['certificate_id'],
						//'description' => $input['description'],
						'notes_admin' => $instance['notesAdmin'] . $input['notes_admin'],
						'notes_class' => $instance['notesClass'] . $input['notes_class']
						);
					
					//Log::debug("creating a roster [" . $this->order->id ."], group: [" . $instance['groupId'] . "]");

					$roster = OnlineRoster::create($roster_data);
					
					if(!empty($roster_data['certificate_id']))
					{
						$certificate = Certificate::find($roster_data['certificate_id']);
						$certificate->update(array('roster_id' => $roster->id));			
					}
				}

				
				//Update FaceToFace Rosters
				if ($has_facetoface_course && $instance['itemType'] == 'OnlineFaceToFace') 
				{	
					//Log::debug("facetoface instance " . json_encode($instance));
					foreach ($existing_facetoface_rosters as $e_roster)
					{
						
						if ($e_roster['customer_id'] == $customer->id && (
							isset($e_roster['course_instance_id']) && $e_roster['course_instance_id'] == $input['courseInstance']))
						{
							$input['certificate_id'] = $e_roster['certificate_id'];
							$input['notes_admin'] = $e_roster['notes_admin'];
							$input['notes_class'] = $e_roster['notes_class'];
							break;
						}

					};
					
					$roster_data = array(
						'id'=> null,
						'order_id' => $this->order->id,
						'item_id' => !empty($instance['item_id']) ? $instance['item_id'] : null,
						'course_instance_id' => $instance['courseInstance'],
						'group_booking_id' => null,
						'customer_id' => $customer->id,
						'certificate_id' => $input['certificate_id'],
						//'description' => $input['description'],
						'notes_admin' => $instance['notesAdmin'] . $input['notes_admin'],
						'notes_class' => $instance['notesClass'] . $input['notes_class']
						);
					
					//Log::debug("creating a roster [" . $this->order->id ."], group: [" . $instance['groupId'] . "]");

					$roster = Roster::create($roster_data);
					
					if(!empty($roster_data['certificate_id']))
					{
						$certificate = Certificate::find($roster_data['certificate_id']);
						$certificate->update(array('roster_id' => $roster->id));			
					}

				}
					
			}

		}
		
		//$a = 10 / 0;
		
		//reload the order
		$this->order = Order::find($this->order->id);
		
		Log::debug("finish updateOnlineRosters [" . $this->order->id ."]");
		
	}
	
	public function updateCustomer()
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
				'lang_eng' => !empty($this->booking['lang_eng']) ? $this->booking['lang_eng'] : null,
				'lang_eng_level' => !empty($this->booking['lang_eng_level']) ? $this->booking['lang_eng_level'] : null,
				'active' => '1'
				);
			$this->customer->update($input);
		}

		Log::debug("finish updateInvoiceCustomer");
		
	}
	
	public function updateCustomerUser()
	{
		if (empty($this->customer->user_id))
		{
			$inputs = array(
				'first_name' => $this->payment['FirstName'],
				'last_name' => $this->payment['LastName'],
				'dob' => !empty($this->payment['Dob']) ? $this->payment['Dob'] : null,
				'mobile' => $this->payment['Mobile'],
				'email' => $this->payment['Email'],
				'username' => $this->payment['Email'],
				'password' => $this->payment['Password'],
				'activated' => 1,
				'permissions' => array(	"superuser"=>"0","admin"=>"0","user"=>"0","student"=>"1"),
			);

			Sentry::getUserProvider()->getEmptyUser()->setLoginAttributeName('username');
			if ($user = Sentry::getUserProvider()->create($inputs))
			{
				$group = Sentry::findGroupByName('Students');
				$user->addGroup($group);
					
				//update customer
				$this->customer->user_id = $user->id;
				$this->customer->save();				
			}
			else
			{
				Log::error("Error creating user");
				throw new Exception("Error creating user");
			}		
		}
		else
		{
			Log::debug("Customer already have user id [" . $this->customer->user_id . "]");
		}
		
		Log::debug("finish updateCustomerUser");
		
	}
	
}