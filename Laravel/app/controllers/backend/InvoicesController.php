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
use View, PDF, PdfService, Queue, File;

class InvoicesController extends AdminController {

	/**
	 * Invoice Repository
	 *
	 * @var Invoice
	 */
	protected $invoice;

	public function __construct(\Invoice $invoice)
	{
		parent::__construct();
		$this->invoice = $invoice;
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
			$invoices = array();
		else
		{
			if(!isset($from) || empty($from))
				$from = date("Y-m-d");

			if(!isset($to) || empty($to))
				$to = date("Y-m-d", strtotime('+1 Week'));

			$invoices = $this->invoice
				->whereBetween('invoice_date', array($from, $to))
				->orderBy('invoice_date')
				->paginate(20);

		}
		Input::flash();	

		return View::make('backend.invoices.index', compact('invoices'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//$orders = array('' => 'Select Order') + \Order::select(\DB::raw('concat (id," ",order_date," ",customer_id) as name, id'))->lists('name', 'id');
		$methods = array('' => 'Select Method') + \PaymentMethod::lists('name', 'code');
		$statuses = array('' => 'Select Status') + \Status::where('status_type','Invoice')->lists('name', 'id');
		$pay_statuses = array('' => 'Select Status') + \Status::where('status_type','Payment')->lists('name', 'id');
		Input::flash();	
		return View::make('backend.invoices.create', compact('methods','statuses','pay_statuses'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('payment_date','payment_method_id','comments','IP','instalment','status_id','total');
		$input['invoice_date'] = $input['invoice_date']  == '' ? null : $input['invoice_date'];
	
		$pay_data = Input::only('payment_date','payment_method_id','comments','IP','instalment','status_id','total');
		$payments= array();
		foreach($course_data['payment_date'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['payment_date'] = $pay_data['payment_date'][$index];
			$obj['payment_method_id'] = $pay_data['payment_method_id'][$index];
			$obj['comments'] = $pay_data['comments'][$index];
			$obj['IP'] = $pay_data['IP'][$index];
			$obj['instalment'] = $pay_data['instalment'][$index];
			$obj['status_id'] = $pay_data['pay_status_id'][$index];
			$obj['total'] = $pay_data['total'][$index];
			array_push($payments, $obj);
		}

		$validation = Validator::make($input, \Invoice::$rules);

		if ($validation->passes())
		{
			$invoice = $this->invoice->create($input);
			foreach ($payments as $data)
			{
				$data['invoice_id'] = $invoice->id;
				\Payment::create($data);
			}
			return Redirect::route('backend.invoices.index');
		}
		Input::flash();	

		return Redirect::route('backend.invoices.create')
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
		$invoice = $this->invoice->findOrFail($id);

		return View::make('backend.invoices.show', compact('invoice'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$invoice = $this->invoice->find($id);
		//$orders = array('' => 'Select Order') + \Order::select(\DB::raw('concat (id," ",order_date," ",customer_id) as name, id'))->lists('name', 'id');
		$methods = array('' => 'Select Method') + \PaymentMethod::lists('name', 'id');
		$statuses = array('' => 'Select Status') + \Status::where('status_type','Invoice')->lists('name', 'id');
		$pay_statuses = array('' => 'Select Status') + \Status::where('status_type','Payment')->lists('name', 'id');

		if (is_null($invoice))
		{
			return Redirect::route('backend.invoices.index');
		}
		Input::flash();	
		
		return View::make('backend.invoices.edit', compact('invoice','methods','statuses', 'pay_statuses'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('payment_id','payment_date','payment_method_id','comments','IP','instalment','status_id','total', '_method');
		$input['invoice_date'] = $input['invoice_date']  == '' ? null : $input['invoice_date'];
		
		$pay_data = Input::only('payment_id','payment_date','payment_method_id','comments','IP','instalment','status_id','total');
		$payments= array();
		foreach($course_data['payment_date'] as $index => $val)
		{
			if ($index == 0)
				continue;
			$obj = array();
			$obj['id'] = $pay_data['payment_id'][$index];
			$obj['payment_date'] = $pay_data['payment_date'][$index];
			$obj['payment_method_id'] = $pay_data['payment_method_id'][$index];
			$obj['comments'] = $pay_data['comments'][$index];
			$obj['IP'] = $pay_data['IP'][$index];
			$obj['instalment'] = $pay_data['instalment'][$index];
			$obj['status_id'] = $pay_data['pay_status_id'][$index];
			$obj['total'] = $pay_data['total'][$index];
			array_push($payments, $obj);
		}
		$validation = Validator::make($input, Invoice::$rules);

		if ($validation->passes())
		{
			$invoice = $this->invoice->find($id);
			$invoice->update($input);
			foreach ($payments as $pay)
			{
				$pay['invoice_id'] = $invoice->id;
				if (isset($pay['id']) && $pay['id'] != '')
				{
					$existing_pay = \Payment::find($pay['id']);
					$existing_pay->update($pay);
				}
				else
				{
					\Payment::create($pay);
				}
			}

			return Redirect::route('backend.invoices.show', $id);
		}
		Input::flash();	

		return Redirect::route('backend.invoices.edit', $id)
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
		$this->invoice->find($id)->delete();

		return Redirect::route('backend.invoices.index');
	}

	/**
	 * generate pdf for resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function pdf($id)
	{
		$invoice = \Invoice::find($id);
		$order = \Order::find(1);

		return View::make('backend.invoices.invoice', compact('invoice', 'order'));
	}
	/**
	 * generate pdf for resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function download($id, $recreate = false)
	{

		$filepath = storage_path() . '/invoices/invoice-' . $id .'.pdf';
		if (filter_var($recreate, FILTER_VALIDATE_BOOLEAN) && file_exists($filepath))
		{	
			File::delete($filepath);	
		}

		return PdfService::download('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $id);	
	}
	
	public function email($id, $recreate = false)
	{

		$filepath = storage_path() . '/invoices/invoice-' . $id .'.pdf';
		if (filter_var($recreate, FILTER_VALIDATE_BOOLEAN) && file_exists($filepath))
		{	
			File::delete($filepath);	
		}
		
		$order = \Order::find($id);
		if($order && $order->rosters->count())
		{
			Queue::push('SendMessage', array(
				'order_id' => $order->id, 
				'SendAdmin'=> false, 
				'SendEmail'=> true, 
				'SendSMS'=> false,
				'IsGroupBooking'=> 'false',
				'HasVouchers' => $order->vouchers->count() ? true : false,
				'success'=> 'true'
				));
			\Log::debug("invoice message queued [" . $order->id ."]");
			
		}
		return \Response::make("", 204);
	}
}