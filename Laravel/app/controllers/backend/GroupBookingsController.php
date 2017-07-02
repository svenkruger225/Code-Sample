<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use Config;
use Input;
use Lang;
use Redirect;
use Sentry;
use Validator;
use View;
use GroupBooking, Instructor, User, Course, Location;

class GroupBookingsController extends AdminController {

	/**
	 * GroupBooking Repository
	 *
	 * @var GroupBooking
	 */
	protected $group_booking;

	public function __construct(GroupBooking $group_booking)
	{
		parent::__construct();
		$this->group_booking = $group_booking;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$lid = Input::get('lid');
		$cid = Input::get('cid');
		$from = Input::get('from');  
		$to = Input::get('to');
		
		if( (!isset($lid) || empty($lid)) && (!isset($cid) || empty($cid)) && (!isset($from) || empty($from)) &&  (!isset($to) || empty($to)))
			$group_bookings = array();
		else
		{
			if(!isset($from) || empty($from))
				$from = date("Y-m-d");

			if(!isset($to) || empty($to))
				$to = date("Y-m-d", strtotime('+1 Week'));

			$group_bookings = GroupBooking::with('location', 'course', 'instructors')
				->fromLocation($lid)
				->forCourse($cid)
				->whereBetween('course_date', array($from, $to))
				->orderBy('location_id')
				->orderBy('course_date')
				->paginate(20);
		}
		
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');
		$locations = array('' => 'Select a Location') + Location::where('parent_id', '=', 0)->lists('name', 'id');
		Input::flash();
		return View::make('backend.groupinstances.index', compact('courses', 'locations', 'group_bookings'));
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$rules = array('cid' => 'required');
		$course_id = Input::get('cid');
		$input = Input::all();
		$validation = Validator::make($input, $rules);

		if ($validation->passes())
		{
			$selectedCourse = Course::find($course_id);
			
			$instructors = array('' => 'Select one or more Instructors') + 
							$selectedCourse->instructors()
							->orderBy('first_name')
							->orderBy('last_name')
							->select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))
							->lists('full_name', 'id');

			$course = Course::find($course_id)->first();
			$locations = array('' => 'Select a Location') + Location::lists('name', 'id');
			Input::flash();

			return View::make('backend.groupinstances.create', compact('instructors', 'course', 'locations'));
		}
		
		return Redirect::route('backend.groupinstances.index')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
		
		

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('instructor','lid','cid','from','to');
		$instructors = Input::get('instructor', array());
		
		$validation = Validator::make($input, GroupBooking::$rules);

		if ($validation->passes())
		{
			$instance = $this->group_booking->create($input);
			$instance->instructors()->sync($instructors);
			
			return Redirect::route('backend.groupinstances.index');
		}

		return Redirect::route('backend.groupinstances.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$group_booking = $this->group_booking->find($id);

		if (is_null($group_booking))
		{
			return Redirect::route('backend.groupinstances.index');
		}

		return View::make('backend.groupinstances.edit', compact('groupinstance'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$groupinstance = GroupBooking::with('instructors')->find($id);
		if (is_null($groupinstance))
		{
			return Redirect::route('backend.groupinstances.index');
		}

		$instInst = $groupinstance->instructors()->lists('id');

		$groupinstance->instructors = $instInst;
		
		$selectedCourse = Course::find($groupinstance->course_id);
		$instructors = array('' => 'Select one or more Instructors') + 
						$selectedCourse->instructors()
						->orderBy('first_name')
						->orderBy('last_name')
						->select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))
						->lists('full_name', 'id');
		
		$locations = array('' => 'Select a Location') + Location::lists('name', 'id');
		
		Input::flash();

		return View::make('backend.groupinstances.edit', compact('groupinstance', 'locations', 'instructors'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('instructor', '_method','lid','cid','from','to');
		$instructors = Input::get('instructor', array());
		$validation = Validator::make($input, GroupBooking::$rules);		

		if ($validation->passes())
		{
			$instance = $this->group_booking->find($id);
			$instance->update($input);
			$instance->instructors()->sync($instructors);
			Input::flash();

			return Redirect::route('backend.groupinstances.edit', $id)->with('success', 'Group Class updated successfully');
		}

		return Redirect::route('backend.groupinstances.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->group_booking->find($id)->delete();

		return Redirect::route('backend.groupinstances.index');
	}

}