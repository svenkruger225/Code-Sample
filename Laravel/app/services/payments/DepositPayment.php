<?php namespace App\Services\payments;

use Utils;

class DepositPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid by direct deposit');	
		$this->CreatePayment(Utils::PaymentMethodId('DEPOSIT'), 'Paid by direct deposit');	
		
	}

	
}