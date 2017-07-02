<?php namespace Controllers\Account;

use AuthorizedController;
use Input;
use Redirect;
use Sentry;
use Validator;
use View;

class ProfileController extends AuthorizedController {

	public function __construct()
	{
		$this->beforeFilter('student-auth');
		parent::__construct();
	}
	
	public function getIndex()
	{
		// Get the user information
		$user = Sentry::getUser();

		// Show the page
		return View::make('backend/account/profile', compact('user'));
	}
	
	public function getOnlineProfile()
	{
		$keys = array('disabilities', 'achievements');
		
		$this->data = \OnlineService::InitialiseContentData();	
		$course_id = null;
		
		if ($this->data->student->onlinerosters->count() > 0)
		{
			$course_id = $this->data->student->onlinerosters->first()->course_id;
		}
		
		$this->data = \OnlineService::GetCommonDataForGivenStep($course_id, null, null, $this->data);
		
		if (is_null($this->data->student))
		{
			return Redirect::route('backend.customers.index');
		}
		
		foreach($keys as $key)
		{
			$this->data->student->$key = json_decode($this->data->student->$key, true);
		}
		
		$disabilities = array();
		if ($this->data->student->disabilities)
		foreach ($this->data->student->disabilities as $disability) 
		{   
			if (strpos( $disability, 'other__' ) !== false ) 
			{
				$this->data->student->disabilities_other = str_replace('other__', '', $disability);
			}
			else 
			{
				array_push($disabilities, $disability);
			}
		}
		$this->data->student->disabilities = $disabilities;

		if (!$this->data->student->achievements)
		$this->data->student->achievements = array();
		
		$data = $this->data;

		// Show the page
		$titles = array('' => 'Select Title') + \Config::get('utils.titles', array());;
		$languages = array('' => 'Select Language') + \AvetmissLanguage::orderBy('name')->lists('name','id');
		$countries = array('' => 'Select Country') + \AvetmissCountry::orderBy('name')->lists('name','id');
		$states = array('' => 'Select State') + \AvetmissState::orderBy('name')->lists('name','id');
		$achievements_list = \AvetmissAchievement::orderBy('id')->get(array('id','name'))->toArray();
		$disabilities_list = \AvetmissDisability::orderBy('id')->get(array('id','name'))->toArray();
		$study_reasons_list = \AvetmissStudyReason::orderBy('id')->get(array('id','name'))->toArray();
		$usi_visa_issue_countries = array('' => 'Select Country') + \UsiVisaIssueCountry::orderBy('name')->lists('name','id');
		return View::make('backend.account.online-profile', compact('data', 'titles', 'types', 'languages','countries','states','achievements_list','disabilities_list','study_reasons_list', 'usi_visa_issue_countries'));
	}

	public function postOnlineProfile()
	{
		try {
			$input = Input::all();
			
			$validation = Validator::make($input, \Customer::$rules);
			if ($validation->passes())
			{
				\CustomerService::CreateUpdateCustomer();

				return Redirect::route('online.profile')->with('success', 'Profile successfully updated');
			}

			return Redirect::route('online.profile')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
		
		}
		catch(Exception $ex)
		{
		// Redirect to the settings page
			return Redirect::route('online.profile')->with('error', 'Unable to update profile');
		}
	}

	public function postIndex()
	{
		// Declare the rules for the form validation
		$rules = array(
			'username' => 'required|min:3',
			'first_name' => 'required|min:3',
			'last_name'  => 'required|min:3',
			'website'    => 'url',
			'gravatar'   => 'email',
			);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		// Grab the user
		$user = Sentry::getUser();

		// Update the user information
		$user->username = Input::get('username');
		$user->first_name = Input::get('first_name');
		$user->last_name  = Input::get('last_name');
		$user->website    = Input::get('website');
		$user->country    = Input::get('country');
		$user->gravatar   = Input::get('gravatar');
		$user->save();

		// Redirect to the settings page
		return Redirect::route('profile')->with('success', 'Account successfully updated');
	}

}
