<?php namespace App\Services\payments;

use Utils;

class Cash2Payment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid cash not at class');		
		$this->CreatePayment(Utils::PaymentMethodId('CASH2'), 'Paid cash not at class');	
		
	}

	
}