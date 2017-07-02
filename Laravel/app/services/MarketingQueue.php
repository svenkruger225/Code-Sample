<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Message;
use EmailService,SmsService, Marketing, MarketingSession, Queue;

class MarketingQueue {

	public function __construct()
	{
	}

	public function fire($job, $data) 
	{

		if (!empty($data['type']) && $data['type'] == 'Publisher')
		{
			$session = MarketingSession::where('session_id',$data['session_id'])
										->where('message_id', $data['message_id'])
										->where('start', $data['start'])
										->where('qty', $data['qty'])
										->where('started', 0)
										->first();
				
			if($session)
			{	
				$session->update(array('started'=> 1));
				\Log::info("Start Process for Session Id " . $data['session_id'] . ", Message Id " . $data['message_id'] . ", Start at " . $data['start'] . ", Qty " . $data['qty']);
				$this->processBlockMarketingEmail($data);
			}
		}
		else
		{
			\Log::info("Fire Message to: " . $data['first_name'] . " " . $data['last_name'] . " " . $data['email'] . " " . $data['mobile'] . " " . $data['mail_out_email'] . " " . $data['mail_out_sms'] );
			if(($data['send_via'] == 'Email' || $data['send_via'] == 'Both') && filter_var($data['mail_out_email'], FILTER_VALIDATE_BOOLEAN))
			{
				//\Log::info("Email SENT...................................................");
				EmailService::sendMarketingMessage($data);
			}
			if(($data['send_via'] == 'Sms' || $data['send_via'] == 'Both') && filter_var($data['mail_out_sms'], FILTER_VALIDATE_BOOLEAN))
			{
				//\Log::info("SMS SENT...................................................");
				SmsService::sendMarketingMessage($data);
			}
		}

		$job->delete();		

	}
	
	public function processBlockMarketingEmail($input)
	{
		$message_id = $input['message_id'];
		$start = $input['start'];
		$qty = $input['qty'];
		$qty_so_far = $input['qty_so_far'];
		$session_id = $input['session_id'];
		
		\Log::info("Start Block for Session Id " . $session_id . ", Message Id " . $message_id . ", Start at " . $start . ", Qty " . $qty);

		$message = Marketing::find($message_id); 
		$sql = "SELECT DISTINCT cus.id, cus.first_name, cus.last_name, cus.email, cus.mobile,cus.mail_out_email,cus.mail_out_sms 
				FROM rosters r
				JOIN customers cus on cus.id = r.customer_id
				JOIN courseinstances ci on ci.id = r.course_instance_id
				JOIN locations l on l.id = ci.location_id
				JOIN locations pl on pl.id = l.parent_id OR (pl.id = l.id AND l.parent_id = 0)
				JOIN courses c on c.id = ci.course_id 
				WHERE (cus.mail_out_email = 1 OR cus.mail_out_sms = 1) AND c.id != 9 AND ci.cancelled = 0 AND ci.active = 1 ";
		
		if (!empty($message->location_id))	
			$sql .= "AND ci.location_id IN (SELECT ID FROM locations WHERE id = " . $message->location_id . " OR parent_id = " . $message->location_id . ") ";

		if (!empty($message->course_id))		
			$sql .= "AND ci.course_id = " . $message->course_id . " ";	

		if (!empty($message->date_from) && $message->date_from != '0000-00-00')
			$sql .= "AND ci.course_date >= '" .$message->date_from . "' ";
		
		if (!empty($message->date_to) && $message->date_to != '0000-00-00')
			$sql .= "AND ci.course_date <= '" .$message->date_to . "'";
		
		$sql .= " LIMIT $start, $qty";	
		
		//\Log::info($sql);

		$result = \DB::select( $sql );
		
		$emails=array(); 
		$mobiles = array(); 

		if (count($result) > 0)
		{
			$counter = 0;
			\Log::info("Start Processing Marketing messages for " . count($result));
			foreach ($result as $sql_data)
			{
				$counter++;
				$message->first_name = $sql_data->first_name;
				$message->last_name = $sql_data->last_name;
				$message->mail_out_email = $sql_data->mail_out_email;
				$message->mail_out_sms = $sql_data->mail_out_sms;
				
				if(in_array($sql_data->email, $emails)) 
				{ 
					$message->mail_out_email = 0;
				} 
				if(in_array($sql_data->mobile, $mobiles)) 
				{ 
					$message->mail_out_sms = 0;
				} 
				
				if(filter_var($sql_data->mail_out_email, FILTER_VALIDATE_BOOLEAN))
				{
					$emails[] = $sql_data->email; 
				}
				if(filter_var($sql_data->mail_out_sms, FILTER_VALIDATE_BOOLEAN))
				{
					$mobiles[] = $sql_data->mobile; 			
				}
				
				////test
				//$message->mobile = '0414589738';
				//$message->email = 'csouza@live.com.au';
				//$message->unsubscribe = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/unsubscribe/179121'>unsubscribe</a></p>";
				//$message->browser_view = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/viewMarketingEmail/" . $data['message_id'] . "/179121'>View message on browser</a></p>";
				//$message->body .= "Total emails: " . count($result);
				
				//prod
				$message->unsubscribe = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/unsubscribe/" . $sql_data->id . "'>unsubscribe</a></p>";
				$message->browser_view = "<p><a href='http://" .\Request::server('SERVER_NAME') ."/api/emails/viewMarketingEmail/" . $message_id . "/" . $sql_data->id . "'>View message on browser</a></p>";
				$message->disclamer = "<br><br><p>Disclaimer: Ton Ton Song Pty Ltd recognises that your privacy is very important to you and we are committed to protecting your personal information. You have received this email because you have given permission to be corresponded to by Ton Ton Song Pty Ltd. To unsubscribe please click the unsubscribe link below. Ton Ton Song Pty Ltd is trading as Coffee School. ABN 9211 541 9988</p>";		
				$message->mobile = $sql_data->mobile;
				$message->email = $sql_data->email;
				
				$data = $message->toArray();
				$data['attachments'] = $message->attachments->lists('path', 'id');
				$data['type'] = 'Mailer';
				if
				( 
					(($data['send_via'] == 'Email' || $data['send_via'] == 'Both') && filter_var($data['mail_out_email'], FILTER_VALIDATE_BOOLEAN)) ||
					(($data['send_via'] == 'Sms' || $data['send_via'] == 'Both') && filter_var($data['mail_out_sms'], FILTER_VALIDATE_BOOLEAN))
				)
				{
					//\Log::info("SENT...................................................");
					Queue::push('MarketingQueue', $data);
					\Log::info("Queued Message to: |" . $counter . "|" . $data['first_name'] . "|" . $data['last_name'] . "|" . $data['email'] . "|" . $data['mobile'] . "|" . $data['mail_out_email'] . "|" . $data['mail_out_sms'] );
				}
				else
				{
					\Log::info("Not Queued Message to: |" . $counter . "|" . $data['first_name'] . "|" . $data['last_name'] . "|" . $data['email'] . "|" . $data['mobile'] . "|" . $data['mail_out_email'] . "|" . $data['mail_out_sms'] );
				}
			}
			\Log::info("Finished Processing Marketing messages for " . count($result));
			$start += $qty;
			\Log::info("Publish Message for Session Id " . $session_id . ", Message Id " . $message_id . ", Start at " . $start . ", Qty " . $qty);


			$new_data['message_id'] = $message_id;
			$new_data['start'] = $start;
			$new_data['qty'] = $qty;
			$new_data['qty_so_far'] = $qty_so_far + count($result);
			$new_data['session_id'] = $session_id;
			$new_data['started'] = 0;
			MarketingSession::create($new_data);
			
			$new_data['type'] = 'Publisher';
			Queue::push('MarketingQueue', $new_data);
		}
		else
		{
			if ($start > 0)
			{
				// send a copy
				$message->first_name = 'Admin';
				$message->last_name = '';
				$message->mobile = '0414589738';
				$message->email = 'csouza@live.com.au';
				$message->mail_out_email = 1;
				$message->mail_out_sms = 1;
				$message->unsubscribe = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/unsubscribe/179121'>unsubscribe</a></p>
									 <p>Total emails: " . $qty_so_far . "</p>";
				$message->browser_view = "<p><a style='color: #333 !important;' href='http://" .\Request::server('SERVER_NAME') ."/api/emails/viewMarketingEmail/" . $message_id . "/179121'>View message on browser</a></p>";
				$message->disclamer = "<br><br><p>Disclaimer: Ton Ton Song Pty Ltd recognises that your privacy is very important to you and we are committed to protecting your personal information. You have received this email because you have given permission to be corresponded to by Ton Ton Song Pty Ltd. To unsubscribe please click the unsubscribe link below. Ton Ton Song Pty Ltd is trading as Coffee School. ABN 9211 541 9988</p>";		
				$data = $message->toArray();
				$data['attachments'] = $message->attachments->lists('path', 'id');
				$data['type'] = 'Mailer';
				Queue::push('MarketingQueue', $data);
				\Log::info("Queued Message to: " . $data['first_name'] . "|" . $data['last_name'] . "|" . $data['email'] . "|" . $data['mobile'] . "|" . $data['mail_out_email'] . "|" . $data['mail_out_sms'] );
				
				$data['first_name'] = 'Marketing';
				$data['last_name'] = '';
				$data['mobile'] = '0452224992';
				$data['email'] = 'marketing@coffeeschool.com.au';
				Queue::push('MarketingQueue', $data);
				\Log::info("Queued Message to: " . $data['first_name'] . "|" . $data['last_name'] . "|" . $data['email'] . "|" . $data['mobile'] . "|" . $data['mail_out_email'] . "|" . $data['mail_out_sms'] );
			}
			
		}
		
	}
	
	

}