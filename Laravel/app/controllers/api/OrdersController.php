<?php namespace Controllers\Api;

use AdminController;
use Cartalyst\Sentry\Users\LoginRequiredException;
use Cartalyst\Sentry\Users\PasswordRequiredException;
use Cartalyst\Sentry\Users\UserExistsException;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Config, Input, Lang, Redirect, Exception, Sentry, Validator, View, Response, Utils, log, Queue;
use Location, Roster, Order, Item, Invoice, CourseInstance, Customer, DB, Status, Course, OrderService;
use App\Services\payments\PaymentFactory;

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

	public function getItems()
	{
		$order_id = Input::all();
		$order = $this->order->find($order_id[0]);
		
		$list = array();
		foreach($order->active_items as $item)
		{
			$entry = array(
				'id' => $item->id ,
				'OrderId' => $item->order_id ,
				'CourseId' => $item->course_instance_id ,
				'GroupId' => $item->group_booking_id ,
				'VoucherId' => $item->vouchers_ids ,
				'ProductId' => $item->product_id ,
				'Type' => $item->itemtype->name ,
				'Description' => $item->description ,
				'Qty' => $item->qty ,
				'Price' => $item->price ,
				'Gst' => $item->gst ,
				'Total' => $item->total ,
				'Active' => $item->active 
				);
			array_push($list, $entry);
		}

		return Response::json($list);
	}

	public function activateOrder()
	{
		DB::beginTransaction();	
		$order_id = Input::all();
	
		try 
		{	
			OrderService::activateOrder($order_id[0]);
			DB::commit();
			return Response::json(array('msg'=>'Successfully activated order'));
		}
		catch (Exception $e)
		{
			DB::rollback();
			return Response::json(array(
				'success' => false,
				'Message' => "Problem activating order <br>" . $e->getMessage()
				), 500);
		}

	}

	public function deactivateOrder()
	{
		DB::beginTransaction();	
		$order_id = Input::all();
	
		try 
		{	
			OrderService::deactivateOrder($order_id[0]);
			DB::commit();
			return Response::json(array('msg'=>'Successfully deactivated order'));
		}
		catch (Exception $e)
		{
			Log::error($e);
			DB::rollback();
			return Response::json(array(
				'success' => false,
				'Message' => "Problem deactivating order <br>" . $e->getMessage()
				), 500);
		}

	}
	
	public function processPaidList()
	{
		DB::beginTransaction();	

		$data = Input::json()->all();
		try 
		{	
			foreach($data['noshow'] as $roster_id)
			{
				OrderService::updateNoShow($roster_id);
			}
				
			DB::commit();
			return Response::json(array('msg'=>'Successfully Processed paid list'));

		}
		catch (Exception $e)
		{
			DB::rollback();
			return Response::json(array(
				'success' => false,
				'Message' => "Problem processing not list <br>" . $e->getMessage()
				), 500);
		}

	}
	
	public function processNotPaidList()
	{
		DB::beginTransaction();	
		$data = Input::json()->all();
	
		try 
		{	
			foreach($data['deactivate'] as $roster_id)
			{
				OrderService::deactivateRoster($roster_id);
			}

			foreach($data['paidcash'] as $roster_id)
			{
				OrderService::updateOrderPayment($roster_id);
			}
			
			DB::commit();
			return Response::json(array('msg'=>'Successfully Processed not paid list'));

		}
		catch (Exception $e)
		{
			DB::rollback();
			return Response::json(array(
				'success' => false,
				'Message' => "Problem processing not paid list <br>" . $e->getMessage()
				), 500);
		}

	}

	public function createNewTransaction()
	{
		DB::beginTransaction();	
		$data = Input::json()->all();
		
		try 
		{	
			$order = $this->order->find($data['OrderId']);
			if ($data['PaymentMethod'] != 'ADJUST' && $data['PaymentMethod'] != 'REFUND')
			{
				//if ((float)$order->total == (float)$order->paid)
				//{
				//	$msg = "This Order has already been fully paid";
				//	throw new Exception($msg);				
				//}
				//if ( ((float)$order->paid + (float)$data['Amount']) > (float)$order->total )
				//{
				//	$msg = "This Transaction total is bigger than the Order Owing ($" . $order->owing . ")";
				//	throw new Exception($msg);				
				//}
			}
			
			$payment = array(
				'PaymentMethod' => $data['PaymentMethod'],
				'PaymentDate' => $data['TransactionDate'],
				'PaymentStatus' => true,
				'Backend' => 1,
				'GatewayResponse' => $data['Comments'],
				'TotalToPay' => $data['Amount']				
				);
			
			$payment_service = PaymentFactory::create($payment['PaymentMethod']);
			$payment_service->process($order, $payment);

			$data['Status'] = 'OK';
			$data['IP'] = $_SERVER['REMOTE_ADDR'];
			$data['User'] = Sentry::getUser()->name;
			
			DB::commit();
			return Response::json(array('payment'=>$data));

		}
		catch (Exception $e)
		{
			DB::rollback();
			return Response::json(array(
				'success' => false,
				'Message' => "Problem processing new payment <br>" . $e->getMessage()
				), 500);
		}
	}

	
	public function emailInvoice($id, $recreate = false)
	{

		try 
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
				Log::info("invoice message queued [" . $order->id ."]");
			
			}
			
			return Response::json("Invoice email re-sent");

		}
		catch (Exception $e)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem resending invoice email <br>" . $e->getMessage()
				), 500);
		}
	}


	public function getOrderById()
	{
		
		try 
		{	
			$order_id = Input::all();
			$order = $this->order->find($order_id[0]);

			$payments = array();
			if ($order->payments->count())
				foreach	($order->payments as $payment)
				{

					$pay = array(
						'id' => $payment->id,
						'TransactionDate' => $payment->payment_date,
						'PaymentMethod' => $payment->method ? $payment->method->name : $payment->payment_method_id,
						'Comments' => $payment->comments,
						'Status' => $payment->status->name,
						'Amount' => $payment->total
						);
					array_push($payments, $pay);

				}	

			$entry = array(
				'id' => $order->id ,
				'OrderId' => $order->id ,
				'order_date' => $order->order_date ,
				'invoice_id' => $order->current_invoice ? $order->current_invoice->id : '',
				'purchase_id' => $order->purchase_id ,
				'agent_id' => $order->agent_id ,
				'company_id' => $order->company_id ,
				'paid' => $order->paid ,
				'owing' => $order->owing ,
				'total' => $order->total ,
				'payments' => $payments
				);

			return Response::json($entry);

		}
		catch (Exception $e)
		{
			return Response::json(array(
				'success' => false,
				'Message' => "Problem loading order" . $e->getMessage()
				), 500);
		}
	}

}