<?php namespace App\Services\payments;

use Utils;

class AgentPayment extends AbstractPayment
{

	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$this->CreateInvoice(Utils::StatusId('Invoice', 'Open'), 'Agent to Pay');
		$this->setParameter('TotalToPay', 0);
		$this->CreatePayment(Utils::PaymentMethodId('AGENT'), 'Pay Later - Agent');	
		
	}

	
}