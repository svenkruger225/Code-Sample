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
use Company;

class CompaniesController extends AdminController {

	/**
	 * Company Repository
	 *
	 * @var Company
	 */
	protected $company;

	public function __construct(Company $company)
	{
		parent::__construct();
		$this->company = $company;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$companies = $this->company->orderBy('name')->paginate(20);

		return View::make('backend.companies.index', compact('companies'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('backend.companies.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Company::$rules);

		if ($validation->passes())
		{
			$this->company->create($input);

			return Redirect::route('backend.companies.index');
		}

		return Redirect::route('backend.companies.create')
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
		$company = $this->company->findOrFail($id);

		return View::make('backend.companies.show', compact('company'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$company = $this->company->find($id);

		if (is_null($company))
		{
			return Redirect::route('backend.companies.index');
		}

		return View::make('backend.companies.edit', compact('company'));
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
		$validation = Validator::make($input, Company::$rules);

		if ($validation->passes())
		{
			$company = $this->company->find($id);
			$company->update($input);

			return Redirect::route('backend.companies.edit', $id);
		}

		return Redirect::route('backend.companies.edit', $id)
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
		$this->company->find($id)->delete();

		return Redirect::route('backend.companies.index');
	}

}