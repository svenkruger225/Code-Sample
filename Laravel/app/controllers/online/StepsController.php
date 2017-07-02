<?php namespace Controllers\Online;

use AdminController;
use Config, Input,Lang,Redirect,Sentry,Validator,View;
use OnlineStep;

class StepsController extends AdminController {

	/**
	 * OnlineStep Repository
	 *
	 * @var OnlineStep
	 */
	protected $step;

	public function __construct(OnlineStep $step)
	{
		parent::__construct();
		$this->step = $step;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$modules = \OnlineModule::lists('name','id');
		$module_id = \Session::has('module_id') ? \Session::get('module_id') : Input::get('module_id');
		$query = $this->step;
		if (!empty($module_id)) {
			$query = $query->where('module_id', $module_id);
			\Session::flashInput(array('module_id'=>$module_id));
		}
		$query = $query->orderBy('active', 'desc')
			->orderBy('module_id')
			->orderBy('order');
		$steps = $query->get();
		return View::make('online.backend.steps.index', compact('steps','modules'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$module_id = Input::get('module_id');
		if (!empty($module_id)) {
			$modules = \OnlineModule::where('id', $module_id)->lists('name','id');
			\Session::flashInput(array('module_id'=>$module_id));
		}
		else {
			$modules = \OnlineModule::lists('name','id');
		}
		return View::make('online.backend.steps.create', compact('modules'));
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
		
		if (empty($input['order']))
		{
			$module = \OnlineModule::find($input['module_id']);	
			$input['order'] = $module->last_step_order + 10;	
		}
		
		$validation = Validator::make($input, OnlineStep::$rules);

		if ($validation->passes())
		{
			$step = $this->step->create($input);
			\Session::flashInput(array('module_id'=>$step->module_id));
			return Redirect::route('online.steps.index')
			->with('module_id', $step->module_id)
			->with('success', 'OnlineStep successfully created.');
		}

		return Redirect::route('online.steps.create')
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
		$step = $this->step->find($id);
		$modules = \OnlineModule::where('id', $step->module_id)->lists('name','id');
		\Session::flashInput(array('module_id'=>$step->module_id));

		if (is_null($step))
		{
			return Redirect::route('online.steps.index');
		}
		
		return View::make('online.backend.steps.edit', compact('step','modules'));
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
		$validation = Validator::make($input, OnlineStep::$rules);
		
		if ($validation->passes())
		{
			$step = $this->step->find($id);
			if (empty($input['order']))
			{
				$input['order'] = $step->module->last_step_order + 10;	
			}

			$step->update($input);
			\Session::flashInput(array('module_id'=>$step->module_id));
			return Redirect::route('online.steps.index')
			->with('module_id', $step->module_id)
			->with('success', 'OnlineStep successfully updated.');
		}

		return Redirect::route('online.steps.edit', $id)
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
		$step = $this->step->find($id);
		\Session::flashInput(array('module_id'=>$step->module_id));
		$step->delete();

		return Redirect::route('online.steps.index')
			->with('module_id', $step->module_id)
			->with('success', 'OnlineStep successfully deleted.');
	}

}