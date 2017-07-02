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

class PaymentsController extends AdminController {

	/**
	 * Payment Repository
	 *
	 * @var Payment
	 */
	protected $payment;

	public function __construct(\Payment $payment)
	{
		$this->beforeFilter('super-auth');
		parent::__construct();
		$this->payment = $payment;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$order_id = Input::get('order_id');  
		$from = Input::get('from');  
		$to = Input::get('to');
		if(!empty($from) && empty($to))
			$to = $from;
		
		
		
		$query = $this->payment;
		
		if(	!empty($order_id))
		{
			$query = $query
				->where('order_id', $order_id);
		}
		else
		{
			if(!empty($from) && !empty($to))
				$query = $query->whereBetween('payment_date', array($from, $to));
		}



		$payments = $query
			->orderBy('payment_date', 'desc')
			->paginate(20)
			->appends(array('order_id' => $order_id,'from' => $from,'to' => $to,));
		
		Input::flash();	

		return View::make('backend.payments.index', compact('payments'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$methods = \PaymentMethod::lists('name','id');
		$statuses = \Status::where('status_type', 'Payment')->lists('name','id');
		Input::flash();
		return View::make('backend.payments.create', compact('methods', 'statuses'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, \Payment::$rules);

		if ($validation->passes())
		{
			$this->payment->create($input);

			return Redirect::route('backend.payments.index');
		}

		return Redirect::route('backend.payments.create')
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
		$payment = $this->payment->findOrFail($id);
		$methods = \PaymentMethod::lists('name','id');
		$statuses = \Status::where('status_type', 'Payment')->lists('name','id');

		return View::make('backend.payments.show', compact('payment', 'methods', 'statuses'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$payment = $this->payment->find($id);

		if (is_null($payment))
		{
			return Redirect::route('backend.payments.index');
		}
		$methods = \PaymentMethod::lists('name','id');
		$statuses = \Status::where('status_type', 'Payment')->lists('name','id');

		Input::flash();
		return View::make('backend.payments.edit', compact('payment', 'methods', 'statuses'));
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
		$input['gateway_id'] = empty($input['gateway_id']) ? null : $input['gateway_id'];
		
		$validation = Validator::make($input, \Payment::$rules);

		if ($validation->passes())
		{
			$payment = $this->payment->find($id);
			$payment->update($input);

			return Redirect::route('backend.payments.edit', $id)->with('success', 'Payment updated successfully');
		}

		$test = $validation->getMessageBag();

		return Redirect::route('backend.payments.edit', $id)
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
		$this->payment->find($id)->delete();

		return Redirect::route('backend.payments.index');
	}

}