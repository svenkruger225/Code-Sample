<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response;
use CmsResource;

class ResourceController extends AdminController {

	/**
	 * Sm Repository
	 *
	 * @var Sm
	 */
	protected $resource;

	public function __construct(CmsResource $resource)
	{
		parent::__construct();
		$this->resource = $resource;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$resources = $this->resource->all();

		return View::make('backend.resources.index', compact('resources'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$types = array('carousel'=>'carousel', 'block'=>'block');
		return View::make('backend.resources.create', compact('types'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, CmsResource::$rules);

		if ($validation->passes())
		{
			$this->resource->create($input);

			return Redirect::route('backend.resources.index');
		}

		return Redirect::route('backend.resources.create')
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
		$resource = $this->resource->findOrFail($id);

		return View::make('backend.resources.show', compact('resource'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$types = array('carousel'=>'carousel', 'block'=>'block');
		$resource = $this->resource->find($id);

		if (is_null($resource))
		{
			return Redirect::route('backend.resources.index');
		}

		return View::make('backend.resources.edit', compact('resource','types'));
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
		$validation = Validator::make($input, CmsResource::$rules);

		if ($validation->passes())
		{
			$resource = $this->resource->find($id);
			$resource->update($input);

			return Redirect::route('backend.resources.index');
		}

		return Redirect::route('backend.resources.edit', $id)
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
		$this->resource->find($id)->delete();

		return Redirect::route('backend.resources.index');
	}

}