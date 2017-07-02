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
use CourseInstance, CourseInstanceSpecial, Instructor, User, Course, Location;

class CourseInstancesController extends AdminController {

	/**
	 * Courseinstance Repository
	 *
	 * @var Courseinstance
	 */
	protected $courseinstance;

	public function __construct(Courseinstance $courseinstance)
	{
		parent::__construct();
		$this->courseinstance = $courseinstance;
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
		$courseinstances = SearchService::ProcessCourseInstanceSearch();		

			
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');
		$locations = array('' => 'Select Location');
		
		
		$parents = Location::where('parent_id',0)->get();
		foreach ($parents as $location)
		{
			$locations = array_add($locations, $location->id, $location->name);
			foreach ($location->children as $loc)
			{
				$locations = array_add($locations, $loc->id, '...... ' . $loc->name);
			}
		}		
		
		
		//$locs = Location::where('parent_id', 0)->get();
		//foreach ($locs as $location)
		//{
		//	$group = array();
		//	$group = array_add($group, $location->id , $location->name);
		//	foreach ($location->children as $loc)
		//	{	
		//		$group = array_add($group, $loc->id , $loc->name);
		//	}
		//	$locations = array_add($locations, $location->name, $group);
		//}
		
		
		return View::make('backend.instances.index', compact('courses', 'locations', 'courseinstances'));
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$input = Session::get('_old_input');
		$course = null;
		if(array_key_exists('c_id', Input::all()))
			$course = Course::find(Input::get('c_id'));
		elseif($input && array_key_exists('c_id', $input) && $input['c_id'] != '')
			$course = Course::find($input['c_id']);
		
		if(array_key_exists('l_id', Input::all()))
			$l_id = Input::get('l_id');  
		elseif( $input && array_key_exists('l_id', $input))
			$l_id = $input['l_id'];  
		else
			$l_id = '';  
		
		Session::flashInput(Input::all());
		
		if ($course)
		{	
			$instructors = array('' => 'Select one or more Instructors') + 
				$course->instructors()
				->orderBy('first_name')
				->orderBy('last_name')
				->select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))
				->lists('full_name', 'id');

			$locations = array('' => 'Select Location');
			$parents = Location::where('parent_id',0)->get();
			foreach ($parents as $location)
			{
				$locations = array_add($locations, $location->id, $location->name);
				foreach ($location->children as $loc)
				{
					$locations = array_add($locations, $loc->id, '...... ' . $loc->name);
				}
			}			//$locs = Location::where('parent_id', 0)->get();
			//foreach ($locs as $location)
			//{
			//	$group = array();
			//	$group = array_add($group, $location->id , $location->name);
			//	foreach ($location->children as $loc)
			//	{	
			//		$group = array_add($group, $loc->id , $loc->name);
			//	}
			//	$locations = array_add($locations, $location->name, $group);
			//}

			$course_times = Config::get('utils.course_times', array());
		}
		else
		{
			return Redirect::route('backend.instances.index')->with('error', 'You must select a course first');
		}	
		$courses = array('' => 'Select Course Type:') + Course::lists('name', 'id');

		return View::make('backend.instances.create', compact('instructors', 'course', 'courses', 'course_times', 'locations'));
		
		

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('instructor','l_id','c_id','from','to', 'price_original', 'price_offline', 'price_online', 'price_id', 'price_active');
		$special = Input::only('price_original', 'price_offline', 'price_online', 'price_id', 'price_active');
		$instructors = Input::get('instructor', array());
		
		Session::reflash();
		$validation = Validator::make($input, Courseinstance::$rules);

		if ($validation->passes())
		{
			$instance = $this->courseinstance->create($input);
			$instance->instructors()->sync($instructors);
			
			$special['course_instance_id'] = $instance->id;
			$priceValidation = Validator::make($special, CourseInstanceSpecial::$rules);
			if ($priceValidation->passes())
			{
				$special['id'] = null;
				$special['active'] = $special['price_active'];
				unset($special['price_id']);
				unset($special['price_active']);
				$instance_price = CourseInstanceSpecial::create($special);
			}

			return Redirect::route('backend.instances.index');
		}
		return Redirect::back()->withInput()->withErrors($validation)->with('message', 'There were validation errors.');

		//return Redirect::route('backend.instances.create')
		//	->withInput()
		//	->withErrors($validation)
		//	->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$courseinstance = $this->courseinstance->find($id);

		if (is_null($courseinstance))
		{
			return Redirect::route('backend.instances.index');
		}

		return View::make('backend.instances.edit', compact('courseinstance'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$courseinstance = CourseInstance::with('instructors')->find($id);
		if (is_null($courseinstance))
		{
			return Redirect::route('backend.instances.index');
		}

		$instInst = $courseinstance->instructors()->lists('id');

		$courseinstance->instructors = $instInst;
		
		$selectedCourse = Course::find($courseinstance->course_id);
		$instructors = array('' => 'Select one or more Instructors') + 
						$selectedCourse->instructors()
						->orderBy('first_name')
						->orderBy('last_name')
			->select(\DB::raw('concat (first_name," ",last_name) as full_name,id'))
						->lists('full_name', 'id');
		
		$locations = array('' => 'Select Location');
		$parents = Location::where('parent_id',0)->get();
		foreach ($parents as $location)
		{
			$locations = array_add($locations, $location->id, $location->name);
			foreach ($location->children as $loc)
			{
				$locations = array_add($locations, $loc->id, '...... ' . $loc->name);
			}
		}
		
		//$locs = Location::where('parent_id', 0)->get();
		//foreach ($locs as $location)
		//{
		//	$group = array();
		//	$group = array_add($group, $location->id , $location->name);
		//	foreach ($location->children as $loc)
		//	{	
		//		$group = array_add($group, $loc->id , $loc->name);
		//	}
		//	$locations = array_add($locations, $location->name, $group);
		//}
		
		//Session::reflash();

		$course_times = Config::get('utils.course_times', array());

		return View::make('backend.instances.edit', compact('courseinstance', 'locations', 'course_times', 'instructors'));
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
			$input = Input::except('instructor', '_method','l_id','c_id','from','to', 'price_original', 'price_offline', 'price_online', 'price_id', 'price_active');
			$special = Input::only('price_original', 'price_offline', 'price_online', 'price_id', 'price_active');
			$instructors = Input::get('instructor', array());
			$validation = Validator::make($input, Courseinstance::$rules);		

			//Session::reflash();
			if ($validation->passes())
			{
				$instance = $this->courseinstance->find($id);
				$instance->update($input);
				$instance->instructors()->sync($instructors);
			
				$special['course_instance_id'] = $instance->id;
				$priceValidation = Validator::make($special, CourseInstanceSpecial::$rules);
				if ($priceValidation->passes())
				{
					$special['id'] = $special['price_id'];
					$special['active'] = $special['price_active'];
					unset($special['price_id']);
					unset($special['price_active']);

					$sps = CourseInstanceSpecial::where('course_instance_id', $instance->id)->get();
					foreach($sps as $sp)
					{
						if ($sp->id != $special['id'])
							$sp->delete();
						else
							$sp->update($special);
					}
					
					if (empty($special['id']))
					{
						$special['id'] = null;
						$instance_price = CourseInstanceSpecial::create($special);
					}
				}
			
				return Redirect::route('backend.instances.edit', $id)->with('success', 'Class updated successfully');
			}

			return Redirect::route('backend.instances.edit', $id)
				->withInput()
				->withErrors($validation)
				->with('message', 'There were validation errors.');
			
		}
		catch (Exception $ex)
		{
			return Redirect::back()
			->withInput()
			->with('error', $ex->getMessage());
		}
	}
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$instance = $this->courseinstance->find($id);
		
		$input = Session::get('_old_input');
		
		if(array_key_exists('l_id', Input::all()))
			$l_id = Input::get('l_id');  
		elseif( $input && array_key_exists('l_id', $input))
			$l_id = $input['l_id'];  
		else
			$l_id = '';  
		
		if(array_key_exists('c_id', Input::all()))
			$c_id = Input::get('c_id');  
		elseif($input && array_key_exists('c_id', $input))
			$c_id = $input['c_id'];  
		else
			$c_id = '';  
		
		if(array_key_exists('from', Input::all()))
			$from = Input::get('from');  
		elseif($input && array_key_exists('from', $input))
			$from = $input['from'];  
		else
			$from = '';  
		
		if(array_key_exists('to', Input::all()))
			$to = Input::get('to');  
		elseif($input && array_key_exists('to', $input))
			$to = $input['to'];  
		else
			$to = '';  
		
		if ($instance->students > 0) 
		{
			return Redirect::route('backend.instances.index', array('l_id'=>$l_id, 'c_id'=>$c_id,'from'=>$from,'to'=>$to))
			->with('error', 'Class cannot be deleted as it has students attached');
		}
		else
		{
			$instance->delete();
			return Redirect::route('backend.instances.index', array('l_id'=>$l_id, 'c_id'=>$c_id,'from'=>$from,'to'=>$to));
		}

	}

}