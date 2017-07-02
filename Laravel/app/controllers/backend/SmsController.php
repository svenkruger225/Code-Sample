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

class SmsController extends AdminController {

	/**
	 * Sm Repository
	 *
	 * @var Sm
	 */
	protected $sm;

	public function __construct(\Sms $sm)
	{
		parent::__construct();
		$this->sm = $sm;
	}
	public function index()
	{
		$sms = $this->sm->all();

		return View::make('backend.sms.index', compact('sms'));
	}
	public function create()
	{
		return View::make('backend.sms.create');
	}
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, \Sms::$rules);

		if ($validation->passes())
		{
			$this->sm->create($input);

			return Redirect::route('backend.sms.index');
		}

		return Redirect::route('backend.sms.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}
	public function show($id)
	{
		$sm = $this->sm->findOrFail($id);

		return View::make('backend.sms.show', compact('sm'));
	}
	public function edit($id)
	{
		$sm = $this->sm->find($id);

		if (is_null($sm))
		{
			return Redirect::route('backend.sms.index');
		}

		return View::make('backend.sms.edit', compact('sm'));
	}
	public function update($id)
	{
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Sm::$rules);

		if ($validation->passes())
		{
			$sm = $this->sm->find($id);
			$sm->update($input);

			return Redirect::route('backend.sms.show', $id);
		}

		return Redirect::route('backend.sms.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}
	public function destroy($id)
	{
		$this->sm->find($id)->delete();

		return Redirect::route('backend.sms.index');
	}

}