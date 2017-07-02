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
use PaymentMethod;

class PaymentMethodsController extends AdminController {

	/**
	 * Payment_method Repository
	 *
	 * @var Payment_method
	 */
	protected $payment_method;

	public function __construct(PaymentMethod $payment_method)
	{
		parent::__construct();
		$this->payment_method = $payment_method;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$payment_methods = $this->payment_method->orderBy('order')->get();

		return View::make('backend.payment_methods.index', compact('payment_methods'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.payment_methods.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$input['order'] = empty($input['order']) ? 0 : $input['order'];
		$validation = Validator::make($input, \PaymentMethod::$rules);

		if ($validation->passes())
		{
			$this->payment_method->create($input);

			return Redirect::route('backend.payment_methods.index');
		}

		return Redirect::route('backend.payment_methods.create')
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
		$payment_method = $this->payment_method->findOrFail($id);

		return View::make('backend.payment_methods.show', compact('payment_method'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$payment_method = $this->payment_method->find($id);

		if (is_null($payment_method))
		{
			return Redirect::route('backend.payment_methods.index');
		}

		return View::make('backend.payment_methods.edit', compact('payment_method'));
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
		$input['order'] = empty($input['order']) ? 0 : $input['order'];
		$validation = Validator::make($input, PaymentMethod::$rules);

		if ($validation->passes())
		{
			$payment_method = $this->payment_method->find($id);
			$payment_method->update($input);

			return Redirect::route('backend.payment_methods.index');
		}

		return Redirect::route('backend.payment_methods.edit', $id)
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
		$this->payment_method->find($id)->delete();

		return Redirect::route('backend.payment_methods.index');
	}

}