<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use Config,Input,Lang,Redirect,Sentry, Validator,View, Utils;
use supplier, User, Course;

class SuppliersController extends AdminController {

	/**
	 * supplier Repository
	 *
	 * @var supplier
	 */
	protected $supplier;

	public function __construct(Supplier $supplier)
	{
		parent::__construct();
		$this->supplier = $supplier;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('search');
		$c_id = Input::get('c_id');  

		$query = $this->supplier
			->join('users_groups', 'users.id', '=', 'users_groups.user_id')
			->where('users_groups.group_id',4);
		
		if(	$search && $search != '')
			$query = $query
				->where('first_name', 'like', '%' . $search . '%')
				->orWhere('last_name', 'like', '%' . $search . '%')
				->orWhere('business_name', 'like', '%' . $search . '%')
				->orWhere('keywords', 'like', '%' . $search . '%');
		
		if(	$c_id && $c_id != '')
			$query = $query->trainerForCourse($c_id);
		

		$suppliers = $query->paginate(20);
		$courses = array('' => 'Select a Course Type') + Course::lists('name', 'id');
		Input::flash();

		return View::make('backend.suppliers.index', compact('courses', 'suppliers'));
	}

	public function create()
	{
		$states = array(''=>'Select State') + Config::get('utils.states', array());
		return View::make('backend.suppliers.create', compact('states'));
	}


	public function store()
	{
		$input = Input::all();
		$input['permissions'] = array("superuser"=>"0","admin"=>"0","user"=>"0");
		$input['password'] = 'password';

		$validation = Validator::make($input, \Supplier::$rules);

		if ($validation->passes())
		{
			if ($supplier = Sentry::getUserProvider()->create($input))
			{
				$group = Sentry::findGroupByName('Suppliers');
				$supplier->addGroup($group);
				return Redirect::route('backend.suppliers.index');	
			}
		}

		return Redirect::route('backend.suppliers.create')
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
		$supplier = $this->supplier->find($id);
		

		if (is_null($supplier))
		{
			return Redirect::route('backend.suppliers.index');
		}
		
		$states = array(''=>'Select State') + Config::get('utils.states', array());
		
		Input::flash();

		return View::make('backend.suppliers.edit', compact('states','supplier'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except( 'groups','_method');
		$validation = Validator::make($input, supplier::$rules);
		// If validation fails, we'll exit the operation now.
		if ($validation->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validation);
		}

		try
		{

			$supplier = $this->supplier->find($id);
			$input['username'] = $supplier->username;
			$input['username'] = $input['username'] == '' ? Utils::GenerateUsername(4,8) : $input['username'];
			$input['password'] = $supplier->getPassword();
			$input['password'] = $input['password'] == '' ? 'password' : $input['password'];
			
			$supplier->update($input);
			
			Input::flash();

			// Redirect to the user page
			return Redirect::route('backend.suppliers.edit', $id)->with('success', 'Supplier was successfully updated.' );

		}
		catch (LoginRequiredException $e)
		{
			$error = Lang::get('backend/users/message.user_login_required');
		}

		// Redirect to the user page
		//return Redirect::route('backend.suppliers.edit', $id)->withInput()->with('error', $error);

		return Redirect::route('backend.suppliers.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

}