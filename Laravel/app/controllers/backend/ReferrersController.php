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
use Referrer;

class ReferrersController extends AdminController {

	/**
	 * Referrer Repository
	 *
	 * @var Referrer
	 */
	protected $referrer;

	public function __construct(Referrer $referrer)
	{
		parent::__construct();
		$this->referrer = $referrer;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function dashboard()
	{
		return View::make('backend.referrers.dashboard');
	}
	
	public function index()
	{
		$referrers = $this->referrer->orderBy('order')->orderBy('name')->paginate(20);

		return View::make('backend.referrers.index', compact('referrers'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.referrers.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Referrer::$rules);
		$input['order'] = $input['order'] == '' ? 999999 : $input['order'];

		if ($validation->passes())
		{
			$this->referrer->create($input);

			return Redirect::route('backend.referrers.index');
		}

		return Redirect::route('backend.referrers.create')
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
		$referrer = $this->referrer->findOrFail($id);

		return View::make('backend.referrers.show', compact('referrer'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//$courses = array('' => 'Select Course Type:') + \Course::lists('name', 'id');
		//$locations = array('' => 'Select Location:') + \Location::lists('name', 'id');
		$referrer = $this->referrer->find($id);

		if (is_null($referrer))
		{
			return Redirect::route('backend.referrers.index');
		}

		return View::make('backend.referrers.edit', compact('referrer'));
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
		$validation = Validator::make($input, Referrer::$rules);
		$input['order'] = $input['order'] == '' ? 999999 : $input['order'];

		if ($validation->passes())
		{
			$referrer = $this->referrer->find($id);
			$referrer->update($input);

			return Redirect::route('backend.referrers.edit', $id);
		}

		return Redirect::route('backend.referrers.edit', $id)
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
		$this->referrer->find($id)->delete();

		return Redirect::route('backend.referrers.index');
	}

}