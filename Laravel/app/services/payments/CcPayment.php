<?php namespace App\Services\payments;

use Utils;

class CcPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$status = $this->getParameter('PaymentStatus') ? Utils::StatusId('Invoice', 'Paid') : Utils::StatusId('Invoice', 'Open');

		if (empty($this->payment['PaymentErrorCode']) || $this->payment['PaymentErrorCode'] != '11607') // duplicate transaction
		{
			$this->CreateInvoice($status, 'Paid by Credit Card');
		}

		$this->CreatePayment(Utils::PaymentMethodId('CC'), 'Paid by Credit Card');	
		
	}

	
}