<?php namespace Controllers\Api;

use AdminController;
use Config,Input,Lang,Redirect,Sentry,Validator,View, Response, DB, Utils, Mail;
use Location, User, Customer, Course, CourseInstance, Roster, Order, Item, Marketing, Attachment, SmsService, EmailService;

class MarketingController extends AdminController {

	/**
	 * Marketing Repository
	 *
	 * @var Marketing
	 */
	protected $email;

	public function __construct(Marketing $email, Order $order)
	{
		parent::__construct();
		$this->email = $email;
		$this->order = $order;
	}

	public function testEmail()
	{
		
		$data = Input::all();
		
		$email = Marketing::find($data['id']);

		$order = array(
			'id' => '1234',
			'backend' => '1',
			'total' => '100.00',
			'customer' => array(
					'full_name' => 'Carlos Testing',
					'id' => '11',
					'email' => $data['email'],
					'phone' => '1234567890',
					'mobile' => '987654321',
					'question1' => 'answer 1',
					'question2' => 'answer 2',
					'question3' => 'answer 3'
					),
				'items' => array(
					array(
						'course_instance_id' => null,
						'group_booking_id' => '13546',
						'vouchers_ids' => '',
						'product_id' => null,
						'item_type_id' => '1',
						'qty' => '2',
						'price' => '15.00',
						'total' => '30.00',
						'active' => '1'
						),
					array(
						'course_instance_id' => null,
						'group_booking_id' => '23564',
						'vouchers_ids' => '',
						'product_id' => null,
						'item_type_id' => '1',
						'qty' => '3',
						'price' => '15.00',
						'total' => '45.00',
						'active' => '1'
						),
					),
			'agent_id' => null,
			'company_id' => null
		);		
		$orderObj = json_decode(json_encode($order));


		$roster = array(
			'customer' => array(
					'first_name' => 'Carlos',
					'last_name' => 'Testing',
					'email' => $data['email']
					),
				'instance' => array(
					'location' => array(
						'name' => 'Sydney',
						'address' => 'Sydney address',
						'city' => 'Haymarket',
						'state' => 'NSW',
						'post_code' => '2000',
						'email' => 'info@coffeeschool.com.au',
						'phone' => '1234567890',
						'mobile' => '1234567890'
						),
					'course' => array(
						'name' => 'RSA' 
						),
					'course_date' => '2013-10-28',
					'time_start' => '10:00 AM',
					'time_end' => '04:00 PM'
					)
				);		
		
		$rosterObj = json_decode(json_encode($roster));
		
		EmailService::sendToAdmin($orderObj, $email);
		EmailService::sendToCustomer($rosterObj, false, $email);
		return Response::json(array());
	}

	public function processBulkMessages()
	{
		$input = Input::json()->all();
	
		try 
		{	
			if ($input['message']['Type'] == 'Customer' || $input['message']['Type'] == 'User')
			{
				$sent = 0;
				$errors = 0;
				$response = "<ul>";
				if (is_array($input['list']) && count($input['list']) > 0)
				{
					foreach($input['list'] as $id)
					{
						$input['message']['customer_id'] = $id;
						if ($this->sendCustomerMessage($input, true))
							$sent++;
						else
							$errors++;
						//$response .= '<li>' . $this->sendCustomerMessage($input) . '</li>';
					}
					$response .= '<li>' . $sent . ' Message(s) sent</li>';
					$response .= '<li>' . $errors . ' Messages with error</li>';
				}
				else
				{
					$response = '<li>' . $this->sendCustomerMessage($input, false) . '</li>';
				}
				$response .= "</ul>";
				return Response::json(array('msg'=>'Processed message(s)<br>' . $response));
			}
			else
			{
				return $this->sendBulkMessages($input);
			}

		}
		catch (Exception $e)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem processing bulk messages<br>" . $e->getMessage()
				), 500);
		}
	}
	
	public function sendCustomerMessage($input, $is_bulk = false)
	{
		$response = "";
		try 
		{	
			if (!empty($input['message']['Type']) && $input['message']['Type'] == 'User')
			{
				$customer = User::find($input['message']['customer_id']);
				//\Log::info("User Message " . $customer->mobile . " " . $customer->name);
			}
			else
			{
				$customer = Customer::find($input['message']['customer_id']);
				//\Log::info("Customer Message " . $customer->mobile . " " . $customer->name);
			}
			
			if (filter_var($input['message']['SendEmail'], FILTER_VALIDATE_BOOLEAN))
			{
				$result = json_decode(json_encode(array(
					'subject' => $input['message']['Subject'],
					'email' => $customer->email,
					'name' => $customer->name,
					'body' => $input['message']['Message']
					)));
						
				$data = array('result'=> $result);		
				Mail::send('backend.messages.custom', $data, function($message) use ($result)
					{
						$message->subject($result->subject)->to($result->email, $result->name);
					});
				if ($is_bulk)
					$response = true;
				else
					$response = "Email sent <br>";
			}
			if (filter_var($input['message']['SendSms'], FILTER_VALIDATE_BOOLEAN))
			{
				$data = array(
					'to' => $customer->mobile,
					'text' => $input['message']['Message']
					);

				$result = SmsService::sendmsg($data);				
				if ($is_bulk)
					$response = true;
				else
					$response .= implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));
			}
				
			return $response;

		}
		catch (Exception $e)
		{
			if ($is_bulk)
				return false;
			else
				return $e->getMessage();
		}
	}
	
	public function sendBulkMessages($input)
	{
		
		$response = "";
		try 
		{	
			foreach($input['list'] as $roster_id)
			{
				$roster = Roster::find($roster_id);
				if (filter_var($input['message']['SendEmail'], FILTER_VALIDATE_BOOLEAN))
				{
					$result = json_decode(json_encode(array(
						'subject' => $input['message']['Subject'],
						'email' => $roster->customer->email,
						'name' => $roster->customer->name,
						'body' => $input['message']['Message']
						)));
					
					$data = array('result'=> $result);		
					Mail::send('backend.messages.custom', $data, function($message) use ($result)
						{
							$message->subject($result->subject)->to($result->email, $result->name);
						});
				}
				if (filter_var($input['message']['SendSms'], FILTER_VALIDATE_BOOLEAN))
				{
					$data = array(
						'to' => $roster->customer->mobile,
						'text' => $input['message']['Message']
						);
					SmsService::sendmsg($data);
				}
			}
			
			return Response::json(array('msg'=>'Successfully Processed bulk messages'));

		}
		catch (Exception $e)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem processing bulk messages<br>" . $e->getMessage()
				), 500);
		}
	}


}