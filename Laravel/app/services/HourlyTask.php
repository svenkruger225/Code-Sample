<?php namespace App\Services;

use Exception, log, File, CalendarService, SmsService, CourseRepeatService, EmailService, OrderService, Config, View;

class HourlyTask {

	public $date_to_run;
	public $options;
	
	public $messages;
	public $notrainers;
	public $repeats;
	public $balance;
	public $cleaning;
	public $open_orders;
	
	public function __construct($date_to_run = null, $options = array())
	{
		$this->date_to_run = $date_to_run;
		$this->options = $options;
		if (empty($date_to_run))
		{
			$tomorrow = new DateTime('tomorrow');
			$this->date_to_run = $tomorrow->format('Y-m-d');
		}
	}

	public function execute()
	{
		//php artisan dailytask:process --quiet --env=production

		Log::info('  Start Hourly task Process .');
		
		$this->open_orders = array();
		$this->open_orders = $this->processOpenOrders();
		
		//$this->sendAdminEmail();
		
		Log::info('  End Hourly Task Process.');
		
		
	}
	
	protected function processOpenOrders()
	{
		try 
		{
			Log::info('start processOpenOrders');
			$result = OrderService::updateOpenOrders();

			if (!isset($this->options['quiet']))
			{
				//$res = print_r($result);
				Log::info((array)$result);
			}
			
			Log::info('end processOpenOrders');

			return $result;
		}
		catch (Exception $ex)
		{
			Log::error($ex);
		} 
		
	}
	
	protected function sendAdminEmail()
	{
		try
		{
			$messages = array();
			$notrainers = array();
			$repeats = array();
			$balance = array();
			$cleaning = array();
			$open_orders = $this->open_orders;
			
			$result = json_decode(json_encode(array(
				'email' => Config::get('mail.admin_email', 'csouza@outlook.com.au'),
				'cc' => Config::get('mail.cc_daily_task', null),
				'first_name' => 'CoffeeSchool',
				'last_name' => 'Admin',
				'subject' => sprintf("Coffee School Hourly Tasks for: %s", date('d/m/Y')),
				'body' => View::make('backend.reports.dailytasks', compact('messages', 'notrainers', 'repeats','balance','cleaning','open_orders'))->render(),
				'attachments' => array()
				)));
			
			if (!isset($this->options['quiet']))
			{
				//$res = print_r($result);
				Log::info((array)$result);
			}
			
			EmailService::send($result);
			
			Log::info('end sendAdminEmail');
		}
		catch (Exception $e)
		{
			Log::error($e);
		}
	}
	
	
}
