<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, stdClass, Log;
use Customer, DateTime, Queue;

class CustomerService {

	public function __construct()
	{
		Input::merge(\Utils::array_map_recursive("trim", Input::all(), array('first_name','last_name','email', 'FirstName','LastName','Email')));
	}

	public function CreateUpdateCustomer($data = null)
	{
		if (is_null($data))
			$data = Input::all();
				
		$customer = null;
		$roster = null;
		$old_customer_id = null;
		
		if (!empty($data['customer_id']))
		{
			\Log::debug('initial customer_id: ' . $data['customer_id']);
			$customer = \Customer::find($data['customer_id']);
			if ($customer)
				$old_customer_id = $customer->id;
		}
		
		if (!empty($data['roster_id']))
		{
			\Log::debug('initial roster_id: ' . $data['roster_id']);
			$roster = \Roster::find($data['roster_id']);
			if (!$roster)
			{
				\Log::debug('try online roster: ' . $data['roster_id']);
				$roster = \OnlineRoster::find($data['roster_id']);
			}
		}
		
		$keys = array('disabilities', 'achievements');
		$input = \Utils::GetInput(null, array(), $data, array('_method','usi','customer_id','roster_id',
			'certificate_date','location_id','course_id','origin_selection','DvsDocumentType','CertificateNumber','DatePrinted',
			'RegistrationDate','RegistrationNumber','RegistrationState','RegistrationYear','DocumentNumber',
			'LicenceNumber','LicenceState','DvsDocument','verify_usi','is_course_accredited',
			'CountryOfIssue','PassportNumber','NameLine1','NameLine2','NameLine3','NameLine4','CardColour',
			'ExpiryDate','ExpiryDay','ExpiryMonth','ExpiryYear','IndividualRefNumber','MedicareCardNumber','ImmiCardNumber','AcquisitionDate',
			'StockNumber',"errors","IsValid"));
	
		$input['first_name'] = trim($input['first_name']);
		$input['last_name'] = trim($input['last_name']);
		$input['email'] = trim($input['email']);
		
		
		if(!empty($input['disabilities_other']))
			array_push($input['disabilities'], 'other__' . $input['disabilities_other'] );
		
		foreach($keys as $key)
			if(isset($input[$key]))
				$input[$key] = count($input[$key]) > 0 ? json_encode($input[$key]) : '';
		
		if (empty($input['lang_other']))
			$input['lang_other'] = '@@@@';
		
		if (empty($input['disability']))
			$input['disability'] = 0;

		unset($input['other_disabilities']);
		unset($input['disabilities_other']);

		if (empty($input['origin']))                      {$input['islander_origin'] = 0;}
		elseif ($input['origin'] == 'Aboriginal')         {$input['islander_origin'] = 0;}
		elseif ($input['origin'] == 'Islander')           {$input['islander_origin'] = 1;}
		elseif ($input['origin'] == 'AboriginalIslander') {$input['islander_origin'] = 1;}
		else                                              {$input['origin'] = ''; $input['islander_origin'] = 0;	}
		
		//if(!empty($input['origin']))
		//	$input['islander_origin'] = null;

		if(!isset($input['active']))
			$input['active'] = 1;

		$input['avetmiss_done'] = 1;

		Log::info(json_encode($input));
		
		$existing_customer_for_input = \Customer::where('first_name', 'LIKE', $input['first_name'] . '%')
			->where('last_name', $input['last_name'])
			->where('email', $input['email'])
			->orderBy('id','desc')
			->first();

		// ok 
		//if there is a customer and 
		//the customer first_name is different than the input OR 
		//the customer last name is different the input OR 
		//the customer email is different tha the input
		// or IF none of the above but the existing customer has usi verified or avetmiss done
		// then we use the existing
		
		if ($existing_customer_for_input)
		{
			\Log::debug('existing_customer_for_input: ' . $existing_customer_for_input->id);

			if ($customer && ($customer->first_name != $input['first_name'] && stristr($customer->first_name, $input['first_name']) === FALSE) ) 
			{
				\Log::debug('different first_name: ' . $customer->id . 'existing_customer_for_input: ' . $existing_customer_for_input->id);
				$customer = $existing_customer_for_input;
			}
			else if ($customer && $customer->last_name != $input['last_name']) 
			{
				\Log::debug('different last_name: ' . $customer->id . 'existing_customer_for_input: ' . $existing_customer_for_input->id);
				$customer = $existing_customer_for_input;
			}	
			else if ($customer && $customer->email != $input['email'] )
			{
				\Log::debug('different email: ' . $customer->id . 'existing_customer_for_input: ' . $existing_customer_for_input->id);
				$customer = $existing_customer_for_input;
			}	
			else if ( !$customer )
			{
				\Log::debug('no customer - existing_customer_for_input: ' . $existing_customer_for_input->id);
				$customer = $existing_customer_for_input;
			}	
			else if ($existing_customer_for_input->usi_verified || $existing_customer_for_input->avetmiss_done )
			{
				\Log::debug('existing_customer_for_input has usi or avetmiss: ' . $existing_customer_for_input->id);
				$customer = $existing_customer_for_input;
			}
		}	
		else
		{
			\Log::debug('NO existing_customer_for_input');
			if ($customer && ($customer->first_name != $input['first_name'] && stristr($customer->first_name, $input['first_name']) === FALSE) ) 
			{
				\Log::debug('different first_name: ' . $customer->id . 'first_name: ' . $input['first_name']);
				$customer = null; 
			}
			else if ($customer && $customer->last_name != $input['last_name']) 
			{
				\Log::debug('different last_name: ' . $customer->id . 'last_name: ' . $input['last_name']);
				$customer = null; 
			}	
			else if ($customer && $customer->email != $input['email'] )
			{
				\Log::debug('different email: ' . $customer->id . 'email: ' . $input['email']);
				$customer = null; 
			}
		}		

		if($customer)
		{
			\Log::debug('update customer: ' . $customer->id );
			$customer->update($input);
		}
		else
		{
			\Log::debug('create customer');
			$validation = Validator::make($input, \Customer::$rules);

			if ($validation->passes())
			{
				$customer = \Customer::create($input);
			}
			else
			{
				throw new Exception('Problem updating customer:<br>' . implode('<br>',$validation->errors()->getMessageBag()->all()));
			}
		}
		
		if (is_null($customer))
			throw new Exception("Failed to create/update customer");
		
		\Log::debug('FINAL CUSTOMER: ' . $customer->toJson());
		
		$rosters = \Roster::where('customer_id', $old_customer_id)->get();
		if (!$rosters->count()) 
		{
			\Log::debug('Check for online rosters: ' . $rosters->count());
			$rosters = \OnlineRoster::where('customer_id', $old_customer_id)->get();
		}
		
		if ($rosters->count()) 
		{
			\Log::debug('Updating rosters: ' . $rosters->count());
			foreach($rosters as $roster)
			{
				\Log::debug('Updating roster: ' . $roster->id);
				$roster->update(array('customer_id' => $customer->id));
			}
		}
		elseif ($roster)
		{
			\Log::debug('Updating single roster: ' . $roster->id);
			$roster->update(array('customer_id' => $customer->id));
		}
		
		return $customer;

	}

	public function GetStudentsWithAvetmissUsiMissing($start_date = null, $end_date = null)
	{

		if (is_null($start_date))
		$start_date = new DateTime('tomorrow');
		
		if (is_null($end_date))
		{
			$end_date = clone $start_date;
		}
		
		$sql = "SELECT DISTINCT ci.id as course_instance_id, ci.course_date, ci.time_start, ci.time_end, ci.course_id, co.name as course_name, r.id as roster_id, r.customer_id, 
				CONCAT(c.first_name, ' ', c.last_name) as full_name, c.dob, c.email, c.mobile, c.unique_student_identifier as usi, c.usi_verified, c.avetmiss_done
				FROM rosters r
				JOIN customers c ON r.customer_id = c.id
				JOIN courseinstances ci ON r.course_instance_id = ci.id
				JOIN courses co ON ci.course_id = co.id
				where IFNULL(co.certificate_code,'') != '' AND
				ci.course_date between '" . $start_date->format('Y-m-d') . "' AND '" . $end_date->format('Y-m-d') . "' AND 
				ci.active = 1 AND c.avetmiss_done = 0
				order by ci.course_date, co.order, c.first_name, c.last_name;";
				
		$students = DB::select( $sql );

		Log::info(sprintf("Students: %s", count($students)));
		
		$server_results = array();
		$previous_name = "";
		$r_id = "";
		$course = array();
		foreach($students as $student)
		{
			if ($previous_name != $student->course_name)
			{
				if ($previous_name != '')
					$server_results = array_add($server_results, $student->roster_id, $course);
				
				$course['course_name'] = sprintf("%s  %s  %s : %s", $student->course_name, date('M-d-Y (D)', strtotime($student->course_date)),$student->time_start,$student->time_end);
				$course['messages'] = array();
			}
			$previous_name = $student->course_name;

			$data['customer_id'] = $student->customer_id;
			$data['roster_id'] = $student->roster_id;
			$data['type'] = 'EnrolmentReminder';
			Queue::push('UsiQueue', $data);

			$data['type'] = 'EnrolmentReminderSms';
			Queue::push('UsiQueue', $data);
			
			array_push($course['messages'], sprintf("Customer: %s, Mobile: %s, Email Id: %s, USI: %s, Avetmiss: %s", $student->full_name, $student->mobile, $student->email, $student->usi_verified, $student->avetmiss_done));
			$r_id = $student->roster_id;
		}
		$server_results = array_add($server_results, $r_id, $course);
		
		return $server_results;
	}	
	
	private function CanVerifyUsi($student)
	{
		return ( !$student->usi_verified && !empty($student->first_name) && !empty($student->last_name) && !empty($student->dob) && !empty($student->usi));
	}

}

