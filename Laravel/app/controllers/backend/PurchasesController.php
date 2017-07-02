<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View;
use Location, Purchase, Order, Item, Invoice, Customer, DB, Status, Course;

class PurchasesController extends AdminController {

	/**
	 * Purchase Repository
	 *
	 * @var Purchase
	 */
	protected $purchase;

	public function __construct(Purchase $purchase)
	{
		parent::__construct();
		$this->purchase = $purchase;
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
			$purchases = array();
		else
		{
			if(!isset($from) || empty($from))
				$from = date("Y-m-d");

			if(!isset($to) || empty($to))
				$to = date("Y-m-d", strtotime('+1 Week'));

			$purchases = $this->purchase->whereBetween('created_at', array($from, $to))
				->orderBy('location_id')
				->orderBy('created_at')
				->paginate(20);

		}
		Input::flash();

		return View::make('backend.purchases.index', compact('purchases'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$purchase = $this->purchase->with('customer')->find($id);

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

		if (is_null($purchase))
		{
			return Redirect::route('backend.purchases.index');
		}
		Input::flash();

		return View::make('backend.purchases.edit', compact('purchase', 'locations'));
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
		$validation = Validator::make($input, Purchase::$rules);

		if ($validation->passes())
		{
			$purchase = $this->purchase->find($id);
			$purchase->update($input);

			return Redirect::route('backend.purchases.edit', $id);
		}

		return Redirect::route('backend.purchases.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

}