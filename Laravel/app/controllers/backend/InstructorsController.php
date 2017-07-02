<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Config;
use Input;
use Lang;
use Redirect;
use Sentry;
use Validator;
use View;
use Instructor, User, Course, CalendarService, Location, GroupBooking, CourseInstance;

class InstructorsController extends AdminController {

	/**
	 * Instructor Repository
	 *
	 * @var Instructor
	 */
	protected $instructor;

	public function __construct(Instructor $instructor)
	{
		parent::__construct();
		$this->instructor = $instructor;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('instructor_search');
		$c_id = Input::get('instructor_course_id');  
		$state = Input::get('instructor_state');

		$query = $this->instructor->with('courses')
			->join('users_groups', 'users.id', '=', 'users_groups.user_id')
			->where('users_groups.group_id',3);
		
		if(	$search && $search != '')
			$query = $query
				->where('users.first_name', 'like', '%' . $search . '%')
				->orWhere('users.last_name', 'like', '%' . $search . '%');
		
		if(	!empty($c_id))
			$query = $query->trainerForCourse($c_id);
		
		if(	!empty($state))
			$query = $query->trainerFromState($state);
		

		$instructors = $query
			->paginate(15)
			->appends(array(
					'instructor_search' => $search,
					'instructor_course_id' => $c_id,
					'instructor_state' => $state
			));
	
		$courses = array('' => 'Select a Course Type') + Course::lists('name', 'id');
		$states = array(''=>'Selected a State','NSW'=>'NSW','NT'=>'NT','QLD'=>'QLD','SA'=>'SA','TAS'=>'TAS','VIC'=>'VIC','WA'=>'WA');
		Input::flash();

		return View::make('backend.instructors.index', compact('courses', 'states', 'instructors'));
	}

	public function roster()
	{

		$result = CalendarService::GetMonthClasses();	

		$courses = array('' => 'Select a Course Type') + Course::lists('name', 'id');
		$states = array(''=>'Selected a State','NSW'=>'NSW','NT'=>'NT','QLD'=>'QLD','SA'=>'SA','TAS'=>'TAS','VIC'=>'VIC','WA'=>'WA');
		$months = array(''=> 'Select Month');
		for ($x=1;$x<=12;$x++){  $months += array($x => date( 'F', mktime(0, 0, 0, $x, 1))); } 
		$years = array(''=> 'Select Year');
		for ($year=2014;$year<=2030;$year++){  $years += array($year => $year); } 
		$locations = array('' => 'All Locations','onsite' => 'On Site') + Location::where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->lists('name', 'id');		
		$courses = array('' => 'Courses: All Types','0' => 'Group Course Bookings') + Course::where('active', 1)->remember(Config::get('cache.minutes', 1))->lists('name', 'id');		
		Input::flash();

		return View::make('backend.instructors.roster', compact('locations','courses','months', 'years', 'courses', 'states', 'result'));
	}
	
	public function updtrainers()
	{
		try
		{
			$type = Input::get('type', null);
			$instance = Input::get('instance', null);
			$instructors = array_filter(Input::get('instructor', array()));
			if (!empty($instance) && count($instructors) > 0)
			{
				$class = $type == 'Group' ? 'GroupBooking' : 'CourseInstance';
				
				$instance = $class::find($instance);
				$instance->instructors()->sync($instructors);
				return Redirect::back()->with('success', 'Class trainers updated successfully');
			}
			else
			{
				return Redirect::back()->with('error', 'Make a selection first');
			}
			
		}
		catch (Exception $ex)
		{
			return Redirect::back()->with('error', $ex->getMessage());
		}
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//$users = array('' => 'Select User') + User::select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))->lists('full_name', 'id');
		$courses = array('' => 'Select one or more Course Type') + Course::lists('name', 'id');
		$instructor = $this->instructor->find($id);
		
		$instructorCourses = $instructor->courses()->lists('course_id');
		$instructor->courses = $instructorCourses;

		if (is_null($instructor))
		{
			return Redirect::route('backend.instructors.index');
		}
		
		$location = Location::where('name', 'LIKE', '%' . $instructor->business_city . '%')->where('state', $instructor->business_state)->first();
		if ($location)
			$instructor->location_id = $location->id;
		
		Input::flash();
		//$states = array(''=>'Selected a State','NSW'=>'NSW','NT'=>'NT','QLD'=>'QLD','SA'=>'SA','TAS'=>'TAS','VIC'=>'VIC','WA'=>'WA');
		$locations = array('' => 'Select a Location') + Location::where('parent_id', 0)->lists('name', 'id');

		return View::make('backend.instructors.edit', compact('instructor', 'locations', 'courses'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{

		try 
		{	        
			$input = Input::except('courses', '_method');
			
			$location = Location::find($input['location_id']);
			if ($location)
			{
				$input['business_city'] = $location->name;
				$input['business_state'] = $location->state;
				unset($input['location_id']);
			}
	
			$courses = Input::get('courses', array());
			$validation = Validator::make($input, Instructor::$rules);

			if ($validation->passes())
			{
				$instructor = $this->instructor->find($id);
				$instructor->update($input);
				$instructor->courses()->sync($courses);

				Redirect::route('backend.instructors.edit', $id)->with('success', 'Instructor updated successfully');
			}
			Input::flash();

			return Redirect::route('backend.instructors.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
		}
		catch (Exception $ex )
		{
			return Redirect::route('backend.users.edit', $id)->with('error', $ex->getMessage());
		}

	}

	public function destroy($id = null)
	{
		try
		{
			// Get user information
			$user = Sentry::getUserProvider()->findById($id);

			// Check if we are not trying to delete ourselves
			if ($user->id === Sentry::getId())
			{
				// Prepare the error message
				$error = Lang::get('backend/users/message.error.delete');

				// Redirect to the user management page
				return Redirect::route('instructors')->with('error', $error);
			}

			// Do we have permission to delete this user?
			if ($user->isSuperUser() and ! Sentry::getUser()->isSuperUser())
			{
				// Redirect to the user management page
				return Redirect::route('backend.instructors.index')->with('error', 'Insufficient permissions!');
			}

			// Delete the user
			$user->delete();

			// Prepare the success message
			$success = Lang::get('backend/users/message.success.delete');

			// Redirect to the user management page
			return Redirect::route('backend.instructors.index')->with('success', $success);
		}
		catch (UserNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('backend/users/message.user_not_found', compact('id' ));

			// Redirect to the user management page
			return Redirect::route('backend.instructors.index')->with('error', $error);
		}
	}


	public function restore($id = null)
	{
		try
		{
			// Get user information
			$user = Sentry::getUserProvider()->createModel()->withTrashed()->find($id);

			// Restore the user
			$user->restore();

			// Prepare the success message
			$success = Lang::get('backend/users/message.success.restored');

			// Redirect to the user management page
			return Redirect::route('backend.instructors.index')->with('success', $success);
		}
		catch (UserNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('backend/users/message.user_not_found', compact('id'));

			// Redirect to the user management page
			return Redirect::route('backend.instructors.index')->with('error', $error);
		}
	}


}