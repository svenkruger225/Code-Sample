<?php namespace App\Services;

use Exception, log, File, CustomerService, UsiService, SmsService, EmailService, Config, View, DateTime;

class UsiTask {

	public $start_date;
	public $end_date;
	public $options;
	
	public $messages;
	public $avetmiss_usi_missing;
	
	public function __construct($start_date = null, $end_date = null, $options = array())
	{
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->options = $options;
		if (empty($start_date))
			$this->start_date = new DateTime('tomorrow');
		
		if (empty($end_date))
			$this->end_date = clone $this->start_date;
		
	}

	public function execute()
	{
		//php artisan dailytask:process --quiet --env=production

		Log::info('=====================================');
		Log::info('  Start USI task Process . start ' . $this->start_date->format('d/m/Y') . ', end: ' . $this->end_date->format('d/m/Y'));
		Log::info('-------------------------------------');
		
		$this->messages = array();
		
		$this->avetmiss_usi_missing = $this->getStudentsWithAvetmissUsiMissing($this->start_date, $this->end_date );
		//$this->avetmiss_usi_missing = $this->getStudentsWithAvetmissUsiMissing();

		$this->sendAdminEmail();
		
		Log::info('-------------------------------------');
		Log::info('  End Usi Task Process.');
		Log::info('=====================================');
		
		
	}

	protected function getStudentsWithAvetmissUsiMissing($start_date, $end_date)
	{
		try
		{
			Log::info('start getStudentsWithAvetmissUsiMissing');
			$result = CustomerService::GetStudentsWithAvetmissUsiMissing($start_date, $end_date);

			Log::info('');
			if (!isset($this->options['quiet']))
			{
				//$res = print_r($result);
				Log::info( " emails and sms have being placed in a queue to be sent");
			}
			Log::info('end getStudentsWithAvetmissUsiMissing');
			
			return $result;
		}
		catch (Exception $e)
		{
			Log::error($e);
		}
	}
	
	protected function sendAdminEmail()
	{
		try
		{
			$messages = $this->avetmiss_usi_missing;
			
			Log::info($messages);

			
			$result = json_decode(json_encode(array(
				'email' => Config::get('mail.admin_email', 'csouza@outlook.com.au'),
				'cc' => Config::get('mail.cc_daily_task', null),
				'first_name' => 'CoffeeSchool USI',
				'last_name' => 'Admin',
				'subject' => sprintf("Coffee School USI Daily Tasks for: %s to %s", $this->start_date->format('d/m/Y'), $this->end_date->format('d/m/Y')),
				'body' => View::make('backend.reports.usidailytasks', compact('messages'))->render(),
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
