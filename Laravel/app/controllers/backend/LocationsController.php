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
use Location;


class LocationsController extends AdminController {

	/**
	 * Location Repository
	 *
	 * @var Location
	 */
	protected $location;

	public function __construct(Location $location)
	{
		parent::__construct();
		$this->location = $location;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$locations = $this->location->where('parent_id',0)->get();

		return View::make('backend.locations.index', compact('locations'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$locations = array('0' => 'Select Parent Location') + $this->location->lists('name', 'id');
		return View::make('backend.locations.create', compact('locations'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Location::$rules);

		if ($validation->passes())
		{
			$this->location->create($input);

			return Redirect::route('backend.locations.index');
		}

		return Redirect::route('backend.locations.create')
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
		$location = $this->location->findOrFail($id);

		return View::make('backend.locations.show', compact('location'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$location = $this->location->find($id);

		if (is_null($location))
		{
			return Redirect::route('backend.locations.index');
		}

		return View::make('backend.locations.edit', compact('location'));
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
		$validation = Validator::make($input, Location::$rules);

		if ($validation->passes())
		{
			$location = $this->location->find($id);
			$location->update($input);

			return Redirect::route('backend.locations.edit', $id);
		}

		return Redirect::route('backend.locations.edit', $id)
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
		$this->location->find($id)->delete();

		return Redirect::route('backend.locations.index');
	}

}