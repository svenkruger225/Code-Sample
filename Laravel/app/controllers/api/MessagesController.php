<?php namespace Controllers\Api;

use AdminController;
use Config,Input,Lang,Redirect,Sentry,Validator,View, Response, DB, Utils, Mail;
use Location, User, Customer, Course, CourseInstance, Roster, Order, Item;
use Queue, Message, Marketing, MarketingSession, Attachment, SmsService, EmailService, MessageType, Instructor;

class MessagesController extends AdminController {

	/**
	 * Message Repository
	 *
	 * @var Message
	 */
	protected $email;

	public function __construct(Message $email, Order $order)
	{
		parent::__construct();
		$this->email = $email;
		$this->order = $order;
	}

	public function testEmail()
	{
		
		return Input::all();
		
		$email = Message::find($data['id']);

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

	public function testMarketingEmail()
	{
		
		$data = Input::all();
		
		$message = Marketing::find($data['id']);
		$message->first_name = 'Test';
		$message->last_name = 'Email';
		$message->mail_out_email = 1;
		$message->mail_out_sms = 1;
		$message->email = $data['email'];
		$message->mobile = !empty($data['mobile']) ? $data['mobile'] : '';
		$message->browser_view = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/viewMarketingEmail/" . $data['id'] . "/179121'>View message on browser</a></p>";
		$message->unsubscribe = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/unsubscribe/179121'>unsubscribe</a></p>";
		$message->disclamer = "<br><br><p>Disclaimer: Ton Ton Song Pty Ltd recognises that your privacy is very important to you and we are committed to protecting your personal information. You have received this email because you have given permission to be corresponded to by Ton Ton Song Pty Ltd. To unsubscribe please click the unsubscribe link below. Ton Ton Song Pty Ltd is trading as Coffee School. ABN 9211 541 9988</p>";		
		
		$data = $message->toArray();
		$data['attachments'] = $message->attachments->lists('path', 'id');
		Queue::push('MarketingQueue', $data);
		return Response::json(array());
	}

	public function processMarketingEmail()
	{
		$data['message_id'] = Input::get('id');
		$data['start'] = '0';
		$data['qty'] = '200';
		$data['qty_so_far'] = '0';
		$session_date = new \DateTime();
		$data['session_id'] = \Utils::GeneratePassword(20,20) . $session_date->format("U");
		$data['started'] = 0;

		\Log::info("Start Queue for Session Id " . $data['session_id'] . ", Message Id " . $data['message_id'] . ", Start at " . $data['start'] . ", Qty " . $data['qty']);
		
		MarketingSession::create($data);
		
		$data['type'] = 'Publisher';
		Queue::push('MarketingQueue', $data);

		return Response::json(array('msg'=>'Messages Queued'));
		
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
					if ($roster && $roster->customer)
					{
						\Log::debug('roster: ' . $roster->id . ', customer: ' . $roster->customer->name . ', email: ' . $roster->customer->email );
						
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
					else
					{
						\Log::debug('problem roster: ' . $roster_id );
					}
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