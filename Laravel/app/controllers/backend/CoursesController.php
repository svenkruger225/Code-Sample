<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config;
use Input;
use Lang;
use Redirect;
use Sentry;
use Validator;
use View;
use Course;

class CoursesController extends AdminController {

	/**
	 * Course Repository
	 *
	 * @var Course
	 */
	protected $course;

	public function __construct(Course $course)
	{
		parent::__construct();
		$this->course = $course;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$is_online = Input::get('online',0);
		$query = $this->course
			->orderBy('active', 'desc')
			->orderBy('type')
			->orderBy('order');
		if ($is_online)
		{
			$query = $query->where('type','Online');
		}	
		$courses = 	$query->get();
		return View::make('backend.courses.index', compact('courses'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$locations = \Location::where('parent_id', 0)->lists('name','id');
		$courses = array('0' => 'Select Course Type:') + Course::orderBy('order')->lists('name', 'id');
		return View::make('backend.courses.create', compact('locations','courses'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('price_id', 'course_id','location_id','price_online','price_offline','discount','discount_type','students_min','act', '_method');
		
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		
		$prices_data = Input::only('price_id', 'course_id','location_id','price_online','price_offline','discount','discount_type','students_min','act');

		$prices= array();
		foreach($prices_data['location_id'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['id'] = null;
			$obj['course_id'] = null;
			$obj['location_id'] = $prices_data['location_id'][$index];
			$obj['price_online'] = $prices_data['price_online'][$index];
			$obj['price_offline'] = $prices_data['price_offline'][$index];
			$obj['discount'] = empty($prices_data['discount'][$index]) ? 0 : $prices_data['discount'][$index];
			$obj['discount_type'] = $prices_data['discount_type'][$index];
			$obj['students_min'] = $prices_data['students_min'][$index];
			$obj['active'] = $prices_data['act'][$index];
			array_push($prices, $obj);
		}
		
		$validation = Validator::make($input, Course::$rules);

		if ($validation->passes())
		{
			$course = $this->course->create($input);
			foreach ($prices as $price)
			{
				$price['course_id'] = $course->id;
				\CoursePrice::create($price);
			}

			return Redirect::route('backend.courses.index');
		}

		return Redirect::route('backend.courses.create')
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
		$course = $this->course->findOrFail($id);

		return View::make('backend.courses.show', compact('course'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$course = $this->course->find($id);

		if (is_null($course))
		{
			return Redirect::route('backend.courses.index');
		}
		$locations = \Location::where('parent_id', 0)->lists('name','id');
		$courses = array('0' => 'Select Course Type:') + Course::orderBy('order')->lists('name', 'id');

		return View::make('backend.courses.edit', compact('course','locations','courses'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('price_id', 'course_id','location_id','price_online','price_offline','discount','discount_type','students_min','act', '_method');
		
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		
		$prices_data = Input::only('price_id', 'course_id','location_id','price_online','price_offline','discount','discount_type','students_min','act');
		$prices= array();
		foreach($prices_data['location_id'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['id'] = $prices_data['price_id'][$index] == '' ? null : $prices_data['price_id'][$index];
			$obj['course_id'] = $prices_data['course_id'][$index];
			$obj['location_id'] = $prices_data['location_id'][$index];
			$obj['price_online'] = $prices_data['price_online'][$index];
			$obj['price_offline'] = $prices_data['price_offline'][$index];
			$obj['discount'] = $prices_data['discount'][$index];
			$obj['discount_type'] = $prices_data['discount_type'][$index];
			$obj['students_min'] = $prices_data['students_min'][$index];
			$obj['active'] = $prices_data['act'][$index];
			array_push($prices, $obj);
		}

		$validation = Validator::make($input, Course::$rules);

		if ($validation->passes())
		{
			$course = $this->course->find($id);
			$course->update($input);
			
			foreach ($course->prices as $price)
			{
				$new_price_found = false;
				foreach ($prices as $new_price)
				{
					if ($new_price['id'] == $price->id) {
						$new_price['course_id'] = $course->id;
						$price->update($new_price);
						$new_price_found = true; 
						break;
					}					
				}
				if (!$new_price_found) 
					$price->delete();
			}
			foreach ($prices as $new_price)
			{
				$new_price['course_id'] = $course->id;
				if ($new_price['id'] == '' || $new_price['id'] == null)
				{
					\CoursePrice::create($new_price);
				}
			}

			return Redirect::route('backend.courses.edit', $id)->with('success', 'Course successfully updated.');
		}

		return Redirect::route('backend.courses.edit', $id)
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
		$course = $this->course->find($id);
		foreach ($course->prices as $price)
			$price->delete();
		$course->delete();

		return Redirect::route('backend.courses.index');
	}

}