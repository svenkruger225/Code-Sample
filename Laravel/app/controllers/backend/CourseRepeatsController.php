<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use Config,Input,Lang,Redirect,Sentry,Validator,View, Session;
use CourseRepeat, Course, Location, CourseRepeatService, SearchService;

class CourseRepeatsController extends AdminController {

	/**
	 * Courserepeat Repository
	 *
	 * @var Courserepeat
	 */
	protected $courserepeat;

	public function __construct(CourseRepeat $courserepeat)
	{
		parent::__construct();
		$this->courserepeat = $courserepeat;
		
		//if (count(Input::all()) == 0)
		//	Session::forget('_old_input');
		
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$courseRepeats = SearchService::ProcessCourseRepeatSearch();		
		
		$locations = array('' => 'Select Location');
		$locs = Location::with('children')->where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->get();
		foreach ($locs as $location)
		{
			$group = array();
			$group = array_add($group, $location->id , $location->name);
			foreach ($location->children as $loc)
			{	
				$group = array_add($group, $loc->id , $loc->name);
			}
			$locations = array_add($locations, $location->name, $group);
		}
		$courses = array('' => 'Select Course') + Course::remember(Config::get('cache.minutes', 1))->lists('name', 'id');


		return View::make('backend.courserepeats.index', compact('locations', 'courses','courseRepeats'));
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');
		$locations = array('' => 'Select Location');
		$locs = Location::with('children')->where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->get();
		foreach ($locs as $location)
		{
			$group = array();
			$group = array_add($group, $location->id , $location->name);
			foreach ($location->children as $loc)
			{	
				$group = array_add($group, $loc->id , $loc->name);
			}
			$locations = array_add($locations, $location->name, $group);
		}
		$course_times = Config::get('utils.course_times', array());
		Session::reflash();
		return View::make('backend.courserepeats.create', compact('courses', 'locations', 'course_times'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$input['end_date'] = $input['end_date'] == '' ? null : $input['end_date'];
		$input['maximum_students'] += 0;
		$input['maximum_alert'] += 0;
		$input['time_start'] = date ('H:i:s',strtotime($input['time_start']));
		$input['time_end'] = date ('H:i:s',strtotime($input['time_end']));
		$validation = Validator::make($input, Courserepeat::$rules);

		Session::reflash();
		if ($validation->passes())
		{
			$courserepeat = $this->courserepeat->create($input);
			CourseRepeatService::Update($courserepeat);
			
			return Redirect::route('backend.courserepeats.index')->with('success', 'Course Repeat Added and processed');
		}

		return Redirect::route('backend.courserepeats.create')
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
			return Redirect::route('backend.courserepeats.index');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');
		$locations = array('' => 'Select Location');
		$locs = Location::with('children')->where('parent_id', 0)->remember(Config::get('cache.minutes', 1))->get();
		foreach ($locs as $location)
		{
			$group = array();
			$group = array_add($group, $location->id , $location->name);
			foreach ($location->children as $loc)
			{	
				$group = array_add($group, $loc->id , $loc->name);
			}
			$locations = array_add($locations, $location->name, $group);
		}
		$course_times = Config::get('utils.course_times', array());
		Session::reflash();
		
		$courserepeat = $this->courserepeat->find($id);

		if (is_null($courserepeat))
		{
			return Redirect::route('backend.courserepeats.index')->with('error', "Couldn't find Course Repeat.");
		}
		
		$courserepeat->time_start = date ('h:i A',strtotime($courserepeat->time_start));
		$courserepeat->time_end = date ('h:i A',strtotime($courserepeat->time_end));
		
		//var_dump($courserepeat);
		//exit();

		return View::make('backend.courserepeats.edit', compact('courserepeat', 'courses', 'locations', 'course_times'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$input['end_date'] = $input['end_date'] == '' ? null : $input['end_date'];
		$input['maximum_students'] += 0;
		$input['maximum_alert'] += 0;
		$input['time_start'] = date ('H:i:s',strtotime($input['time_start']));
		$input['time_end'] = date ('H:i:s',strtotime($input['time_end']));
		
		$validation = Validator::make($input, Courserepeat::$rules);

		Session::reflash();
		if ($validation->passes())
		{
			$courserepeat = $this->courserepeat->find($id);
			if(CourseRepeatService::Update($courserepeat))
			{
				$courserepeat->update($input);
				return Redirect::route('backend.courserepeats.index')->with('success', 'Course Repeat Updated and processed');
			}
			else
			{
				return Redirect::route('backend.courserepeats.index')->with('error', 'Problem updating repeat');
			}

		}

		return Redirect::route('backend.courserepeats.edit', $id)
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
		$courserepeat = $this->courserepeat->find($id);
		if (CourseRepeatService::Delete($courserepeat))
			$courserepeat->delete();
		Session::reflash();

		return Redirect::route('backend.courserepeats.index')->with('success', 'Course Repeat Deleted');
	}

}