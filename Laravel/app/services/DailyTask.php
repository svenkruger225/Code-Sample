<?php namespace App\Services;

use Exception, log, File, CalendarService, SmsService, CourseRepeatService, EmailService, OrderService, Config, View;

class DailyTask {

	public $date_to_run;
	public $options;
	
	public $messages;
	public $notrainers;
	public $repeats;
	public $balance;
	public $cleaning;
	
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

		Log::info('=====================================');
		Log::info('  Start Daily task Process .');
		Log::info('-------------------------------------');
		
		$this->messages = array();
		$this->notrainers = array();
		$this->repeats = array();
		
		
		$this->notrainers = $this->getClassesWithoutTrainer();
		
		$this->repeats = $this->runCourseRepeats();
		
		$this->balance = $this->getSmsBalance();
		
		$this->cleaning = $this->clearLogFiles();

		$this->messages = $this->sendSmsMessages();

		$this->sendAdminEmail();
		
		Log::info('-------------------------------------');
		Log::info('  End Daily Task Process.');
		Log::info('=====================================');
		
		
	}

	protected function getClassesWithoutTrainer()
	{
		try
		{
			Log::info('start getClassesWithoutTrainer');
			$result = CalendarService::getClassesWithoutTrainer();

			Log::info('');
			if (!isset($this->options['quiet']))
			{
				//$res = print_r($result);
				Log::info((array)$result);
			}
			Log::info('end getClassesWithoutTrainer');
			
			return $result;
		}
		catch (Exception $e)
		{
			Log::error($e);
		}
	}
	
	protected function getSmsBalance()
	{
		try
		{
			Log::info('start getSmsBalance');
			$result = SmsService::getbalance();

			Log::info(sprintf('Balance is: %s', $result));
			Log::info('end getSmsBalance');

			return $result;
		}
		catch (Exception $e)
		{
			Log::error($e);
		}
	}
	
	protected function sendSmsMessages()
	{
		try
		{
			Log::info('start sendSmsMessages');

			$result = SmsService::SendRemiderSms($this->date_to_run);

			if (!isset($this->options['quiet']))
			{
				//$res = print_r($result);
				Log::info((array)$result);
			}
			Log::info('Sms messages sent successfully.');
			
			Log::info('end sendSmsMessages');
			
			return $result;
		}
		catch (Exception $e)
		{
			Log::error('Exception on - sendSmsMessages');
			Log::error($e);
		}
	}
	
	protected function runCourseRepeats()
	{
		try
		{
			Log::info('start runCourseRepeats');
			
			$result = CourseRepeatService::RunAll();

			Log::info('Course Repeats Processed.');
			
			Log::info('end runCourseRepeats');
			
			return $result;
		}
		catch (Exception $e)
		{
			Log::error($e);
		}
	}
	
	protected function clearLogFiles()
	{
		try 
		{
			Log::info('start clearLogFiles');
			$result = 0;
			$files = glob(storage_path() . "/logs/*");
			$interval = strtotime('-1 month');//files older than 1 month
			foreach($files as $file) 
			{
				if(is_file($file) && filemtime($file) <= $interval) 
				{ 
					Log::info($file);
					if (unlink($file))
						$result++;
				}
			}
			
			Log::info('end clearLogFiles');

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
			$messages = $this->messages;
			$notrainers = $this->notrainers;
			$repeats = $this->repeats;
			$balance = $this->balance;
			$cleaning = $this->cleaning;
			$open_orders = array();
			
			$result = json_decode(json_encode(array(
				'email' => Config::get('mail.admin_email', 'csouza@outlook.com.au'),
				'cc' => Config::get('mail.cc_daily_task', null),
				'first_name' => 'CoffeeSchool',
				'last_name' => 'Admin',
				'subject' => sprintf("Coffee School Daily Tasks for: %s", date('d/m/Y')),
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
