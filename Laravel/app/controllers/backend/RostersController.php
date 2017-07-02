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

class RostersController extends AdminController {

	/**
	 * Roster Repository
	 *
	 * @var Roster
	 */
	protected $roster;

	public function __construct(\Roster $roster)
	{
		parent::__construct();
		$this->roster = $roster;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$rosters = $this->roster->paginate(20);

		return View::make('backend.rosters.index', compact('rosters'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.rosters.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, \Roster::$rules);

		if ($validation->passes())
		{
			$this->roster->create($input);

			return Redirect::route('backend.rosters.index');
		}

		return Redirect::route('backend.rosters.create')
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
		$roster = $this->roster->findOrFail($id);

		return View::make('backend.rosters.show', compact('roster'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$roster = $this->roster->find($id);

		if (is_null($roster))
		{
			return Redirect::route('backend.rosters.index');
		}

		return View::make('backend.rosters.edit', compact('roster'));
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
		$validation = Validator::make($input, Roster::$rules);

		if ($validation->passes())
		{
			$roster = $this->roster->find($id);
			$roster->update($input);

			return Redirect::route('backend.rosters.show', $id);
		}

		return Redirect::route('backend.rosters.edit', $id)
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
		$this->roster->find($id)->delete();

		return Redirect::route('backend.rosters.index');
	}

}