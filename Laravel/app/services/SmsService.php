<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, DateTime;
use Roster, CourseInstance, GroupBooking, Message;
use App\Services\Html2Text;

	class SmsService {

	// Curl handle resource id
	var $_ch;

	//file resource id
	var $_fp;


	public function __construct()
	{
		$config = Config::get('sms', array());
		$this->api_server = $config['serverUrl'];
		$this->credentials = "&mobileID=" . $config['mobileID'] . "&password=" . $config['password'] ;
		$this->from_type = 	"&from=" . rawurlencode($config['from']) . "&msg_type=" . $config['msg_type'];
		$this->msg_types = $config['msg_types'];
		$this->curl_use_proxy = false;

	}

	// Query balance of remaining SMS credits
	function getbalance() {
		$_url = $this->api_server . "?querybalance";
		$_post_data = $this->credentials;

		$response = $this->_curl($_url, $_post_data);
		
		if ($response['errno'] != '0') {
			return $response;
		}
		
		$send = $response['data'];

		if (is_int($send)) {
			return trim($send);
		} else {
			return $send;
		}

	}

	function querymsg ($_msg) {
		$_url = $this->api_server . "?querymessage";
		$_post_data = $this->credentials . "&msgid=" . $_msg['msgid'];

		$response = $this->_curl($_url, $_post_data);
		
		if ($response['errno'] != '0') {
			return $response;
		}
		
		$status = explode("&", $response['data']);
		
		if ($status[0] != "") {
			return array($status[0], $status[1]);
		} else {
			return $response['data'];
		}
	}

	public function send($roster, $isGroupBooking = false, $sms = null) 
	{
		try 
		{
			if ($isGroupBooking)
				$instance = $roster->groupbooking;
			else
				$instance = $roster->instance;

			Log::info('Sending Sms for order: '. $roster->order_id .  ', roster: '. $roster->id . ', mobile: '. $roster->customer->mobile . ', isGropupBooking: ' . $isGroupBooking . ', location: ' . $instance->location_id . ', course: ' . $instance->course_id);

			if (!is_object($sms))
				$sms = Message::where('message_id', Utils::MessageTypeId('Sms'))->where('location_id', $instance->location_id)->where('course_id',$instance->course_id)->first();

			if (!is_object($sms))
				$sms = Message::where('message_id', Utils::MessageTypeId('Sms'))->where('location_id', null)->where('course_id',$instance->course_id)->first();

			if (!is_object($sms))
				$sms = Message::where('message_id', Utils::MessageTypeId('Sms'))->where('location_id', null)->where('course_id',null)->first();


			if (!is_object($sms))
			{
				$sms = new Message();
				if ($isGroupBooking)
				{
					$sms->body = Config::get('sms.group_default', null);
					//Log::info('Group message');
				}
				else
				{
					$sms->body = Config::get('sms.course_default', null);
					//Log::info('Course message: ' . $sms->msg);
				}
			}
			
			if (!is_object($sms))
			{
				throw new Exception("Could not find sms body");
			}


			$result = json_decode(json_encode(array(
				'order_id' => $roster->order_id,
				'first_name' => $roster->customer->first_name,
				'last_name' => $roster->customer->last_name,
				'mobile' => $roster->customer->mobile,
				'location' => $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'course_name' => $instance->course->name,
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'start_time' => $instance->start_time,
				'end_time' => $instance->end_time
				)));		
		
			$msg = strip_tags($sms->body);
			
			preg_match_all('#\{\{(.*?)\}\}#', $msg, $matches);
			foreach($matches[1] as $match)
			{
				if (property_exists($result, $match))
					$msg = str_replace("{{" .$match. "}}", $result->$match, $msg);
			}
	
			//Log::info('Sending Sms mobile: ' . $roster->customer->mobile . ', message: '. $msg);
		
			$data = array(
				'to' => preg_replace('/\s+/', '', $roster->customer->mobile),
				'text' => $msg
				);
			\Log::info('data: ' . json_encode($data));
			
			$res = $this->sendmsg($data);
			$res1 = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($res),array_values($res)));
			
			Log::info('Sending Sms to  mobile: '. $roster->customer->mobile . ', result code: ' . $res1);

		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	function sendmsg ($_msg) {

		set_time_limit(72000); //time for script to run

		$_url = $this->api_server . "?sendsms";
		
		$_post_data = 
				$this->credentials . 
				"&to=" . rawurlencode($_msg['to']) . 
				"&text=" . rawurlencode($_msg['text']) . 
				$this->from_type;

		//Log::info('post_data : '. $_post_data);

		$response = $this->_curl($_url, $_post_data);

		Log::info('sms response : '. json_encode($response));
		//sms response : {"errno":0,"error":"","data":"MessageID:20140604101031VWjr0rieXUKwar50mk:61428265099\n","http_code":200} [] []

		$result = array();
				
		if ($response['errno'] != '0') {
			$result['result'] = 'error';
			$result['message'] = $response['error'];
			$result['mobile'] = '';
			$result['resultId'] = '';
			//return $response;
		}
		elseif (strpos( $response['data'], 'An error has occured') !== false) {
			$result['result'] = 'error';
			$result['message'] = $response['data'];
			$result['mobile'] = '';
			$result['resultId'] = '';
			//return array($response['data']);
		}
		elseif (strpos( $response['data'], '500') !== false) {
			$result['result'] = 'error';
			$result['message'] = $response['data'];
			$result['mobile'] = '';
			$result['resultId'] = '';
			//return array($response['data']);
		}
		else
		{		
			$data = explode(":", str_replace(array("\r", "\n"), '', $response['data']));			
			$result['result'] = 'success';
			$result['mobile'] = !empty($data[2]) ? $data[2] : json_encode($data);
			$result['resultId'] = !empty($data[1]) ? $data[1] : json_encode($data);
			$result['message'] = '';
		}
		
		return $result;

	}


	public function sendMarketingMessage($message) 
	{	
		try 
		{
			$message = json_decode(json_encode($message));			
			$body = $message->browser_view . $message->sms_body . $message->unsubscribe;
			preg_match_all('#\{\{(.*?)\}\}#', $body, $matches);
			foreach($matches[1] as $match)
				if (property_exists($message, $match))
					$body = str_replace("{{" .$match. "}}", $message->$match, $body);
			
			$body = preg_replace("/<img[^>]+\>/i", "", $body); 
			$h2t = new Html2Text($body);
			$data = array(
				'to' => preg_replace('/\s+/', '', $message->mobile),
				'text' => $h2t->get_text()
				);
			//\Log::info('data: ' . json_encode($data));
			
			$result = $this->sendmsg($data);		
			$response = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));		
			//\Log::info(sprintf("Marketing: %s, Mobile: %s, Result Id: %s", $message->first_name, $message->mobile, $response));
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}
		
		
	}

	public function sendEnrolmentDataReminder($data) 
	{	
		try 
		{
			$sms = Message::where('message_id', Utils::MessageTypeId('EnrolmentReminderSms'))
				->where('active',1)->first();
			
			if (!is_object($sms))
			{
				throw new Exception("Could not find message body");
			}
			
			$customer = \Customer::findOrFail($data['customer_id']);
			$roster = \Roster::findOrFail($data['roster_id']);	
			
			if (!empty($roster->group_booking_id))
				$instance = $roster->groupbooking;
			else
				$instance = $roster->instance;

			$result = json_decode(json_encode(array(
				'order_id' => $roster->order_id,
				'first_name' => $roster->customer->first_name,
				'last_name' => $roster->customer->last_name,
				'mobile' => $roster->customer->mobile,
				'location' => $instance->location->name,
				'address' => $instance->location->address,
				'city' => $instance->location->city,
				'course_name' => $instance->course->name,
				'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
				'start_time' => $instance->start_time,
				'end_time' => $instance->end_time
				)));		
			
			$msg = strip_tags($sms->body);
			preg_match_all('#\{\{(.*?)\}\}#', $msg, $matches);
			foreach($matches[1] as $match)
				if (property_exists($result, $match))
					$msg = str_replace("{{" .$match. "}}", $result->$match, $msg);
			
			//Log::info('Sending Sms mobile: ' . $roster->customer->mobile . ', message: '. $msg);
			
			$data = array(
				'to' => $roster->customer->mobile,
				'text' => $msg
				);
			
			$result = $this->sendmsg($data);
			$response = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));		
			//\Log::info(sprintf("Marketing: %s, Mobile: %s, Result Id: %s", $message->first_name, $message->mobile, $response));
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		}	
		
	}


	public function SendRemiderSms($date_to_run = null)
	{
		if (empty($date_to_run))
		{
			$tomorrow = new DateTime('tomorrow');
			$date_to_run = $tomorrow->format('Y-m-d');
		}
		Log::info('Start processing reminder messages for: ' . $date_to_run);
		
		$server_results = array();
		
		$instances = CourseInstance::where('course_date', $date_to_run)
			->where('course_id','!=',  9)
			->where('cancelled', 0)
			->where('active', 1)
			->get();
			
		Log::info(sprintf("CourseInstance instances: %s", $instances->count()));
		
		foreach($instances as $instance)
		{
			try
			{
				$course['course_name'] = sprintf("%s  %s  %s : %s", $instance->course->name, date('M-d-Y (D)', strtotime($instance->course_date)),$instance->start_time,$instance->end_time);
				//array_push($server_results, array('course_name' => sprintf("%s  %s  %s : %s", $instance->course->name, date('M-d-Y (D)', strtotime($instance->course_date)),$instance->start_time,$instance->end_time)));
						
				$course['messages'] = array();

				$sms = Message::where('message_id', Utils::MessageTypeId('Reminder'))->where('location_id', $instance->location_id)->where('course_id',$instance->course_id)->first();

				if (!is_object($sms))
					$sms = Message::where('message_id', Utils::MessageTypeId('Reminder'))->where('location_id', null)->where('course_id',$instance->course_id)->first();

				if (!is_object($sms))
					$sms = Message::where('message_id', Utils::MessageTypeId('Reminder'))->where('location_id', null)->where('course_id',null)->first();


				if (!is_object($sms))
				{
					$sms = new Message();
					$sms->body = Config::get('sms.course_reminder', null);
				}
				
				$rosters = $instance->rosters->filter(function($roster) 
				{ 
					if ($roster->reminder_sms_sent == 0)
						return $roster; 
				});		
			
				Log::info(sprintf("instance: %s , students: %s, msgs to send: %s", $instance->id, $instance->rosters->count(), $rosters->count()));
				
				foreach($rosters as $roster)
				{
					try 
					{
						$result = json_decode(json_encode(array(
							'order_id' => $roster->order_id,
							'first_name' => $roster->customer->first_name,
							'last_name' => $roster->customer->last_name,
							'mobile' => $roster->customer->mobile,
							'location' => $instance->location->name,
							'address' => $instance->location->address,
							'city' => $instance->location->city,
							'course_name' => $instance->course->name,
							'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
							'start_time' => $instance->start_time,
							'end_time' => $instance->end_time
							)));		
				
						$msg = strip_tags($sms->body);
						preg_match_all('#\{\{(.*?)\}\}#', $msg, $matches);
						foreach($matches[1] as $match)
							if (property_exists($result, $match))
								$msg = str_replace("{{" .$match. "}}", $result->$match, $msg);
				
						//Log::info('Sending Sms mobile: ' . $roster->customer->mobile . ', message: '. $msg);
				
						$data = array(
							'to' => $roster->customer->mobile,
							'text' => $msg
							);
				
						$result = $this->sendmsg($data);
						$response = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));
						//$result = array ( mt_rand ( 1000000000 , 9999999999 ) );
						
						if ($result['result'] == 'success')
						{
							$roster->update(array('reminder_sms_sent' => 1));
						}
				
						array_push($course['messages'], sprintf("Customer: %s, Mobile: %s, Result Id: %s", $roster->customer->full_name, $roster->customer->mobile, $response));
				
						Log::info(sprintf("Customer: %s, Mobile: %s, Result Id: %s", $roster->customer->full_name, $roster->customer->mobile, $response));
				
						set_time_limit(120);
				
					}
					catch (Exception $ex)
					{
						Log::error('Exception on - CourseInstance rosters as roster');
						Log::error($ex);
					}
				}

				Log::info(sprintf("instance: %s , instructors: %s", $instance->id, $instance->instructors->count()));
			
				$sms = Message::where('message_id', Utils::MessageTypeId('TrainerSms'))->where('location_id', null)->where('course_id',null)->first();

				if (!is_object($sms))
				{
					$sms = new Message();
					$sms->body = Config::get('sms.instructor_reminder', null);
				}
			
				foreach($instance->instructors as $instructor)
				{
					try 
					{
						$result = json_decode(json_encode(array(
							'order_id' => '',
							'first_name' => $instructor->first_name,
							'last_name' => $instructor->last_name,
							'mobile' => $instructor->mobile,
							'location' => $instance->location->name,
							'address' => $instance->location->address,
							'city' => $instance->location->city,
							'course_name' => $instance->course->name,
							'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
							'start_time' => $instance->start_time,
							'end_time' => $instance->end_time
							)));		
				
						$msg = strip_tags($sms->body);
						preg_match_all('#\{\{(.*?)\}\}#', $msg, $matches);
						foreach($matches[1] as $match)
							$msg = str_replace("{{" .$match. "}}", $result->$match, $msg);
				
						$data = array(
							'to' => $instructor->mobile,
							'text' => $msg
							);
				
						$result = $this->sendmsg($data);
						$response = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));
						//$result = array ( mt_rand ( 1000000000 , 9999999999 ) );
						array_push($course['messages'], sprintf("Instructor: %s, Mobile: %s, Result Id: %s", $instructor->name, $instructor->mobile, $response));
				
						Log::info(sprintf("Instructor: %s, Mobile: %s, Result Id: %s", $instructor->name, $instructor->mobile, $response));
					
					}
					catch (Exception $ex)
					{
						Log::error('Exception on - CourseInstance instructors as instructor');
						Log::error($ex);
					}
				
				}
				if ($rosters->count() || $instance->instructors->count())
					$server_results = array_add($server_results, $instance->id, $course);
				//array_push($server_results, array('messages' => $messages));

				
			}
			catch (Exception $ex)
			{
				Log::error('Exception on - instances as instance');
				Log::error($ex);
			}
			
		}


		$instances = GroupBooking::where('course_date', $date_to_run)->where('active', 1)->get();
		
		Log::info(sprintf("GroupBooking instances: %s", $instances->count()));
		
		foreach($instances as $instance)
		{
			if ($instance->instructors->count() > 0)
			{
				try
				{

					Log::info(sprintf("GroupBooking instance: %s , instructors: %s", $instance->id, $instance->instructors->count()));
			
					$sms = Message::where('message_id', Utils::MessageTypeId('TrainerSms'))->where('location_id', null)->where('course_id',null)->first();

					if (!is_object($sms))
					{
						$sms = new Message();
						$sms->body = Config::get('sms.instructor_reminder', null);
					}
				
					foreach($instance->instructors as $instructor)
					{
						try
						{
							$result = json_decode(json_encode(array(
								'order_id' => '',
								'first_name' => $instructor->first_name,
								'last_name' => $instructor->last_name,
								'mobile' => $instructor->mobile,
								'location' => $instance->location->name,
								'address' => $instance->location->address,
								'city' => $instance->location->city,
								'course_name' => $instance->course->name,
								'course_date' => date('M-d-Y (D)', strtotime($instance->course_date)),
								'start_time' => $instance->start_time,
								'end_time' => $instance->end_time
								)));		
					
							$msg = strip_tags($sms->body);
							preg_match_all('#\{\{(.*?)\}\}#', $msg, $matches);
							foreach($matches[1] as $match)
								$msg = str_replace("{{" .$match. "}}", $result->$match, $msg);
					
							$data = array(
								'to' => $instructor->mobile,
								'text' => $msg
								);
					
							$result = $this->sendmsg($data);
							$response = implode(', ', array_map(function ($k, $v) { return "$k ($v)"; },array_keys($result),array_values($result)));
							//$result = array ( mt_rand ( 1000000000 , 9999999999 ) );
							array_push($course['messages'], sprintf("GroupBooking Instructor: %s, Mobile: %s, Result Id: %s", $instructor->name, $instructor->mobile, $response));
					
							Log::info(sprintf("GroupBooking Instructor: %s, Mobile: %s, Result Id: %s", $instructor->name, $instructor->mobile, $response));
				
						}
						catch (Exception $ex)
						{
							Log::error('Exception on - GroupBooking instructors as instructor');
							Log::error($ex);
						}
					
					}
					
				}
				catch (Exception $ex)
				{
					Log::error('Exception on - GroupBooking instances as instance');
					Log::error($ex);
				}
			}
			
		}	
		
		return $server_results;
	}




	/**
	 * Perform curl stuff
	 *
	 * @param   string  URL to call
	 * @param   string  HTTP Post Data
	 * @return  mixed   HTTP response body or PEAR Error Object
	 * @access	private
	 */
	function _curl ($url, $post_data) {
		/**
		 * Reuse the curl handle
		 */
		if (!is_resource($this->_ch)) {
			$this->_ch = curl_init();
			if (!$this->_ch || !is_resource($this->_ch)) {
				return 'Cannot initialise a new curl handle.';
			}

			curl_setopt ($this->_ch, CURLOPT_HEADER, 0);
			curl_setopt ($this->_ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt ($this->_ch, CURLOPT_SSL_VERIFYPEER,0);
			if ($this->curl_use_proxy) {
				curl_setopt ($this->_ch, CURLOPT_PROXY, $this->curl_proxy);
				curl_setopt ($this->_ch, CURLOPT_PROXYUSERPWD, $this->curl_proxyuserpwd);
			}

		}

		$this->_fp = tmpfile();
		
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_POST, 1);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($this->_ch, CURLOPT_FILE, $this->_fp);

		$status = curl_exec($this->_ch);
		$response['http_code'] = curl_getinfo($this->_ch, CURLINFO_HTTP_CODE);

		if (empty($response['http_code'])) {
			return 'No HTTP Status Code was returned.';
		} elseif ($response['http_code'] === 0) {
			return 'Cannot connect to the AussieSMS API Server.';
		}

		if ($status) {
			$response['error'] = curl_error($this->_ch);
			$response['errno'] = curl_errno($this->_ch);
		}

		rewind($this->_fp);

		$pairs = "";
		while ($str = fgets($this->_fp, 4096)) {
			$pairs .= $str;
		}
		fclose($this->_fp);

		$response['data'] = $pairs;
		unset($pairs);
		asort($response);

		return ($response);
	}

	
}

