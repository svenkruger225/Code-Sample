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

class StatusesController extends AdminController {

	/**
	 * Status Repository
	 *
	 * @var Status
	 */
	protected $status;

	public function __construct(\Status $status)
	{
		parent::__construct();
		$this->status = $status;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$statuses = $this->status->all()->sortBy(function($status)
			{
				return $status->status_type;
			});
		return View::make('backend.statuses.index', compact('statuses'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$statuses = array('Order'=>'Order', 'Invoice'=>'Invoice', 'Payment'=>'Payment', 'Voucher'=>'Voucher');
		return View::make('backend.statuses.create', compact('statuses'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, \Status::$rules);

		if ($validation->passes())
		{
			$this->status->create($input);

			return Redirect::route('backend.statuses.index');
		}

		return Redirect::route('backend.statuses.create')
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
		$status = $this->status->findOrFail($id);

		return View::make('backend.statuses.show', compact('status'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$status = $this->status->find($id);

		if (is_null($status))
		{
			return Redirect::route('backend.statuses.index');
		}
		$statuses = array('Order'=>'Order', 'Invoice'=>'Invoice', 'Payment'=>'Payment', 'Voucher'=>'Voucher');

		return View::make('backend.statuses.edit', compact('status', 'statuses'));
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
		$validation = Validator::make($input, \Status::$rules);

		if ($validation->passes())
		{
			$status = $this->status->find($id);
			$status->update($input);

			return Redirect::route('backend.statuses.index');
		}

		return Redirect::route('backend.statuses.edit', $id)
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
		$this->status->find($id)->delete();

		return Redirect::route('backend.statuses.index');
	}

}