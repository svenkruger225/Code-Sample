<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Message;
use EmailService,SmsService;

class UsiQueue {

	public function __construct()
	{
	}

	public function fire($job, $data) 
	{

		if (!empty($data['type']) && $data['type'] == 'EnrolmentData')
		{
			EmailService::sendEnrolmentDataMessage($data);
		}
		else if (!empty($data['type']) && $data['type'] == 'EnrolmentReminder')
		{
			\Log::debug("EnrolmentReminder: " . json_encode($data) );
			EmailService::sendEnrolmentDataReminder($data);
		}
		else if (!empty($data['type']) && $data['type'] == 'EnrolmentReminderSms')
		{
			\Log::debug("EnrolmentReminderSms: " . json_encode($data) );
			SmsService::sendEnrolmentDataReminder($data);
		}
		else
		{
			\Log::error("Wrong message type for: " . json_encode($data) );
		}

		$job->delete();		

	}
	

}