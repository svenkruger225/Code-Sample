<?php namespace App\Services\payments;

use Utils;

class ChequePayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Paid'), 'Paid cheque deposit');		
		$this->CreatePayment(Utils::PaymentMethodId('CHEQUE'), 'Paid cheque deposit');			
		
	}

	
}