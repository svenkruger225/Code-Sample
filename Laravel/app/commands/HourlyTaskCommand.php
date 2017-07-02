<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Exception;
use Illuminate\Log\Monolog\Logger;
use Illuminate\Cache\FileStore;
use App\Services\HourlyTask;

class HourlyTaskCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'hourlytask:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Coffee School Hourly Tasks.';
	
	protected $messages;
	protected $notrainers;
	protected $repeats;
	protected $balance;
	protected $cleaning;
	

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//php artisan hourlytask:process --quiet --env=production

		$this->comment('=====================================');
		$this->info('  Start Hourly task Process reminders.');
		$this->comment('-------------------------------------');
		$tomorrow = new \DateTime('tomorrow');
		$date_to_run = $tomorrow->format('Y-m-d');
		$options = array();
		if ($this->option('quiet'))
		{
			$options = array('quiet'=>'1');
		}
		
		$hourly_task = new \App\Services\HourlyTask($date_to_run, $options);
		$hourly_task->execute();

		$this->comment('-------------------------------------');
		$this->info('  End Process reminders.' . $date_to_run);
		$this->comment('=====================================');

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
			);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
			);
	}

}
