<?php namespace App\Services\payments;

use Utils, Voucher;

class VoucherPayment extends AbstractPayment
{
	
	public function process($order, $parameters)
	{
		$this->initialize($parameters);
		$this->order = $order;
		$voucherId = $this->getParameter('VoucherId');
		$voucherValue = $this->getParameter('VoucherValue');
		
		$voucher = Voucher::find($voucherId);
		if ($voucher->isValid())
		{
			$voucher->update(array('status_id'=> Utils::StatusId('Voucher', 'Used'), 'active' => '0'));
			
			$this->setParameter('PaymentStatus', true);
			$this->setParameter('TotalToPay', $voucherValue);
			$this->CreatePayment(Utils::PaymentMethodId('VOUCHER'), 'Paid with Voucher id: ' . $voucherId);	
		} 
		else
		{
			throw new Exception($voucher->message);
		}
		
		
	}

	
}