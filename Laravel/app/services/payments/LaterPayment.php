<?php namespace App\Services\payments;

use Utils;

class LaterPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Open'), 'Pay Later');
		$this->setParameter('TotalToPay', 0);
		$this->CreatePayment(Utils::PaymentMethodId('LATER'), 'Pay Later');	
		
	}

	
}