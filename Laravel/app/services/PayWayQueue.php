<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Message;
use OrderService;

class PayWayQueue {

	public function __construct()
	{
	}

	public function fire($job, $data) 
	{

		if(OrderService::processPayWayServerResponse($data))
		{
			$job->delete();		
		}	

	}

}