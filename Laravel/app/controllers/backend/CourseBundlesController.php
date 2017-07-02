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
use View, Session, SearchService;
use CourseBundle, Course, Location;

class CourseBundlesController extends AdminController {

	/**
	 * Coursebundle Repository
	 *
	 * @var Coursebundle
	 */
	protected $coursebundle;

	public function __construct(CourseBundle $coursebundle)
	{
		parent::__construct();
		$this->coursebundle = $coursebundle;
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

		$coursebundles = SearchService::ProcessCourseBundleSearch();		
		
		$courses = array('' => 'Select a Course Type') + Course::lists('name', 'id');
		$locations = array('' => 'Select Location');
		$locs = Location::where('parent_id', 0)->get();
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

		return View::make('backend.coursebundles.index', compact('locations', 'courses', 'coursebundles'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');
		$locations = array('' => 'Select Location:') + Location::lists('name', 'id');
		Session::reflash();
		return View::make('backend.coursebundles.create', compact('courses', 'locations'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('course_id','price_online','price_offline','act');
		
		$input['date_from'] = $input['date_from']  == '' || $input['date_from']  == '0000-00-00' ? null : $input['date_from'];
		$input['date_to'] = $input['date_to']  == ''  || $input['date_to']  == '0000-00-00'? null : $input['date_to'];
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
			
		$course_data = Input::only('course_id','price_online','price_offline','act');
		$bundle_courses= array();
		foreach($course_data['course_id'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['course_id'] = $course_data['course_id'][$index];
			$obj['price_online'] = $course_data['price_online'][$index];
			$obj['price_offline'] = $course_data['price_offline'][$index];
			$obj['active'] = 1;
			array_push($bundle_courses, $obj);
		}

		$validation = Validator::make($input, Coursebundle::$rules);
		Session::reflash();

		if ($validation->passes())
		{
			$coursebundle = $this->coursebundle->create($input);
			foreach ($bundle_courses as $data)
			{
				$course_id = $data['course_id'];
				unset($data['course_id']);
				$coursebundle->bundles()->attach($course_id, $data );
			}
			return Redirect::route('backend.coursebundles.index');
		}

		return Redirect::route('backend.coursebundles.create')
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
		$coursebundle = $this->coursebundle->findOrFail($id);

		return View::make('backend.coursebundles.show', compact('coursebundle'));
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
		$locations = array('' => 'Select Location:') + Location::lists('name', 'id');
		
		$coursebundle = CourseBundle::with('bundles')->find($id);
		Session::reflash();

		if (is_null($coursebundle))
		{
			return Redirect::route('backend.coursebundles.index');
		}

		return View::make('backend.coursebundles.edit', compact('coursebundle', 'courses', 'locations'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('course_id','price_online','price_offline','act', '_method');
		
		$input['date_from'] = $input['date_from']  == '' || $input['date_from']  == '0000-00-00' ? null : $input['date_from'];
		$input['date_to'] = $input['date_to']  == ''  || $input['date_to']  == '0000-00-00'? null : $input['date_to'];
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		
		$course_data = Input::only('course_id','price_online','price_offline','act');
		$bundle_courses= array();
		foreach($course_data['course_id'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['course_id'] = $course_data['course_id'][$index];
			$obj['price_online'] = $course_data['price_online'][$index];
			$obj['price_offline'] = $course_data['price_offline'][$index];
			$obj['active'] = 1;
			array_push($bundle_courses, $obj);
		}

		$validation = Validator::make($input, Coursebundle::$rules);
		Session::reflash();

		if ($validation->passes())
		{
			$coursebundle = $this->coursebundle->find($id);			
			$coursebundle->update($input);
			$coursebundle->bundles()->sync(array());
			foreach ($bundle_courses as $data)
			{
				$course_id = $data['course_id'];
				unset($data['course_id']);
				$coursebundle->bundles()->attach($course_id, $data );
			}
		
			return Redirect::route('backend.coursebundles.index');
		}

		return Redirect::route('backend.coursebundles.edit', $id)
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
		$bundle = $this->coursebundle->find($id);
		if ($bundle->bundles()->count())
			$bundle->bundles()->sync(array());
		$bundle->delete();
		Session::reflash();

		return Redirect::route('backend.coursebundles.index');
	}

}