<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response;
use Agent, Location, Course, CourseInstance, Order, Item, Invoice, Customer, DB, Status, Voucher, User;
use ReportsService, Utils, PaymentMethod, CSV;

class ReportsController extends AdminController {

	protected $is_group_booking;
		
	public function __construct()
	{
		$this->beforeFilter('super-auth');
		parent::__construct();
	}
	
	public function dashboard() 
	{
		return View::make('backend.reports.dashboard');
	}
	
	public function agent($csv = null) 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$result = ReportsService::GetAgentTotals($csv);
		
		$agents = array('' => 'Select Agent') + \Agent::lists('name','id');
		$order_types = Config::get('utils.order_types', array());
		
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->lists('name', 'id');
		$courses = array('' => 'All Course Types') + Course::where('active', 1)->lists('name', 'id');		


		Input::flash();
		if ($csv)
		{
			$filename = 'agents-commissions-' . date('Y-m-d') . '.csv';
			return \CSV::fromArray($result)->stream( $filename);
		}
		else
		{
			return View::make('backend.reports.agent', compact('agents','locations', 'courses', 'result', 'start'));
		}		
		
	}	
	public function financialentries() 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$items = ReportsService::GetFinancialTotalsEntries();
		
		$order_types = Config::get('utils.order_types', array());
		
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->lists('name', 'id');
		$courses = array('' => 'All Course Types') + Course::where('active', 1)->lists('name', 'id');		


		Input::flash();
		return View::make('backend.reports.financialentries', compact('order_types','locations', 'courses', 'items', 'start'));
	}
	
	public function financial() 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$result = ReportsService::GetFinancialTotals();
		
		$order_types = Config::get('utils.order_types', array());
		
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->lists('name', 'id');
		$courses = array('' => 'All Course Types') + Course::where('active', 1)->lists('name', 'id');		


		Input::flash();
		return View::make('backend.reports.financial', compact('order_types','locations', 'courses', 'result', 'start'));
	}
	
	public function owing_info($owing_date) 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$items = ReportsService::GetFinancialTotalsEntries($owing_date);
		
		$order_types = Config::get('utils.order_types', array());
		
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->lists('name', 'id');
		$courses = array('' => 'All Course Types') + Course::where('active', 1)->lists('name', 'id');		


		Input::flash();
		return View::make('backend.reports.financialentries', compact('order_types','locations', 'courses', 'items', 'start'));
	}
	
	public function transactions() 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$result = ReportsService::GetFinancialTransactions();
		
		$order_types = Config::get('utils.order_types', array());
		
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->lists('name', 'id');
		$courses = array('' => 'All Course Types') + Course::where('active', 1)->lists('name', 'id');		
		$methods = array('' => 'All Payment Methods') + PaymentMethod ::lists('name', 'id');		


		Input::flash();
		return View::make('backend.reports.transactions', compact('order_types','locations', 'courses', 'methods', 'result', 'start'));
	}
	
	public function staff_financial() 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$result = ReportsService::GetStaffFinancial();
		$order_types = Config::get('utils.order_types', array());

		Input::flash();
		return View::make('backend.reports.staff_financial', compact('order_types','result', 'start'));
	}
	
	public function staff_sales() 
	{
		$start = Utils::getmicrotime();
		$result = array();
		
		$result = ReportsService::GetStaffSales();
		$order_types = Config::get('utils.order_types', array());

		Input::flash();
		return View::make('backend.reports.staff_sales', compact('order_types','result', 'start'));
	}

	public function trainerrosters()
	{
		$result = array();
		
		$result = ReportsService::GetTrainerRosters();
		
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->lists('name', 'id');
		$courses = array('' => 'All Course Types') + Course::where('active', 1)->lists('name', 'id');		
		$trainers = array('' => 'All') + User::instructors()->sortBy(function($user){return $user->first_name;})->lists('name','id');		
		

		Input::flash();
		return View::make('backend.reports.trainerrosters', compact('trainers','locations', 'courses', 'result'));
	}
	
	public function exportmyob() 
	{
		$start = Utils::getmicrotime();
		$results = array();
		
		$results = ReportsService::ExportMyob();
		
		//$months = Config::get('utils.months', array());
		//$months = array(''=> 'Select Month');
		//for ($x=1;$x<=12;$x++){  $months += array($x => date( 'F', mktime(0, 0, 0, $x, 1))); } 
		$types = Config::get('utils.report_types', array());
		
		//$years = array(''=> 'Select Year');
		//for ($year=2013;$year<=2030;$year++){  $years += array($year => $year); } 


		Input::flash();
		if (count($results) > 0)
		{
			$from_date = Input::get('from_date');
			$to_date = Input::get('to_date');
			$single_date = Input::get('single_date');
			$report_type = Input::get('report_type');
			if (!empty($single_date))
			{
				$from_date = $single_date;
				$to_date = $single_date;
			}

			$filename = "myob-$report_type-$from_date-to-$to_date-" . date('Y-m-d-h-i') . '.csv';
			$path = storage_path() . '/reports/';

			return CSV::fromArray($results)->put($path . $filename)->stream( $filename);
			
			//return Redirect::back();
		}
		
		$files = glob(storage_path() . '/reports/*.csv');
		usort($files, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});		
		
		$reports = array();
		foreach($files as $file)
		{
			$reports[]  = array('path'=>$file, 'name'=>basename($file), 'size'=>Utils::human_filesize(filesize($file)), 'date'=>date ("d/m/Y H:i:s", filemtime($file)));
		}
		
		
		return View::make('backend.reports.myob', compact('types', 'start', 'reports'));
	}
	
	public function downloadmyob() 
	{
		$path = Input::get('path');
		return CSV::fromFile($path)->stream( basename($path));
	}
	
	public function removemyob() 
	{
		$path = Input::get('path');
		if (file_exists($path))
		{	
			unlink($path);
			return Redirect::back()->with('success', 'File deleted');
		}
		return Redirect::back()->with('error', 'Could not find the selected file');
	}

}