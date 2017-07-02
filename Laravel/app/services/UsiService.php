<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, DB, Response, Exception, stdClass, Utils, Log;
use UsiBulkCreate, UsiCreate, UsiBulkVerify, UsiVerify, UsiDocument, Queue;
use App\Services\Curl;

class UsiService {

	public $input;
	public $bulk;
	public function __construct()
	{
		$config = Config::get('usi', array());
		$this->api_server = isset($config['serverUrl']) ? $config['serverUrl'] : "http://usidev.coffeeschool.com.au/";
		$this->curl = new Curl;
		Input::merge(\Utils::array_map_recursive("trim", Input::all(), array('first_name','last_name','email', 'FirstName','LastName','Email')));
		$this->input = Input::all();
		$this->bulk = Input::get('bulk', array());
	}

	public function BulkCreateUsi()
	{
		$response = array();
		
		return $response;
	}

	public function CreateUsi()
	{
		$response = array();
		
		$customer_id = Input::get('customer_id', null);
		$this->customer_id = $customer_id;
		$this->roster_id = Input::get('roster_id', null);
		
		// next 3 lines for no USI
		$this->customer = \CustomerService::CreateUpdateCustomer();	
		$this->ProcessCreateNoUSIResponse();
		return array(
			'success' => true,
			'Message' => 'Enrolment Data succesfully updated'
			);
		
		/*
		
		$this->usi_create = UsiCreate::where(function ($query) use($customer_id){
				$query->where("customer_id",$customer_id)
				->orWhereNull('customer_id');
			})
			->where("first_name",Input::get('first_name', null))
			->where("last_name",Input::get('last_name', null))
			->where("dob",Input::get('dob', null))
			->first();
			
		if(!is_null($this->usi_create) && 
			(Utils::StatusName("Usi", $this->usi_create->status_id) == 'Open' || 
			Utils::StatusName("Usi", $this->usi_create->status_id) == 'Failure' || 
			Utils::StatusName("Usi", $this->usi_create->status_id) == 'Invalid') )
		{
			$bulk = UsiBulkCreate::find($this->usi_create->usi_bulk_create_id);	
				
			if($bulk)
				$bulk->delete();
				
			$this->usi_create->delete();
				
			$this->usi_create = null;
		}
		
		if(is_null($this->usi_create) )
		{
			$this->usi_bulk_create = UsiBulkCreate::create(
				array(
						'organisation_code' => '970582',
						'request_id' => 0,
						'receipt_number' => '',
						'response' => '',
						'number_of_applications' => 1
						)
					);
		
			$customer = \CustomerService::CreateUpdateCustomer();	
		
			$this->usi_create = UsiCreate::create(
				array(
						'usi_bulk_create_id' => $this->usi_bulk_create->id,
						'application_id' => 1,
						'customer_id' => $customer->id,
						'first_name' => Input::get('first_name', null),
						'middle_name' => Input::get('middle_name', null),
						'last_name' => Input::get('last_name', null),
						'dob' => Input::get('dob', null),
						'gender' => Input::get('gender', null),

						'email_address' => Input::get('email', null),
						'phone_number' => Input::get('phone', null),
						'mobile_number' => Input::get('mobile', null),

						'address' => $this->GetAddress1() . ', ' . $this->GetAddress2(),
						'city' => Input::get('city', null),
						'state' => Input::get('state', null),
						'post_code' => Input::get('post_code', null),

						'town_city_of_birth' => Input::get('city_of_birth', null),
						'country_of_birth' => Input::get('country_of_birth', 'Australia'),
						'country_of_residence' => Input::get('country_of_residence', 'Australia'),
						'country_study_in' => 'Australia',

						'preferred_method' => Input::get('preferred_method', null),

						'status_id' => Utils::StatusId("Usi", "Open"),		
						'user_id' => 1		
						)
					);
			
			\Log::debug($this->usi_create->toJson());
				
			$this->CreateDocument();
				
			$data = $this->CreateUsiCreateObj();
				
			Log::info("Send request to usi service");		
			$response = $this->PostDataToUsiApi("api/usi/createUSI/" . $this->usi_bulk_create->id , $data);				
			$response_obj = json_decode($response, true);
			$response_obj['customer_id'] = $customer->id;

			if (!isset($response_obj['USIStatus']))
			{
				$response_obj['USIStatus'] = 'Invalid';
				$response_obj['USI'] = '';
				$response_obj['Response']['Errors'] = array($response_obj['Message'], "Try later or Contact Office");
			}
			
			$response = json_encode($response_obj);			
		}
		else
		{
			Log::info("request already processed");
			$this->dvs_document = $this->usi_create->document;		
			$this->usi_bulk_create = UsiBulkCreate::find($this->usi_create->usi_bulk_create_id);

			$response = $this->usi_bulk_create->response;
			$response_obj = json_decode($response, true);
		}		
			
		$this->ProcessCreateResponse($response);

		return $response;
		*/
	}

	public function BulkVerifyUsi($data = null)
	{
		$response = array();
		$this->usi_verify = array();
		
		$this->bulk = Input::get('bulk', array());
		if ($data)
			$this->bulk = $data;
		
		$open_status_id = Utils::StatusId("Usi", "Open");

		$this->usi_bulk_verify = UsiBulkVerify::create(array('organisation_code' => '970582','number_of_verifications' => count($this->bulk)));

		foreach($this->bulk as $key => $entry)
		{
			$usi = Input::get('USI', null, $entry);		
			$usi_verify = UsiVerify::where(function ($query) use($open_status_id, $usi){
					$query->where("usi",$usi)
					->orWhere('status_id', $open_status_id);
				})
				->where("first_name",Input::get('FirstName', null, $entry))
				->where("last_name",Input::get('LastName', null, $entry))
				->where("dob",Input::get('DateOfBirth', null, $entry))
				->first();
				
			if(!is_null($usi_verify) )
			{
				$bulk = UsiBulkVerify::find($usi_verify->usi_bulk_verify_id);	
				if($bulk)
					$bulk->delete();
				
				$usi_verify->delete();			
				$usi_verify = null;
			}
		
			if(is_null($usi_verify) )
			{
				$usi_verify = UsiVerify::create(
					array(
							'usi_bulk_verify_id' => $this->usi_bulk_verify->id,
							'record_id' => 0,
							'usi' => Input::get('USI', null, $entry),
							'first_name' => Input::get('FirstName', null,$entry),
							'last_name' => Input::get('LastName', null, $entry),
							'dob' => Input::get('DateOfBirth', null, $entry),
							'status_id' => Utils::StatusId("Usi", "Open"),		
							'user_id' => 1		
							));
				
				$entry['RecordId'] = $usi_verify->id;
				
				$this->usi_verify[] = $entry; 
			}

		}

		if (count($this->usi_verify) > 0)
		{
			Log::info("Send request to usi service");		
			$response = $this->PostDataToUsiApi("api/usi/BulkVerifyUSI", $this->usi_verify);
			$response_obj = json_decode($response, true);
			
			foreach($response_obj['VerifyResponses'] as $resp)
			{
				$usi_verify = UsiVerify::find($resp['RecordId']);
				$usi_verify->update(array('record_id' => $resp['RecordId'],'status_id' => Utils::StatusId("Usi", $resp['USIStatus'])));
			}
			$this->usi_bulk_verify->update(array('response' => $response));
		}
		else
		{
			
		}
		return $response;
	}

	public function VerifyUsi( $createStudent = false)
	{
		$this->customer_id = Input::get('customer_id', null);
		$this->roster_id = Input::get('roster_id', null);

		$student =  null;
		if ($createStudent)
		{
			$student = \CustomerService::CreateUpdateCustomer();	
		}
		
		$response = array();

		//$open_status_id = Utils::StatusId("Usi", "Open");
		$usi = Input::get('USI', null);
		if (!$usi)
			$usi = Input::get('usi', null);

	
		$this->usi_bulk_verify = UsiBulkVerify::create(
			array(
					'organisation_code' => '970582',
					'number_of_verifications' => 1
					));
				
		$this->usi_verify = UsiVerify::create(
			array(
					'usi_bulk_verify_id' => $this->usi_bulk_verify->id,
					'record_id' => $this->usi_bulk_verify->id,
					'usi' => $usi,
					'first_name' => Input::get('first_name', null),
					'last_name' => Input::get('last_name', null),
					'dob' => Input::get('dob', null),
					'status_id' => Utils::StatusId("Usi", "Open"),		
					'user_id' => 1		
					));
				
		$data['USI'] = $usi;
		$data['FirstName'] = Input::get('first_name');
		$data['LastName'] = Input::get('last_name');
		$data['DateOfBirth'] = Input::get('dob');
		$data['RecordId'] = $this->usi_bulk_verify->id;
				
		Log::info("Send request to usi service: " . json_encode($data));		
		$response = $this->PostDataToUsiApi("api/usi/VerifyUSI", $data);
				
		$response_obj = json_decode($response, true);

		if (!isset($response_obj['USIStatus']))
		{
			$response_obj['USIStatus'] = 'Invalid';
			$response_obj['Response']['Errors'] = array($response_obj['Message'], "Try later or Contact Office");
			$response = json_encode($response_obj);
		}
			
		if ($createStudent && $student)
		{
			if(!empty( $response_obj['USI']) && $response_obj['USIStatus'] == 'Valid')
				$student->update( array('unique_student_identifier' => $response_obj['USI'], 'usi_verified' => 1) );	
				
			$response_obj['customer_id'] = $student->id;	
			$response = json_encode($response_obj);
		}
			
		$this->ProcessVerifyResponse($response);

		return $response;
	}

	public function PostDataToUsiApi($route, $data = null) {
		// record on fiddler
		//$this->curl->options = array('PROXY' => '127.0.0.1:8888');
		
		$data_json = empty($data) ? json_encode($this->input) : json_encode($data);                                                                                   

		$this->curl->headers = array(
			'Content-Type'=> 'application/json',
			'Accept' => 'application/json',
			'Content-Length' => strlen($data_json)
			);
		
		$credentials = Config::get('auth.credentials', null);
		
		if($credentials)
			$this->curl->setAuth($credentials['username'], $credentials['password']);
		
		set_time_limit(72000); //time for script to run

		$url = $this->api_server . $route;
		
		$response = $this->curl->post($url, $data_json);

		Log::info('usi response : '. json_encode($response));

		return $response->body;

	}
	
	private function ProcessVerifyResponse($response)
	{
		$customer_upd_data = array();
		$response_obj = json_decode($response, true);
		$this->usi_verify->update(array(
			'usi' => $response_obj['USI'], 
			'customer_id' => $response_obj['customer_id'], 
			'status_id' => Utils::StatusId("Usi", $response_obj['USIStatus'])
			));
		$this->usi_bulk_verify->update(array(
			'response' => $response
			));
	
		\Log::debug($response);
		\Log::debug($this->usi_verify->toJson());
		
		if ($this->usi_verify->customer && !empty($response_obj['USI']))
		{
			$customer_upd_data['unique_student_identifier'] = $response_obj['USI'];
			$customer_upd_data['usi_verified'] = 1;
		}	
		if ($this->usi_verify->customer)
		{
			$this->usi_verify->customer->update($customer_upd_data);
		}	
		else if ($this->usi_verify->customer_id)
		{
			$customer = \Customer::find($this->usi_verify->customer_id);
			if ($customer)
				$customer->update($customer_upd_data);
		}
		if ($this->customer_id)
		{
			$data['customer_id'] = $this->customer_id;
			$data['roster_id'] = $this->roster_id;
			$data['type'] = 'EnrolmentData';
			Queue::push('UsiQueue', $data);
		}

	}
	
	private function ProcessCreateNoUSIResponse()
	{
		$customer_upd_data = array();
		$customer_upd_data['avetmiss_done'] = 1;
		if ($this->customer && !empty($this->customer->unique_student_identifier))
		{
			$customer_upd_data['usi_verified'] = 1;
		}	
		if ($this->customer)
		{
			$this->customer->update($customer_upd_data);
		}	
		else if ($this->customer_id)
		{
			$customer = \Customer::find($this->customer_id);
			$customer->update($customer_upd_data);
		}
		
		$data['customer_id'] = $this->customer_id;
		$data['roster_id'] = $this->roster_id;
		$data['type'] = 'EnrolmentData';
		Queue::push('UsiQueue', $data);
		
		
	}
		
	private function ProcessCreateResponse($response)
	{
		$response_obj = json_decode($response, true);
		$status_id = Utils::StatusId("Usi", $response_obj['USIStatus']);
		if(empty($status_id))
		$status_id = Utils::StatusId("Usi", 'Unknown');
		
		$this->usi_create->update(array(
			'usi' => $response_obj['USI'], 
			'application_id' => $this->usi_create->id, 
			'usi_document_id' => $this->dvs_document->id, 
			'status_id' => $status_id
			));
		$this->usi_bulk_create->update(array(
			'receipt_number'=> isset($response_obj['ReceiptNumber']) ? $response_obj['ReceiptNumber'] : $response_obj['USI'], 
			'request_id' => $this->usi_bulk_create->id, 
			'response' => $response)
				);

		$customer_upd_data['avetmiss_done'] = 1;
		if ($this->usi_create->customer && !empty($response_obj['USI']))
		{
			$customer_upd_data['unique_student_identifier'] = $response_obj['USI'];
			$customer_upd_data['usi_verified'] = 1;
		}	
		if ($this->usi_create->customer)
		{
			$this->usi_create->customer->update($customer_upd_data);
		}	
		else if ($this->usi_create->customer_id)
		{
			$customer = \Customer::find($this->usi_create->customer_id);
			$customer->update($customer_upd_data);
		}
		
		$data['customer_id'] = $this->customer_id;
		$data['roster_id'] = $this->roster_id;
		$data['type'] = 'EnrolmentData';
		Queue::push('UsiQueue', $data);
		
		
	}

	public function GetAddress1() 
	{

		$address = Input::get('address_building_name', '');
		$address .= $address == '' ? Input::get('address_unit_details', '') : ' ' . Input::get('address_unit_details', '');
		$address .= $address == '' ? Input::get('address_street_number', '') : '/' . Input::get('address_street_number', '');
		
		return $address;
	}

	public function GetAddress2() 
	{
		return Input::get('address_street_name', '');
	}
	
	public function CreateUsiCreateObj()
	{
		return array(
			
			'FirstName' => trim($this->usi_create->first_name),
			'MiddleName' => trim($this->usi_create->middle_name),
			'LastName' => trim($this->usi_create->last_name),
			'DateOfBirth' => $this->usi_create->dob,
			'Gender' => trim($this->usi_create->gender),
			'EmailAddress' => trim($this->usi_create->email_address),
			'PhoneNumber' => trim($this->usi_create->phone_number),
			'MobileNumber' => trim($this->usi_create->mobile_number),

			'Address1' => $this->GetAddress1(),
			'Address2' => $this->GetAddress2(),
			'PostCode' => $this->usi_create->post_code,
			'State' => $this->usi_create->state_obj ? $this->usi_create->state_obj->code : '',
			'SuburbTownCity' => trim($this->usi_create->city),
			'TownCityOfBirth' => trim($this->usi_create->town_city_of_birth),

			'CountryOfBirth' => $this->usi_create->country_birth ? $this->usi_create->country_birth->name : '',
			'CountryStudyingIn' => $this->usi_create->country_study_in,
			'CountryOfResidence' => $this->usi_create->country_residence ? $this->usi_create->country_residence->name : '',

			'PreferredMethod' => $this->usi_create->preferred_method,
			'DvsDocument' => $this->dvs_document->getDvsDocument(),

			'ApplicationId' => $this->usi_create->id,
			'RequestId' => $this->usi_bulk_create->id,
			'UserId' => $this->usi_create->user_id

			);
		
		
	}
	
	public function CreateDocument()
	{
		$document = $this->CreateDocumentObj();
		$this->dvs_document = UsiDocument::create(array(
			'usi_create_id' => $this->usi_create->id,
			'document' => json_encode($document)
			));

	}
	
	public function CreateDocumentObj()
	{
		$doc_type = Input::get('DvsDocumentType', null);
		
		if(empty($doc_type))
			throw new Exception("You must provide a proof of Identification");
			
		
		$class = 'App\\Models\\UsiDocuments\\' . $doc_type;

		if (!class_exists($class)) {
			throw new \Exception("Class '$class' not found");
		}

		$document = new $class($this->input['DvsDocument']);

		return $document;
	}

	
}

