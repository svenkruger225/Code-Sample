<?php namespace Controllers\Online;

use AdminController;
use Config, Input,Lang,Redirect,Sentry,Validator,View;
use OnlineModule;

class ModulesController extends AdminController {

	/**
	 * OnlineModule Repository
	 *
	 * @var OnlineModule
	 */
	protected $module;

	public function __construct(OnlineModule $module)
	{
		parent::__construct();
		$this->module = $module;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//$modules = $this->module
		//	->orderBy('active', 'desc')
		//	->orderBy('order')
		//	->get();
		$courses = \Course::where('type','Online')->lists('name','id');
		
		$course_id = \Session::has('course_id') ? \Session::get('course_id') : Input::get('course_id');
		$query = $this->module;
		if (!empty($course_id)){
			$query = $query->where('course_id', $course_id);
			\Session::flashInput(array('course_id'=>$course_id));
		}
		$query = $query->orderBy('active', 'desc')
			->orderBy('course_id')
			->orderBy('order');
		$modules = $query->get();

		return View::make('online.backend.modules.index', compact('modules', 'courses'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$course_id = Input::get('course_id');
		if (!empty($course_id)) {
			$courses = \Course::where('id', $course_id)->lists('name','id');
			\Session::flashInput(array('course_id'=>$course_id));
		}
		else {
			$courses = \Course::lists('name','id');
		}
		$courses = \Course::where('id', $course_id)->lists('name','id');
		return View::make('online.backend.modules.create', compact('courses'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('_method');
		
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		
		$validation = Validator::make($input, OnlineModule::$rules);

		if ($validation->passes())
		{
			$module = $this->module->create($input);
			\Session::flashInput(array('course_id'=>$module->course_id));
			return Redirect::route('online.modules.index')
					->with('coursee_id', $module->course_id)
					->with('success', 'OnlineModule successfully created.');
			
		}

		return Redirect::route('online.modules.create')
		->withInput()
		->withErrors($validation)
		->with('message', 'There were validation errors.');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$module = $this->module->find($id);
		\Session::flashInput(array('course_id'=>$module->course_id));

		if (is_null($module))
		{
			return Redirect::route('online.modules.index');
		}
		
		$courses = \Course::where('type','Online')->lists('name','id');
		return View::make('online.backend.modules.edit', compact('module','courses'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('_method');
		$input['active'] = isset($input['active']) && $input['active'] == '1' ? 1 : 0;		
		$validation = Validator::make($input, OnlineModule::$rules);

		if ($validation->passes())
		{
			$module = $this->module->find($id);
			$module->update($input);
			\Session::flashInput(array('course_id'=>$module->course_id));
			return Redirect::route('online.modules.index')
				->with('coursee_id', $module->course_id)
				->with('success', 'OnlineModule successfully updated.');
		}

		return Redirect::route('online.modules.edit', $id)
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
		$module = $this->module->find($id);
		\Session::flashInput(array('course_id'=>$module->course_id));
		$module->delete();

		return Redirect::route('online.modules.index')
		->with('coursee_id', $module->course_id)
		->with('success', 'OnlineModule successfully deleted.');
	}

}