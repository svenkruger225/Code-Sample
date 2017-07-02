<?php namespace Controllers\Backend;

use AdminController;
use View, DateTime, Redirect, Input, Utils;
use App\Services\DailyTask;
use App\Services\UsiTask;
use App\Services\HourlyTask;

class DailyTaskController extends AdminController {

	protected $input;
	
	public function __construct()
	{
		$this->input = Input::all();
		parent::__construct();

	}

	public function index()
	{
		// Show the page
		$start = Utils::getmicrotime();
		$results = array();
		return View::make('backend/dailytasks/index', compact('results', 'start'));
	}

	public function runTask()
	{
		if (!isset($this->input['from_date']) && isset($this->input['to_date']))
		$this->input['from_date'] = new \DateTime($this->input['to_date']);
		
		if (!isset($this->input['from_date']))
			$this->input['from_date'] = null;
		else
			$this->input['from_date'] = new \DateTime($this->input['from_date']);

		if (!isset($this->input['to_date']))
			$this->input['to_date'] = null;
		else
			$this->input['to_date'] = new \DateTime($this->input['to_date']);

		if (isset($this->input['single_date']) && $this->input['single_date'] != '')
		{
			$this->input['from_date'] = new \DateTime($this->input['single_date']);
			$this->input['to_date'] = new \DateTime($this->input['single_date']);
		}
		if (empty($this->input['report_type'])) $this->input['report_type'] = '';


		Input::flash();


		if ($this->input['report_type'] == 'DailyTasks')
		{
			return $this->runDailyTasks($this->input['from_date'], $this->input['to_date']);
		}
		else if ($this->input['report_type'] == 'HourlyTask')
		{
			return $this->runHourlyTasks($this->input['from_date'], $this->input['to_date']);
		}
		else if ($this->input['report_type'] == 'UsiTask')
		{
			return $this->runUsiTask($this->input['from_date'], $this->input['to_date']);
		}

		\Log::info(json_encode($this->input));

		$results = array();

		return Redirect::route('backend.dailytasks')->with('error', 'Must select task');
		
	}

	public function runDailyTasks($start_date = null, $end_date = null)
	{
		$start = Utils::getmicrotime();
		$results = array();
		$tomorrow = new DateTime('tomorrow');
		$date_to_run = $tomorrow->format('Y-m-d');
		$daily_task = new DailyTask($date_to_run);
		$daily_task->execute();

		$results['type'] = 'Daily';
		$results['messages'] = $daily_task->messages;
		$results['notrainers'] = $daily_task->notrainers;
		$results['repeats'] = $daily_task->repeats;
		$results['balance'] = $daily_task->balance;
		$results['cleaning'] = $daily_task->cleaning;
		
		return View::make('backend/dailytasks/index', compact('results', 'start'));
		
	}

	public function runUsiTask($start_date = null, $end_date = null)
	{
		$start = Utils::getmicrotime();
		$results = array();
		$daily_task = new UsiTask($start_date, $end_date);
		$daily_task->execute();

		$results['type'] = 'Usi';
		$results['messages'] = $daily_task->avetmiss_usi_missing;
		//\Log::info(json_encode($results));
		return View::make('backend/dailytasks/index', compact('results', 'start'));
		
	}

	public function runHourlyTasks($start_date = null, $end_date = null)
	{
		$start = Utils::getmicrotime();
		$results = array();
		$tomorrow = new DateTime('tomorrow');
		$date_to_run = $tomorrow->format('Y-m-d');
		$daily_task = new HourlyTask($date_to_run);
		$daily_task->execute();

		$results['type'] = 'Hourly';
		$results['open_orders'] = $daily_task->open_orders;
		
		return View::make('backend/dailytasks/index', compact('results', 'start'));
		
	}

}
