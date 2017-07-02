<?php namespace App\Services\payments;

use Symfony\Component\HttpFoundation\ParameterBag;
use Sentry, Utils, Invoice, Payment, Order;
use Log;

abstract class AbstractPayment
{
	/**
	 * @var \Symfony\Component\HttpFoundation\ParameterBag
	 */
	protected $parameters;
	protected $order;

	public function __construct()
	{
	}

	public function initialize(array $parameters = array())
	{
		$this->parameters = new ParameterBag;

		// set parameters
		foreach ($parameters as $key => $value) {
			if (is_array($value)) {
				$this->parameters->set($key, reset($value));
			} else {
				$this->parameters->set($key, $value);
			}
		}
		return $this;
	}

	public function getParameters()
	{
		return $this->parameters->all();
	}

	protected function getParameter($key)
	{
		return $this->parameters->get($key);
	}

	protected function setParameter($key, $value)
	{
		$this->parameters->set($key, $value);

		return $this;
	}
	
	protected function CreateInvoice($status, $comments)
	{
		$this->order->update(array('status_id'=> Utils::StatusId('Order', 'Invoiced')));	
		
		$input = array('id' => null,'order_id' => $this->order->id,'invoice_date' => date('Y-m-d'),'comments' => $comments,'status_id' => $status);
		
		$invoice = Invoice::create($input);
		
		return $invoice;
		
	}
	
	protected function CreatePayment($payment_method, $comments)
	{
		$input = $this->getParameters();
		$input['user_id'] = empty($input['user_id']) ? (Sentry::check() ? Sentry::getUser()->id : null) : $input['user_id'];
		$payment_status = $input['PaymentStatus'];
		
		if (filter_var($payment_status, FILTER_VALIDATE_BOOLEAN))
			$status = Utils::StatusId('Payment', 'OK');
		else
			$status = Utils::StatusId('Payment', 'Failed');

		if (!empty($input['PaymentErrorCode']) && $input['PaymentErrorCode'] == '11607') // paypal duplicate transaction
			$status = Utils::StatusId('Payment', 'Cancelled');

		
		$pay_input = array(
			'id' => null,
			'order_id' => $this->order->id,
			'payment_date' => !empty($input['TransactionDate']) ?  $input['TransactionDate'] : date('Y-m-d'),
			'payment_method_id' => $payment_method,
			'backend' => !empty($input['Backend']) ?  $input['Backend'] : 0,
			'IP' => (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] != 'artisan') ? $_SERVER['REMOTE_ADDR'] : '',
			'comments' => $comments,
			'instalment' => 1,
			'gateway_id' => 1,
			'gateway_response' =>  $input['GatewayResponse'],
			'status_id' => $status,
			'total' => isset( $input['TotalToPay']) ?  $input['TotalToPay'] :  $input['Amount'],
			'user_id' => $input['user_id']
			);
        Log::debug("add payment " .json_encode($pay_input));
		$payment = Payment::create($pay_input);
		
	}


}
