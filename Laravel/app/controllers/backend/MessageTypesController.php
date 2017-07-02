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

class MessageTypesController extends AdminController {

	/**
	 * MessageTypeType Repository
	 *
	 * @var MessageType
	 */
	protected $messagetype;

	public function __construct(\MessageType $messagetype)
	{
		parent::__construct();
		$this->messagetype = $messagetype;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$messagetypes = $this->messagetype->all();

		return View::make('backend.messagetypes.index', compact('messagetypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.messagetypes.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, \MessageType::$rules);

		if ($validation->passes())
		{
			$this->messagetype->create($input);

			return Redirect::route('backend.messagetypes.index');
		}

		return Redirect::route('backend.messagetypes.create')
			->withInput()
			->withErrors($validation)
			->with('messagetype', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$messagetype = $this->messagetype->findOrFail($id);

		return View::make('backend.messagetypes.show', compact('messagetype'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$messagetype = $this->messagetype->find($id);

		if (is_null($messagetype))
		{
			return Redirect::route('backend.messagetypes.index');
		}

		return View::make('backend.messagetypes.edit', compact('messagetype'));
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
		$validation = Validator::make($input, \MessageType::$rules);

		if ($validation->passes())
		{
			$messagetype = $this->messagetype->find($id);
			$messagetype->update($input);

			return Redirect::route('backend.messagetypes.index');
		}

		return Redirect::route('backend.messagetypes.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('messagetype', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->messagetype->find($id)->delete();

		return Redirect::route('backend.messagetypes.index');
	}

}