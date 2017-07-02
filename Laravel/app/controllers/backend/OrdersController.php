<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View;
use Location, Order, Item, Invoice, Customer, DB, Status, Course;

class OrdersController extends AdminController {

	/**
	 * Order Repository
	 *
	 * @var Order
	 */
	protected $order;

	public function __construct(Order $order)
	{
		parent::__construct();
		$this->order = $order;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$from = Input::get('from');  
		$to = Input::get('to');

		if( (!isset($from) || empty($from)) &&  (!isset($to) || empty($to)))
			$orders = array();
		else
		{
			if(!isset($from) || empty($from))
				$from = date("Y-m-d");

			if(!isset($to) || empty($to))
				$to = date("Y-m-d", strtotime('+1 Week'));

			$orders = $this->order->with('status', 'customer', 'location')
				->whereBetween('order_date', array($from, $to))
				->orderBy('created_at', 'desc')
				->orderBy('location_id')
				->orderBy('order_date')
				->paginate(20);

		}
		Input::flash();

		return View::make('backend.orders.index', compact('orders'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$order = $this->order->with('customer')->find($id);
		
		$statuses = array('' => 'Select Status') + Status::where('status_type','Order')->lists('name', 'id');
		//$customers = array('' => 'Select Customer') + Customer::select(DB::raw('concat (first_name," ",last_name) as name,id'))->lists('name', 'id');
		$courses = array('' => 'Select Course') + Course::lists('name', 'id');

		$locations = array('' => 'Select Location');
		$parents = Location::where('parent_id',0)->get();
		foreach ($parents as $location)
		{
			$locations = array_add($locations, $location->id, $location->name);
			foreach ($location->children as $loc)
			{
				$locations = array_add($locations, $loc->id, '...... ' . $loc->name);
			}
		}		

		if (is_null($order))
		{
			return Redirect::route('backend.orders.index');
		}
		Input::flash();
		$methodsCode = \PaymentMethod::where('active', 1)->orderBy('name')->lists('name', 'code');

		return View::make('backend.orders.history', compact('order', 'statuses', 'courses', 'locations', 'methodsCode'));
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
		$validation = Validator::make($input, Order::$rules);

		if ($validation->passes())
		{
			$order = $this->order->find($id);
			$order->update($input);

			return Redirect::route('backend.orders.edit', $id);
		}

		return Redirect::route('backend.orders.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

}