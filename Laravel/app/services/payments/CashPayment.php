<?php namespace App\Services\payments;

use Utils;

class CashPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid cash at class');		
		$this->CreatePayment(Utils::PaymentMethodId('CASH'), 'Paid cash at class');	
		
	}

	
}