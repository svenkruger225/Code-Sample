<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Sentry, Validator, View, Response;
use Product, DB;

class ProductsController extends AdminController {

	/**
	 * Product Repository
	 *
	 * @var Product
	 */
	protected $product;

	public function __construct(Product $product)
	{
		$this->product = $product;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$products = \Product::all();

		return View::make('backend.products.index', compact('products'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.products.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	
		$input = Input::except('_token');
		$validation = Validator::make($input, Product::$rules);


		if ($validation->passes())
		{
			$product = $this->product->create($input);
			
			return Redirect::route('backend.products.edit', $product->id)->with('success', 'Product created successfully');
		}

		return Redirect::route('backend.products.create')
		->withInput()
		->withErrors($validation)
		->withErrors($photo_validation)
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
		$product = $this->product->findOrFail($id);

		return View::make('backend.products.show', compact('product'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$product = $this->product->find($id);

		if (is_null($product))
		{
			return Redirect::route('backend.products.index');
		}

		return View::make('backend.products.edit', compact('product'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except( 'option_active');
		$validation = Validator::make($input, Product::$rules);


		if ($validation->passes())
		{
			$product = $this->product->find($id);
			$product->update($input);
			return Redirect::route('backend.products.edit', $product->id)->with('success', 'Product updated successfully');
		}

		return Redirect::route('backend.products.edit', $id)
		->withInput()
		->withErrors($validation)
		->withErrors($photo_validation)
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
		$this->product->find($id)->delete();

		return Redirect::route('backend.products.index');
	}

}
