<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, DB;
use Response, Exception, PEAR, Mail, Email, PDF, Customer, Certificate, Roster;
use UsiService;

class CertificateService {

	public function __construct()
	{
	}

	public function createNewCustomer() 
	{
		DB::beginTransaction();	
		$roster_id = Input::get('roster_id');	
		
		try 
		{	
			$customer = null;
			$usi = Input::get('usi', null);
			if ($usi)
			{
				$response_json = UsiService::VerifyUsi(true);	
				
				$response = json_decode($response_json);
				if ($response->USIStatus == 'Valid') {
					$customer = \Customer::find($response->customer_id);
				}
				else {
					if ($response->Response && $response->Response->Errors) {
						$errors = "<br>Error Verifying USI<br>Please fix the following errors:<br>" . (count($response->Response->Errors) > 0 ? join('<br>', $response->Response->Errors) : '');
					}
					else {
						$errors = "<br>Error Verifying USI<br>Please fix the following errors:<br>" . (strlen($response->Message) > 0 ? $response->Message : '');
					}
					throw new Exception($errors);
				}				
			}
			else
			{
				$data = Input::all();
				unset($data['customer_id']);
				$customer = \CustomerService::CreateUpdateCustomer($data);
			}		

			$roster = Roster::find($roster_id);
			if($roster)
				$roster->update(array('customer_id' => $customer->id));
			else
				throw new Exception("Could not update roster rolling back transaction");
	
			DB::commit();
			return $customer;
		}
		catch (Exception $e)
		{
			DB::rollback();
			throw $e;
		}

	}

	public function updateCustomer() 
	{
		DB::beginTransaction();	
		try 
		{	
			$customer = null;
			$usi = Input::get('usi', null);
			$customer_id = Input::get('customer_id', null);
			if ($customer_id)
			{
				$customer = \Customer::find($customer_id);
			}
			
			if ($usi && $customer && $customer->unique_student_identifier && !$customer->usi_verified)
			{
				$response_json = UsiService::VerifyUsi(true);	
				
				$response = json_decode($response_json);
				if ($response->USIStatus == 'Valid') {
					$customer = \Customer::find($response->customer_id);
				}
				else {
					if ($response->Response && $response->Response->Errors) {
						$errors = "<br>Error Verifying USI<br>Please fix the following errors:<br>" . (count($response->Response->Errors) > 0 ? join('<br>', $response->Response->Errors) : '');
					}
					else {
						$errors = "<br>Error Verifying USI<br>Please fix the following errors:<br>" . (strlen($response->Message) > 0 ? $response->Message : '');
					}
					throw new Exception($errors);
				}				
			}
			else
			{
				$customer = \CustomerService::CreateUpdateCustomer();
			}		
			
			DB::commit();
			return $customer;
		}
		catch (Exception $e)
		{
			DB::rollback();
			throw $e;
		}
	}

	public function updateCertificate() 
	{

		$input = Input::only('location_id','id','roster_id','course_id','customer_id','certificate_date');
		$input['roster_id'] = isset($input['roster_id']) ? $input['roster_id'] : $input['id'];
		$input['active'] = 1;
		unset($input['id']);
		$user = Sentry::getUser();
		$input['user_id'] = $user->id;
		$input['certificate_date'] = empty($input['certificate_date']) ? date("Y-m-d") : $input['certificate_date'];
		$input['description'] = 'certificate';
		$input['status_id'] = \Utils::StatusId('Certificate', 'Success');
		$validation = Validator::make($input, Certificate::$rules);

		if ($validation->passes())
		{
			$roster = Roster::find($input['roster_id']);
			
			//if (!$roster->isPaid() && !$roster->is_agent_to_pay)
			//{
			//	$msg = "The Roster is not Paid, unable to generate certificate";
			//	throw new Exception($msg);				
			//}
			
			//if ((float)$roster->order->total > (float)$roster->order->paid)
			//{
			//	$msg = "The Order is not Paid, unable to generate certificate";
			//	throw new Exception($msg);				
			//}

			if (Input::get('certificate_id') != '')
			{
				$certificate = Certificate::find(Input::get('certificate_id'));
				$certificate->update(array('customer_id'=>$input['customer_id'], 'certificate_date'=>$input['certificate_date']));
			}
			else
				$certificate = Certificate::create($input);
			
			$roster->update(array('certificate_id'=>$certificate->id));

			return $certificate;
		}
		
		throw new Exception('Problem updating certificate:<br>' . implode('<br>',$validation->errors()->getMessageBag()->all()));

	}
	
	public function updateOnlineCertificate($student) 
	{

		$input = Input::only('location_id','id','roster_id','course_id','customer_id','certificate_date');
		$input['location_id'] = 0;
		$input['course_id'] = $student->current_online_roster->course->id;
		$input['customer_id'] = $student->id;
		$input['roster_id'] = $student->current_online_roster->id;
		$input['active'] = 1;
		$input['user_id'] = $student->user->id;
		$input['certificate_date'] = date("Y-m-d");
		$input['description'] = 'Online certificate';
		$input['status_id'] = \Utils::StatusId('Certificate', 'Success');
		$validation = Validator::make($input, Certificate::$rules);

		if ($validation->passes())
		{
			$certificate = Certificate::create($input);
			$student->current_online_roster->update(array('certificate_id'=>$certificate->id));
			return $certificate;
		}
		
		throw new Exception('Problem creating online certificate:<br>' . implode('<br>',$validation->errors()->getMessageBag()->all()));

	}


}