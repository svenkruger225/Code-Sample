<?php namespace App\Services\payments;

use Utils;

class PaywayPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{

		$this->initialize($parameters);
		$this->order = $order;
		
		$status = $this->getParameter('PaymentStatus') ? Utils::StatusId('Invoice', 'Paid') : Utils::StatusId('Invoice', 'Open');

		$this->CreateInvoice($status, 'Paid by PayWay Net');

		$this->CreatePayment(Utils::PaymentMethodId('CC'), 'Paid by PayWay Net ' . $this->getParameter('Comments'));	

	}

	
}