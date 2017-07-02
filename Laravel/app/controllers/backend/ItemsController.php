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

class ItemsController extends AdminController {

	/**
	 * Item Repository
	 *
	 * @var Item
	 */
	protected $item;

	public function __construct(\Item $item)
	{
		$this->beforeFilter('super-auth');
		parent::__construct();
		$this->item = $item;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$items = $this->item->all();

		return View::make('backend.items.index', compact('items'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.items.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, \Item::$rules);

		if ($validation->passes())
		{
			$this->item->create($input);

			return Redirect::route('backend.items.index');
		}

		return Redirect::route('backend.items.create')
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
		$item = $this->item->findOrFail($id);

		return View::make('backend.items.show', compact('item'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$item = $this->item->find($id);

		if (is_null($item))
		{
			return Redirect::route('backend.items.index');
		}

		return View::make('backend.items.edit', compact('item'));
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
		$validation = Validator::make($input, \Item::$rules);

		if ($validation->passes())
		{
			$item = $this->item->find($id);
			if ($item->price != $input['price']) {
				if ($item->instance && $item->instance->course->gst) {
					$input['gst'] = $input['price'] * 0.10;
				}
				else if  ($item->groupbooking && $item->groupbooking->course->gst) {
					$input['gst'] = $input['price'] * 0.10;
				}
				$input['total'] = $input['price'] * $item->qty;
			}
			
			$item->update($input);
			$item->order->updateOrderTotal(false); // parameter not to cancel if $total = zero

			return Redirect::route('backend.items.edit', $id)
					->with('success', 'update successfully');;
		}

		return Redirect::route('backend.items.edit', $id)
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
		$this->item->find($id)->delete();

		return Redirect::route('backend.items.index');
	}

}