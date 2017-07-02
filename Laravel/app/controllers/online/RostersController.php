<?php namespace Controllers\Online;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config;
use Input;
use Lang;
use Controller, Response;
use Redirect;
use Sentry;
use Validator;
use View;
use OnlineCourse, OnlineService;

class RostersController extends AdminController {

	public function __construct()
	{
		// Call parent
		parent::__construct();
	}

	public function index()
	{

		$courses = \OnlineCourse::where('type', 'Online')
			->where('active', 1)
			->orderBy('order')
			->remember(Config::get('cache.minutes', 1))
			->get();
		return View::make('online/backend/calendar/index', compact('courses'));
	}

	public function displayProgress($roster_id)
	{

		$roster = \OnlineRoster::find($roster_id);
			
		return View::make('online/backend/calendar/history', compact('roster'));
	}

	

}