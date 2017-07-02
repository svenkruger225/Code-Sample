<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Request, Response, Exception, Session, PDF, PdfService;
use Customer, Courseinstance, Order, Roster, Invoice, CreditNote, Payment, PaymentMethod, Status, Voucher;
use Omnipay\Common\CreditCard;
use Omnipay\Common\GatewayFactory;

class PaymentService {

	protected $payment;
	
	protected $order;
	protected $customer;

	protected $method = array();
	protected $status = array();

	public function __construct()
	{
	}
	
	public function Process($order, $payment)
	{
		$this->order = $order;
		$this->payment = $payment;
		
		if ($this->payment['PaymentMethod'] == 'LATER') // 
			$this->PayLater();
		else if ($this->payment['PaymentMethod'] == 'CC')
			$this->CreditCardPayment();
		else if ($this->payment['PaymentMethod'] == 'PAYPALTERM')
			$this->PaidPayPalTerminal();
		else if ($this->payment['PaymentMethod'] == 'CHEQUE')
			$this->PaidChequeDeposit();
		else if ($this->payment['PaymentMethod'] == 'EFTPOS')
			$this->PaidManualEftPos();
		else if ($this->payment['PaymentMethod'] == 'DEPOSIT')
			$this->PaidDirectDeposit();
		else if ($this->payment['PaymentMethod'] == 'CASH')
			$this->PaidCashAtClass();
		else if ($this->payment['PaymentMethod'] == 'CASH2')
			$this->PaidCashNotAtClass();
		else if ($this->payment['PaymentMethod'] == 'AGENT')
			$this->AgentToPay();
		else if ($this->payment['PaymentMethod'] == 'ADJUST')
			$this->Adjustment();
		else if ($this->payment['PaymentMethod'] == 'REFUND')
			$this->Refund();
		else {
			$msg = "Unknown payment method: " . $this->payment['PaymentMethod'];
			throw new Exception($msg);		
		}		
		
		
	}	

	public function ProcessVoucher($order, $payment)
	{
		$this->order = $order;
		$this->payment = $payment;

		$voucher = Voucher::find($this->payment['VoucherId']);
		if ($voucher->isValid())
		{
			$voucher->update(array('status_id'=> Utils::StatusId('Voucher', 'Used'), 'active' => '0'));
			$this->CreateVoucherPayment($voucher->id, $this->payment['VoucherValue']);	
		} 
		else
		{
			throw new Exception($voucher->message);
		}
		
	}

/*
	public function getSecurePayData($first_name, $last_name, $order_id)
	{		
		$gateway = GatewayFactory::create('SecurePay_DirectPost');
		$gateway->initialize(Config::get('securepay', array()));

		$params['firstName'] = $first_name;
		$params['lastName'] = $last_name;

		$params['currency'] = 'AUD';
		$params['transactionId'] = $order_id;
		$params['transactionReference'] = $order_id;
		$params['amount'] = $this->payment['Amount'];
		//$params['amount'] = 11.05;
		$params['clientIp'] = Request::getClientIp();
		$params['returnUrl'] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://'  . $_SERVER["HTTP_HOST"] .'/backend/booking/completeCcPurchase';

		$securepay = $gateway->purchase($params)->send();
		$data = $securepay->getRedirectData();
		$result = array(
			'SecurePayUrl' => $securepay->getRedirectUrl(),
			'MerchantId' => $data['EPS_MERCHANT'],
			'TxnType' => $data['EPS_TXNTYPE'],
			'TimeStamp' => $data['EPS_TIMESTAMP'],
			'FingerPrint' => $data['EPS_FINGERPRINT'],
			'ResultUrl' => $data['EPS_RESULTURL'],
			'Redirect' => $data['EPS_REDIRECT'],
			'RemoteIp' => $data['EPS_IP'],
			'ReferenceId' => $data['EPS_REFERENCEID'],
			'returnUrl' => $data['EPS_RESULTURL']
			);

		return $result;
		
	}
*/

	protected function CreditCardPayment()
	{		

		$status = $this->payment['PaymentStatus'] ? Utils::StatusId('Invoice', 'Paid') : Utils::StatusId('Invoice', 'Open');
		$invoice = $this->CreateInvoice($status, 'Paid by Credit Card');
		$this->CreatePayment(Utils::PaymentMethodId('CC'), 'Paid by Credit Card');	
	}
	
	protected function PaidPayPalTerminal()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid PayPal Terminal');		
		$this->CreatePayment(Utils::PaymentMethodId('PAYPALTERM'), 'Paid PayPal Terminal');		
	}
	
	protected function PaidManualEftPos()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid Manual EFTPOS');		
		$this->CreatePayment(Utils::PaymentMethodId('EFTPOS'), 'Paid Manual EFTPOS');		
	}
	
	protected function PaidChequeDeposit()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid cheque deposit');		
		$this->CreatePayment(Utils::PaymentMethodId('CHEQUE'), 'Paid cheque deposit');			
	}
	
	protected function PaidDirectDeposit()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid by direct deposit');	
		$this->CreatePayment(Utils::PaymentMethodId('DEPOSIT'), 'Paid by direct deposit');	
	}
	
	protected function PaidCashAtClass()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid cash at class');		
		$this->CreatePayment(Utils::PaymentMethodId('CASH'), 'Paid cash at class');	
	}
	
	protected function PaidCashNotAtClass()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid cash not at class');		
		$this->CreatePayment(Utils::PaymentMethodId('CASH2'), 'Paid cash not at class');	
	}
	
	protected function AgentToPay()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Open'), 'Agent to Pay');
	}
	
	protected function PayLater()
	{
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Open'), 'Pay Later');
	}	
	
	protected function Adjustment()
	{
		$this->CreatePayment(Utils::PaymentMethodId('ADJUST'), 'Backend Adjustment');	
	}
	
	protected function Refund()
	{
		$this->CreatePayment(Utils::PaymentMethodId('REFUND'), 'Backend Refund');	
	}
	
	protected function CreateInvoice($status, $comments)
	{
		
		//$order = Order::find($this->order->id);	
		$this->order->update(array('status_id'=> Utils::StatusId('Order', 'Invoiced')));	
		
		$input = array(
			'id' => null,
			'order_id' => $this->order->id,
			'invoice_date' => date('Y-m-d'),
			'comments' => $comments,
			'status_id' => $status
			);
			
		$invoice = Invoice::create($input);
		
		//$this->CreateInvoicePdf();
		
		return $invoice;
		
	}
	
	public function CreateCreditNote($order, $comments)
	{		
		if ($order->current_invoice)
		{		
			$input = array(
				'id' => null,
				'invoice_id' => $order->current_invoice->id,
				'creditnote_date' => date('Y-m-d'),
				'comments' => $comments,
				'total' => $order->total
				);
			
			CreditNote::create($input);
			$order->current_invoice->update(array('status_id'=> Utils::StatusId('Invoice', 'Credit Note')));	
		}
	}
	
	protected function CreatePayment($payment_method, $comments, $result = null)
	{
		
		if (isset($this->payment['PaymentStatus']) && $this->payment['PaymentStatus'])
			$status = Utils::StatusId('Payment', 'OK');
		else
			$status = Utils::StatusId('Payment', 'Failed');
		
		$input = array(
			'id' => null,
			'order_id' => $this->order->id,
			'payment_date' => isset($this->payment['PaymentDate']) ? $this->payment['PaymentDate'] : date('Y-m-d'),
			'payment_method_id' => $payment_method,
			'backend' => isset($this->payment['Backend']) ? $this->payment['Backend'] : 0,
			'IP' => $_SERVER['REMOTE_ADDR'],
			'comments' => $comments,
			'instalment' => 1,
			'gateway_id' => 1,
			'gateway_response' => $this->payment['GatewayResponse'],
			'status_id' => $status,
			'total' => isset($this->payment['TotalToPay']) ? $this->payment['TotalToPay'] : $this->payment['Amount'],
			'user_id' => Sentry::check() ? Sentry::getUser()->id : null
			);
		
		$payment = Payment::create($input);
				
	}
	
	protected function CreateVoucherPayment($voucherId, $voucherValue)
	{
		
		$status = Utils::StatusId('Payment', 'OK');
		$method = Utils::PaymentMethodId('VOUCHER');

		$input = array(
			'id' => null,
			'order_id' => $this->order->id,
			'payment_date' => isset($this->payment['PaymentDate']) ? $this->payment['PaymentDate'] : date('Y-m-d'),
			'payment_method_id' => $method,
			'backend' => isset($this->payment['Backend']) ? $this->payment['Backend'] : 0,
			'IP' => $_SERVER['REMOTE_ADDR'],
			'comments' => 'Paid with Voucher id: ' . $voucherId,
			'instalment' => 0,
			'gateway_id' => null,
			'gateway_response' => null,
			'status_id' => $status,
			'total' => $voucherValue,
			'user_id' => Sentry::check() ? Sentry::getUser()->id : null
			);
		
		$payment = Payment::create($input);
		
		
	}

}

