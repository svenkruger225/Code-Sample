<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Mail, Message;
use Order, Roster, EmailService,SmsService;

class SendMessage {

	protected $order;

	public function __construct()
	{
	}

	public function fire($job, $data) 
	{

		$this->order = Order::find($data['order_id']);
		$isPublicBooking = filter_var($data['IsPublicBooking'], FILTER_VALIDATE_BOOLEAN);
		$isGroupBooking = filter_var($data['IsGroupBooking'], FILTER_VALIDATE_BOOLEAN);
		$isAgentBooking = filter_var($data['IsAgentBooking'], FILTER_VALIDATE_BOOLEAN);
		
		\PdfService::save('Order', 'backend.invoices.invoice', '/invoices/', 'invoice-', $this->order->current_invoice->id);	

		Log::info('Sending Messages for order : '. $this->order->id . ' data: ' . $isGroupBooking . '|' . $data['SendEmail'] . '|' . $data['SendSMS'] . '|' . json_encode($data));

		$this->AdminBookingEmail($data, $isGroupBooking);
		
		if ($isGroupBooking)
		{
			$this->GroupBookingEmail($data);
		}
		else if ($isAgentBooking)
		{

			$this->AgentBookingEmail($data);
		}
		else 
		{
			$this->PublicBookingEmail($data);
		}	
		//@TODO uncomment before deployment
		$job->delete();

	}
	
	private function AdminBookingEmail($data, $isGroupBooking = false)
	{
		if (filter_var($data['SendAdmin'], FILTER_VALIDATE_BOOLEAN))
		{
			EmailService::sendToAdmin($this->order, $isGroupBooking, null, $data['success'],$data['HasVouchers']);
		}
	}
	
	private function GroupBookingEmail($data)
	{
		EmailService::sendToGroupBookingOwner($this->order->rosters);
	}
	
	private function AgentBookingEmail($data)
	{
		EmailService::sendToAgentBookingOwner($this->order);
		if((filter_var($data['SendEmail'], FILTER_VALIDATE_BOOLEAN) || filter_var($data['SendSMS'], FILTER_VALIDATE_BOOLEAN)))
		{
			foreach($this->order->rosters as $roster) 
			{
				if (filter_var($data['SendEmail'], FILTER_VALIDATE_BOOLEAN))
                {
                    if(isset($this->order->agent) && in_array($this->order->agent->code,array('RSA','RSAM','RSAS','RSAB')))
                    {
                        EmailService::sendToAgentBookingStudent($roster, $this->order);
                    }
                    else
                    {
                        EmailService::sendToCompanyBookingStudent($roster, $this->order);
                    }

                }

				if (filter_var($data['SendSMS'], FILTER_VALIDATE_BOOLEAN))
					SmsService::send($roster);
			}			
		}
	}
	
	
	private function PublicBookingEmail($data)
	{
		if((filter_var($data['SendEmail'], FILTER_VALIDATE_BOOLEAN) || filter_var($data['SendSMS'], FILTER_VALIDATE_BOOLEAN)))
		{
			foreach($this->order->rosters as $roster) 
			{
				if (filter_var($data['SendEmail'], FILTER_VALIDATE_BOOLEAN))
					EmailService::sendToCustomer($roster);

				if (filter_var($data['SendSMS'], FILTER_VALIDATE_BOOLEAN))
					SmsService::send($roster);
			}
			
			if ($this->order->vouchers->count())
			{
				if (filter_var($data['SendEmail'], FILTER_VALIDATE_BOOLEAN))
					EmailService::sendVoucherToCustomer($this->order);
			}
			
		}
	}
}