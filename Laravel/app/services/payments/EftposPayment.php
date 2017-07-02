<?php namespace App\Services\payments;

use Utils;

class EftposPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid Manual EFTPOS');		
		$this->CreatePayment(Utils::PaymentMethodId('EFTPOS'), 'Paid Manual EFTPOS');		
		
	}

	
}