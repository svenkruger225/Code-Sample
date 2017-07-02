<?php namespace App\Services\payments;

use Utils;

class AdjustPayment extends AbstractPayment
{


	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		
		$total_to_pay = $this->getParameter('TotalToPay');
		
		//$total_to_pay = isset($this->payment['TotalToPay']) ? $this->payment['TotalToPay'] : $this->payment['Amount'];
		$total_to_pay = $total_to_pay > 0 ? $total_to_pay * -1 : $total_to_pay;		

		$this->CreatePayment(Utils::PaymentMethodId('ADJUST'), 'Backend Adjustment');	
		
	}

	
}