<?php namespace Controllers\Backend;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config,Input,Lang,Redirect,Sentry,Validator,View,Response, File;
use PDF, Voucher, PdfService, Location, Course, SearchService, Status;

class VouchersController extends AdminController {

	/**
	 * Voucher Repository
	 *
	 * @var Voucher
	 */
	protected $voucher;

	public function __construct(Voucher $voucher)
	{
		parent::__construct();
		$this->voucher = $voucher;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$vouchers = SearchService::ProcessVoucherSearch($this->voucher);		

		Input::flash();	

		$statuses = array('' => 'Select Status:') + Status::where('status_type', 'Voucher')->lists('name', 'id');
		$search_types = Config::get('utils.voucher_search_types', array());
		$courses = array('' => 'Select Course Type:') + Course::where('active', 1)->lists('name', 'id');
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->where('active', 1)->lists('name', 'id');

		return View::make('backend.vouchers.index', compact('search_types','locations','courses','vouchers', 'statuses'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if ( ! Sentry::getUser()->isSuperUser())
				return Response::make(View::make('error/403'), 403);
		
		$courses = array('' => 'Select Course Type:') + Course::where('active', 1)->lists('name', 'id');
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->where('active', 1)->lists('name', 'id');
		$statuses = array('' => 'Select Status') + Status::where('Status_type', 'Voucher')->where('active', 1)->lists('name', 'id');
		return View::make('backend.vouchers.create', compact('voucher', 'courses', 'locations', 'statuses'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if ( ! Sentry::getUser()->isSuperUser())
				return Response::make(View::make('error/403'), 403);

		$input = Input::all();
		$validation = Validator::make($input, Voucher::$rules);

		if ($validation->passes())
		{
			$this->voucher->create($input);

			return Redirect::route('backend.vouchers.index')->with('success', 'Voucher created successfully');
		}

		return Redirect::route('backend.vouchers.create')
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
		$voucher = $this->voucher->findOrFail($id);

		return View::make('backend.vouchers.show', compact('voucher'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if ( ! Sentry::getUser()->isSuperUser())
				return Response::make(View::make('error/403'), 403);

		$voucher = $this->voucher->find($id);

		if (is_null($voucher))
		{
			return Redirect::route('backend.vouchers.index');
		}
		$courses = array('' => 'Select Course Type:') + Course::where('active', 1)->lists('name', 'id');
		$locations = array('' => 'Select Location') + Location::where('parent_id', 0)->where('active', 1)->lists('name', 'id');
		$statuses = array('' => 'Select Status') + Status::where('Status_type', 'Voucher')->where('active', 1)->lists('name', 'id');

		return View::make('backend.vouchers.edit', compact('voucher', 'courses', 'locations', 'statuses'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if ( ! Sentry::getUser()->isSuperUser())
				return Response::make(View::make('error/403'), 403);

		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Voucher::$rules);

		if ($validation->passes())
		{
			$voucher = $this->voucher->find($id);
			$voucher->update($input);

			return Redirect::back()->with('success', 'Voucher updated successfully');
		}

		return Redirect::route('backend.vouchers.edit', $id)
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
		if ( ! Sentry::getUser()->isSuperUser())
				return Response::make(View::make('error/403'), 403);

		$this->voucher->find($id)->delete();

		return Redirect::route('backend.vouchers.index');
	}
	
	//public function download($id)
	//{
	//	
	//	//for vouchers , we first delete any existing voucher
	//	$filename = 'voucher-' . $id . '.pdf';
	//	$filepath = storage_path() . '/vouchers/' . $filename;
	//	
	//	if (file_exists($filepath))
	//		File::delete($filepath);	
	//		
	//	//$voucher = Voucher::find($id);
	//	//return View::make('backend.vouchers.voucher', compact('voucher'));
	//	return PdfService::download('Voucher', 'backend.vouchers.voucher', '/vouchers/', 'voucher-', $id);	
	//}

}