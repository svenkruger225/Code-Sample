<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Exception;
use Illuminate\Log\Monolog\Logger;
use Illuminate\Cache\FileStore;
use App\Services\UsiTask;

class UsiTaskCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'usitask:process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Coffee School Usi Tasks.';
	
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
		$this->info('  Start Usi task Process .');
		$this->comment('-------------------------------------');
		
		$start = null;
		$end = null;

		if ($this->argument('start'))
			$start = $this->argument('start');
		if ($this->argument('end'))
			$end = $this->argument('end');
		
		
		$start_date = $start && !empty($start) ? new \DateTime($start) : new \DateTime('tomorrow');
		$date_to_run = $start_date->format('Y-m-d');
		$end_date = $end && !empty($end) ? new \DateTime($end) : clone $start_date;
		$options = array();
		if ($this->option('quiet'))
		{
			$options = array('quiet'=>'1');
		}
		
		$daily_task = new \App\Services\UsiTask($start_date, $end_date, $options);
		$daily_task->execute();

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
			array('start', InputArgument::OPTIONAL, 'Start Date', null),
			array('end', InputArgument::OPTIONAL, 'end date', null)
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
