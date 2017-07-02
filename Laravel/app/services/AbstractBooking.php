<?php namespace App\Services;

use Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, Session, Queue, Utils;
use Customer, GroupBooking, Purchase, Order, Item, ItemType,CourseInstance, Roster, OnlineRoster, Referrer, ReferrerLog;
use DB, Mail, Voucher, PayPalSession, PayWaySession, PdfService, Location, Log, Certificate;
use Omnipay\Common\CreditCard;
use Omnipay\Common\GatewayFactory;
use App\Services\payments\PaymentFactory;
use App\Services\XmlParser;
use App;

abstract class AbstractBooking implements \BookingInterface
{

	protected $scheme;
	protected $booking;
	protected $customers;
	protected $instances;
	protected $payment;
	
	protected $order;
	protected $user;
	protected $customer;
	public $securepay;
	protected $cancelUrl;
	protected $returnUrl;
	
	protected $IsGroupBooking;
	protected $IsPublicBooking;
	protected $IsOnlineBooking;
	protected $IsProductPurchase;
	protected $OrderType;
	

	public function __construct()
	{
		Input::merge(Utils::array_map_recursive("trim", Input::all(), array('first_name','last_name','email', 'FirstName','LastName','Email')));

		$this->instances = Input::get('Instances', array());
		$this->booking = Input::except('Instances','Payment');	
		$this->payment = Input::get('Payment');	
		$this->order = null;
		if (!empty($this->booking['OrderId']))
			$this->order = Order::find($this->booking['OrderId']);


		$this->IsGroupBooking = false;
		$this->IsOnlineBooking = false;
		$this->IsPublicBooking = true;
		$this->IsProductPurchase = false;
		$this->OrderType = 'Public';
		
		if (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] != 'artisan')
		{
			$this->scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
			$this->cancelUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/cancelPayPalPurchase?IsGroupBooking=false&IsPublicBooking=true&IsProductPurchase=false&IsOnlineBooking=false';
			$this->returnUrl = $this->scheme  . $_SERVER["HTTP_HOST"] .'/api/booking/completePayPalPurchase?IsGroupBooking=false&IsPublicBooking=true&IsProductPurchase=false&IsOnlineBooking=false';
		}
	}

	// start PAYPAL

	public function initiatePayPalPurchase()
	{
	}

	public function submitToPayPal()
	{
		
		$gateway = GatewayFactory::create('PayPal_Express');
		$gateway->initialize(Config::get('paypal', array()));
		$voucherCourse = '';
		if (!empty($this->booking['VoucherId']))
		{
			$voucher = Voucher::find($this->booking['VoucherId']);
			if ($voucher->isValid())
				$voucherCourse = $voucher->course_id;
		}

		$items = array();
		foreach ($this->order->active_items as $item)
		{
			$price = $item->price;
			if (!empty($voucherCourse) && $voucherCourse == $item->instance->course_id )
				$price = 0;
			
			$item_data = array(
				'name' => $item->id,
				'desc' => $item->description,
				'qty' => $item->qty,
				'amt' => $price
				);

			array_push($items, $item_data);
		}

		$card = new CreditCard();			
		$card->setFirstName($this->order->customer->first_name);
		$card->setLastName($this->order->customer->last_name);
		$card->setAddress1($this->order->customer->address);
		$card->setCity($this->order->customer->city);
		$card->setPostcode($this->order->customer->post_code);
		$card->setState($this->order->customer->state);
		$card->setCountry($this->order->customer->country);
		$card->setPhone($this->order->customer->mobile);
		$card->setEmail($this->order->customer->email);	
		
		//$card->setAddress1('optional');
		//$card->setCity('optional');
		//$card->setPostcode('9999');
		//$card->setState('New South Wales');
		//$card->setCountry('AU');
		//$card->setPhone($this->order->customer->mobile);
		//$card->setEmail($this->order->customer->email);	
		

		$params = array(
			'cancelUrl' => $this->cancelUrl,
			'returnUrl' => $this->returnUrl,
			'amount' =>  $this->booking['TotalToPay'],
			'currency' => 'AUD',
			'card' => $card,
			'items' => $items
			);

		$response = $gateway->purchase($params)->send();

		$token = $response->getTransactionReference();
		
		$error = $response->getMessage();
		
		if ($this->order)
			$this->order->update(array('comments' => 'Submitted to Paypal'));		
		
		$content = json_encode(array(
			'OrderId' => $this->order->id,
			'VoucherId' => $this->booking['VoucherId'],
			'VoucherValue' => $this->booking['VoucherValue'],
			'Backend' => $this->booking['Backend'],
			'Gst' => $this->booking['Gst'],
			'SendEmail' => $this->booking['SendEmail'],
			'SendSMS' => $this->booking['SendSMS'],
			'TotalToPay' => $this->booking['TotalToPay'],
			'Amount' => $this->booking['TotalToPay'],
			'FrontendUrl' => $this->IsPublicBooking ? $this->booking['FrontendUrl'] : null,
			'user_id'  => Sentry::check() ? Sentry::getUser()->id : null
			));

		PayPalSession::create(
			array(
					'order_id' => $this->order->id,
					'session_id' => $token,
					'session_content' => $content,
					'returned_page' => 'SubmitToPayPal'			
					)
				);

		$response->redirect(); 		

	}

	public function cancelPayPalPurchase()
	{
		$token = Input::get('token');
		if(isset($token))
		{
			
			$session = PayPalSession::where('session_id',$token)->first();
			$session_data = json_decode($session->session_content, true);
			
			$this->order = Order::find($session_data['OrderId']);
			$this->customer = $this->order->customer;
			
			$response_data = array('response'=> 'Transaction cancelled by customer'); 
			$gateway_response = json_encode($response_data);
			$this->payment['PaymentMethod'] = 'CC';
			$this->payment['PaymentStatus'] = false;
			$this->payment['PaymentMessage'] = 'Transaction cancelled by customer';
			$this->payment['GatewayResponse'] = $gateway_response;
			$this->payment['TotalToPay'] = $session_data['TotalToPay'];
			foreach ($session_data as $key => $value)
				$this->payment[$key] = $value;

			$that = $this;
			DB::transaction(function() use(&$that)
				{
					$that->processPayment();	
					$that->updateOrderComments('Paypal cancelled by customer');		
				});
			
			$session->update(array('returned_page'=>'cancelPay'));
			//return $this->payment;
			
		}
		
		$this->sendMessages();
		
		$this->order->update(array('status_id' => Utils::StatusId('Order', 'Cancelled')));
		
		Log::info("Order: " . $this->order->id . ", Paypal cancelled by customer");
		
		return $this->payment;
	}

	public function completePayPalPurchase()
	{
		$token = Input::get('token');
		if(isset($token))
		{
			$session = PayPalSession::where('session_id',$token)->first();
			$session_data = json_decode($session->session_content, true);
			
			$this->order = Order::find($session_data['OrderId']);			
			$this->customer = $this->order->customer;

			$gateway = GatewayFactory::create('PayPal_Express');
			$gateway->initialize(Config::get('paypal', array()));

			$response = $gateway->completePurchase(
				array(
						'amount' =>  $this->order->total,
						'currency' => 'AUD'
						)
					)->send();

			$response_data = $response->getData(); 
			$gateway_response = json_encode($response_data);
			$this->payment['PaymentMethod'] = 'Paypal';
			$this->payment['PaymentStatus'] = $response->isSuccessful();
			$this->payment['PaymentMessage'] = $response->getMessage();
			$this->payment['PaymentErrorCode'] = $response->getErrorCode();			
			$this->payment['GatewayResponse'] = $gateway_response;
			foreach ($session_data as $key => $value)
				$this->payment[$key] = $value;
			
			$that = $this;
			DB::transaction(function() use(&$that)
				{
					$that->processVoucher();
					$that->processPayment();
					$that->updateVouchers();
					$that->updateOrderComments('');		
				});
			
			$this->sendMessages();
			$session->update(array('paypal_response'=> $gateway_response, 'returned_page'=>'completePay'));
			
			Log::info("Order: " . $this->order->id . ", Paypal completed");
			
			return $this->payment;
		}
	}

	public function processOpenPayPalPurchase($token)
	{
		Log::info('start processOpenPayPalPurchase token: ' . $token);
		if(isset($token))
		{
			$session = PayPalSession::where('session_id',$token)->first();
			$session_data = json_decode($session->session_content, true);
			foreach ($session_data as $key => $value)
				$this->payment[$key] = $value;
			
			$this->order = Order::find($session_data['OrderId']);
			
			if (!$this->order->isPaid())
			{
				
				$this->customer = $this->order->customer;

				$gateway = GatewayFactory::create('PayPal_Express');
				$gateway->initialize(Config::get('paypal', array()));

				$response = $gateway->retrievePurchaseDetails(array( 'token' => $token))->send();
				$details_response = $response->getData();
				Log::info('GetExpressCheckoutDetails: ' . json_encode($details_response));
				
				if (isset($details_response['CHECKOUTSTATUS']) && $details_response["CHECKOUTSTATUS"] == "PaymentActionNotInitiated")
				{
					$this->updateOrderComments('Paypal Payment Action Not Initiated');		
					$this->payment['PaymentMessage'] = 'Daily Task check - Paypal Payment Action Not Initiated';
					$session->update(array('returned_page'=>'completePayDailyTask'));
				}
				else
				{
					$payer_id = isset($details_response['PAYERID']) ? $details_response['PAYERID'] : null;

					$response = $gateway->completePurchase(
						array(
								'token' => $token,
								'payerId' => $payer_id,
								'amount' =>  $this->order->total,
								'currency' => 'AUD'
								)
							)->send();

					$response_data = $response->getData();

					Log::info('DoExpressCheckoutPayment: ' . json_encode($response_data));
					
					$gateway_response = json_encode($response_data);
					$this->payment['PaymentMethod'] = 'Paypal';
					$this->payment['PaymentStatus'] = $response->isSuccessful();
					$this->payment['PaymentMessage'] = $response->getMessage();
					$this->payment['PaymentErrorCode'] = $response->getErrorCode();			
					$this->payment['GatewayResponse'] = $gateway_response;
					
					$that = $this;
					DB::transaction(function() use(&$that)
						{
							$that->processVoucher();
							$that->processPayment();
							$that->updateVouchers();
							$that->updateOrderComments('');		
						});
					
					if ($this->payment['PaymentStatus'])
					{
						$this->sendMessages();
					}
					
					$session->update(array('paypal_response'=> $gateway_response, 'returned_page'=>'completePayDailyTask'));
				}
			}
			else
			{
				$this->updateOrderComments('Daily Task check - Already Paid');		
				$this->payment['PaymentMessage'] = 'Daily Task check - Already Paid';
			}
			
			Log::info("Order: " . $this->order->id . ", Paypal completed on Daily Task");
			Log::info('end processOpenPayPalPurchase token: ' . $token);
			
			return $this->payment;
		}
	}

	// end PAYPAL
	
	// start PAYWAY NET

	public function payWayPurchase()
	{
	}

	public function submitToPayWay()
	{	
		$gateway = GatewayFactory::create('PayWayNet');
		$gateway->initialize(Config::get('paywaynet', array()));
		$voucherCourse = '';
		if (!empty($this->booking['VoucherId']))
		{
			$voucher = Voucher::find($this->booking['VoucherId']);
			if ($voucher->isValid())
				$voucherCourse = $voucher->course_id;
		}

		$items = array();
		if ($this->order->paid == 0)
		{
			foreach ($this->order->active_items as $item)
			{
				$price = $item->isDiscount() ? $item->price * -1 : $item->price;
				if (!empty($voucherCourse) && $voucherCourse == $item->instance->course_id )
					$price = 0;
				
				$item_data = array(
					'name' => $item->id,
					'desc' => $item->description,
					'qty' => $item->qty,
					'amt' => $price
					);

				array_push($items, $item_data);
			}
		}
		else
		{
				$item_data = array(
				'name' => '999',
				'desc' => 'Partial Payment for order [' . $this->order->id . '], original total (' . $this->order->total . '), already paid(' . $this->order->paid . ')' ,
				'qty' => '1',
				'amt' => $this->order->owing
				);

			array_push($items, $item_data);
		}

		$session_date = new \DateTime();
		$payway_session_id = Utils::GeneratePassword(20,20) . $session_date->format("U");
		
		$params = array(
			'payway_session_id' => $payway_session_id,
			'payment_reference' => $this->order->id,
			'total_amount' => $this->payment['TotalToPay'],
			'items' => $items
		);
		//Log::info("Params: " . json_encode($params));

		$response = $gateway->purchase($params)->send();

		$this->payway_token = $response->getToken();
		$this->payway_url = $gateway->getBaseUrl() . 'MakePayment?'  . "biller_code=" . $gateway->getBillerCode() . "&token=" . urlencode( $this->payway_token );
		
		$this->updateOrderComments('Submitted to Payway');		
		
		$content = json_encode(array(
			'OrderId' => $this->order->id,
			'Token' => $this->payway_token,
			'VoucherId' => isset($this->booking['Voucher']) ? $this->booking['Voucher']['id'] : '',
			'VoucherValue' => isset($this->booking['Voucher']) ? $this->booking['Voucher']['total'] : '',
			'Backend' => $this->booking['Backend'],
			'Gst' => $this->booking['Gst'],
			'SendEmail' => $this->booking['SendEmail'],
			'SendSMS' => $this->booking['SendSMS'],
			'TotalToPay' => $this->payment['TotalToPay'],
			'Amount' => $this->payment['TotalToPay'],
			'FrontendUrl' => $this->IsPublicBooking ? $this->payment['FrontendUrl'] : null,
			'IsPublicBooking'  => $this->payment['IsPublicBooking'],
			'IsGroupBooking'  => $this->payment['IsGroupBooking'],
			'IsProductPurchase'  => $this->payment['IsProductPurchase'],
			'IsMachineHire'  => $this->payment['IsMachineHire'],
			'user_id'  => Sentry::check() ? Sentry::getUser()->id : null
			));

		PayWaySession::create(
			array(
					'order_id' => $this->order->id,
					'session_id' => $payway_session_id,
					'session_content' => $content,
					'returned_page' => 'SubmitToPayWay'			
					)
				);
			
	}

	public function cancelPayWayPurchase()
	{
		$token = Input::get('payway_session_id');
		if(isset($token))
		{
			
			$session = PayWaySession::where('session_id',$token)->first();
			$session_data = json_decode($session->session_content, true);
			
			$this->order = Order::find($session_data['OrderId']);
			$this->customer = $this->order->customer;
			
			$response_data = array('response'=> 'Transaction cancelled by customer'); 
			$gateway_response = json_encode($response_data);
			$this->payment['PaymentMethod'] = 'PayWay';
			$this->payment['PaymentStatus'] = false;
			$this->payment['PaymentMessage'] = 'Transaction cancelled by customer';
			$this->payment['GatewayResponse'] = $gateway_response;
			$this->payment['TotalToPay'] = $session_data['TotalToPay'];
			foreach ($session_data as $key => $value)
				$this->payment[$key] = $value;

			$that = $this;
			DB::transaction(function() use(&$that)
				{
					$that->processPayment();	
					$that->updateOrderComments('Payway cancelled by customer');		
				});

			$session->update(array('returned_page'=>'cancelPayWay'));
			//return $this->payment;
		}
		
		$this->sendMessages();
		
		
		$this->order->update(array('status_id' => Utils::StatusId('Order', 'Cancelled')));

		Log::info("Order: " . $this->order->id . ", Payway cancelled by customer");
		
		return $this->payment;
	}

	public function completePayWayPurchase()
	{
		Log::info("Start completePayWayPurchase");
		
		$encryptedParametersText = Input::get('EncryptedParameters', null);
		$signatureText = Input::get('Signature', null);
		$encryptionKey = Config::get('paywaynet.encryption_key', null);

		$parameters = Utils::decrypt_parameters( $encryptionKey, $encryptedParametersText, $signatureText );

		if(isset($parameters['payway_session_id']))
		{
			$this->order = Order::lockForUpdate()->find($parameters['payment_reference']);			
			$this->customer = $this->order->customer;
			
			$session = PayWaySession::where('session_id',$parameters['payway_session_id'])->first();
			$session_data = json_decode($session->session_content, true);

			foreach ($session_data as $key => $value) {
				$this->payment[$key] = $value;
			}
			
			if ( isset($this->payment['Amount']) &&  (float)$parameters['payment_amount'] != (float)$this->payment['Amount'])
			{
				$this->payment['Amount'] = $parameters['payment_amount'];
				$this->payment['TotalToPay'] = $parameters['payment_amount'];
			}
			
			if ( isset($this->payment['OrderId']) && $parameters['payment_reference'] != $this->payment['OrderId'])
			{
				$this->payment['OrderId'] = $parameters['payment_reference'];
			}
			
			if ($this->order->comments == 'Submitted to Payway' || (empty($this->order->Comments) && !$this->order->isPaid())	)
			{				
				$this->updateOrderComments('processing...');	
					
				$gateway_response = json_encode($parameters);
				$this->payment['PaymentMethod'] = 'PayWay';
				$this->payment['PaymentStatus'] = $parameters['payment_status'] == 'approved' ? true : false;
				$this->payment['PaymentMessage'] = $parameters['response_text'];
				
				$this->payment['Comments'] = '';
				if ( $parameters['payment_status'] == 'approved' )
				{
					if ( (float)$parameters['payment_amount'] != (float)$this->order->total)
					{
						$this->payment['Comments'] = 'Your payment has been approved but the amount you paid was different from the amount expected.';
					}
					
				}
				$this->payment['GatewayResponse'] = $gateway_response;
				
				$that = $this;
				DB::transaction(function() use(&$that)
					{
						$that->processVoucher();
						$that->processPayment();
						$that->updateVouchers();
						$that->updateOrderComments('');		
					});
				
				$this->sendMessages();
				$session->update(array('payway_response'=> $gateway_response, 'returned_page'=>'completePayWay'));
				
				Log::info("Order: " . $this->order->id . ", Payway completed");
				//Log::info("this->payment: " . json_encode($this->payment));
			}
			else
			{
				Log::info("Order: " . $this->order->id . ", Already completed");
			}
			return $this->payment;
		}
	}

	public function queuePayWayServerResponse()
	{
		Log::info("Start queuePayWayServerResponse");

		$parameters = Input::all();
		
		Queue::later(10, 'PayWayQueue', $parameters); // delay by 10 seconds
		//Queue::push('PayWayQueue', $parameters);
		
		Log::debug("payway server response queued [" . $parameters['payment_reference'] ."]");

	}
		
	public function processPayWayResponse($order, $parameters)
	{
		Log::info("Start processPayWayResponse");
		Log::error("Params: " . json_encode($parameters));
		
		if ($parameters['username'] != Config::get('paywaynet.username', 'n/a') || 
			$parameters['password'] != Config::get('paywaynet.password', 'n/a') )
		{
			Log::error("Order: " . $parameters['payment_reference'] . ", Incorrect Username and password (" . $parameters['username'] . ": " . $parameters['password'] . ")" );
			return false;
		}
		else
		{
			if($order)
			{
				$this->order = $order;			
				$this->customer = $this->order->customer;
				
				if (
					$this->order->comments == 'Submitted to Payway' ||
					(empty($this->order->Comments) && !$this->order->isPaid())
				)
				{
					$this->updateOrderComments('processing .....');		
					$session = PayWaySession::where('session_id',$parameters['payway_session_id'])->first();
					$session_data = json_decode($session->session_content, true);
					
					$gateway_response = json_encode($parameters);
					$this->payment['PaymentMethod'] = 'PayWay';
					$this->payment['PaymentStatus'] = $parameters['payment_status'] == 'approved' ? true : false;
					$this->payment['PaymentMessage'] = $parameters['response_text'];
					foreach ($session_data as $key => $value)
						$this->payment[$key] = $value;
					
					$this->payment['Comments'] = '';
					if ( $parameters['payment_status'] == 'approved' )
					{
						if ( (float)$parameters['payment_amount'] != (float)$this->order->total)
						{
							$this->payment['Comments'] = 'Your payment has been approved but the amount you paid was different from the amount expected.';
						}
					}
					$this->payment['GatewayResponse'] = $gateway_response;
					
					$that = $this;
					DB::transaction(function() use(&$that)
						{
							$that->processVoucher();
							$that->processPayment();
							$that->updateVouchers();
							$that->updateOrderComments('');		
						});
					
					$this->sendMessages();
					$session->update(array('payway_response'=> $gateway_response, 'returned_page'=>'serverCompletePayWay'));
					
					Log::info("Server to Server - Order: " . $this->order->id . ", Payway completed");
					//Log::info("this->payment: " . json_encode($this->payment));

				}
				else
				{
					Log::info("Order: " . $this->order->id . ", Already completed");
				}
				
				return true;
			}
			else
			{
				return false;
			}

		}
	
	}

    /**
     * get voucher real price
     * @return mixed
     */
    protected function _getVoucherPrice()
    {
        $voucherId = $this->payment['VoucherId'];
        $voucherObj = Voucher::find($voucherId);
        $orderId = $voucherObj['order_id'];
        $itemObj = Item::getVoucher($orderId,$voucherId);
        return $itemObj['price'];
    }

    public function getVoucher()
    {

        if (!empty($this->payment['Voucher']))
        {
            $this->payment['VoucherId'] = $this->payment['Voucher']['id'];
            if (!empty($this->payment['VoucherId']))
            {
                $this->payment['Voucher']['total'] =  $this->_getVoucherPrice();
            }
            $this->payment['VoucherValue'] = $this->payment['Voucher']['total'];
        }
        Log::debug("get voucher called".json_encode($this->payment));
    }

	// end PAYWAY NET
	

	public function processPurchase()
	{
		Log::info("Start Order creation on processPurchase");

        $this->getVoucher();

		$this->payment['PaymentStatus'] = true;
		$this->payment['GatewayResponse'] = '';
		
		$this->transactionalPurchase();

		
		//try {
		//	$this->transactionalPurchase();
		//	//Log::info("will send messages normally");
		//}
		//catch (\CourseInstanceValidationException $ex) 		
		//{
		//	Log::error($ex->getMessage());
		//	throw $ex;
		//}
		//catch (\Exception $e) 		
		//{
		//	//Log::error("should send error message " . $e->getMessage());			
		//	$this->order = null;
		//	$this->payment['PaymentStatus'] = false;
		//	$this->payment['PaymentMessage'] = $e->getMessage();
		//}
		
		$this->sendMessages();
		
		if($this->order)
			Log::info("Order: " . $this->order->id . " created");
		else
			Log::error("Problem creating order");
		
		return ($this->order) ? $this->order->id : null;

	}
	
	public function transactionalPurchase()
	{	

	}
	
	// Public Booking functions
	
	public function updateVouchers()
	{
		if (isset($this->payment['PaymentStatus']) && $this->payment['PaymentStatus'])
		{
			if ($this->order && $this->order->vouchers->count())
			{
				foreach($this->order->vouchers as $voucher) 
				{
					try
					{
						Log::debug("update voucher [" . $voucher->id ."]");
						set_time_limit(240);
						$voucher->update(array('active'=> 1));
						PdfService::save('Voucher', 'backend.vouchers.voucher', '/vouchers/', 'voucher-', $voucher->id);	
					}
					catch (Exception $e)
					{
						Log::error($e->getMessage());
					}
				}	
			}
			else
			{
				foreach($this->instances as $input) 
				{
					set_time_limit(240);
					if($input['isVoucher'])
					{
						foreach($input['vouchersIds'] as $id)
						{
							try
							{
								Log::debug("update vouchersIds - id [" . $id ."]");
								$voucher = Voucher::find($id);	
								$voucher->update(array('active'=> 1));
								PdfService::save('Voucher', 'backend.vouchers.voucher', '/vouchers/', 'voucher-', $voucher->id);	
							}
							catch (Exception $e)
							{
								Log::error($e->getMessage());
							}
						}
					}						
				}	
			}
		}	
		
		Log::debug("finish updateVouchers [" . $this->order->id ."]");
	}
	
	public function createOrder()
	{
        	if ($this->IsProductPurchase)
			$total = $this->booking['Total'];
		else
			$total = $this->useOnlinePrice() ? $this->booking['TotalOnLine'] : $this->booking['TotalOffLine'];
                
		$user = Sentry::getUser();
		// if we already have an order
		if ($this->order)
		{

			// if we have an order we try to find a match for the current instance
			if ($this->order->active_items->count())
				foreach($this->order->active_items as $item)
					$item->update(array('active' => 0));

			// and we have a currentInvoice
			// create a credit note
			if ($this->order->current_invoice)
			{		
				Utils::CreateCreditNote($this->order, 'Updating Order Details');
			}
			// reopen the order and update total
			$order_data = array(
				'customer_id' => $this->customer->id,
				'agent_id' => !empty($this->booking['Agent']) && !empty($this->booking['Agent']['id']) ? $this->booking['Agent']['id'] : null,
				'company_id' => !empty($this->booking['Company']) && !empty($this->booking['Company']['id']) ? $this->booking['Company']['id'] : null,
				'comments' => '',
				'status_id' => Utils::StatusId('Order', 'Open'),
				'gst' => $this->IsProductPurchase ? round($total / 11, 2) : 0,
				'total' => $total
				);

			$this->order->update($order_data);
		}
		else
		{
			// get the user
			$user_id = ($this->booking['Backend'] == '1' && $user) ? $user->id : null;
			
			// or just create a new order
			$order_data = array(
				'id'=> null,
				'customer_id' => $this->customer->id,
				'purchase_id' => !empty($this->booking['purchaseId']) ? $this->booking['purchaseId'] : null,
				'backend' => $this->booking['Backend'],
				'agent_id' => !empty($this->booking['Agent']) && !empty($this->booking['Agent']['id']) ? $this->booking['Agent']['id'] : null,
				'company_id' => !empty($this->booking['Company']) && !empty($this->booking['Company']['id']) ? $this->booking['Company']['id'] : null,
				'order_date' => date('Y-m-d'),
				'order_type' => $this->OrderType,
				'comments' => '',
				'status_id' => Utils::StatusId('Order', 'Open'),
				'user_id' => $user_id,
				'gst' => $this->IsProductPurchase ? round($total / 11, 2) : 0,
				'total' => $total
				);

			$this->order = Order::create($order_data);
			
			// if  it is a new order create the referrer log
			if (!empty($this->booking['Referrer']))
			{
				if (is_numeric($this->booking['Referrer']))
				{
					$referrer = Referrer::find($this->booking['Referrer']);
					if ($referrer)
					{
						$referrer_log = array(
							'id'=>null,
							'order_id' => $this->order->id,
							'referrer_id' => $referrer->id,
							'referrer_url' => $referrer->url
							);
						ReferrerLog::create($referrer_log);	
					}
				}	
				else
				{
					$parts = parse_url($this->booking['Referrer']);
					// $parts = scheme, host, path, query
					//parse_str($parts['query']);
					//var_dump($parts);
					//exit();
					if (strpos($parts['host'], 'coffeeschool') === FALSE) // we don't want to record if we referrer  from coffeeschool
					{
						$referrer = Referrer::where('url', $this->booking['Referrer'])->first();
						$referrer_log = array(
							'id'=>null,
							'order_id' => $this->order->id,
							'referrer_id' => $referrer ? $referrer->id : null,
							'referrer_url' => $this->booking['Referrer']
							);
						ReferrerLog::create($referrer_log);	
					}
				}
			}	
			
			
		}
		
		$this->createItems();
	
		$this->order->updateOrderTotal();
		
		Log::debug("finish createOrder [" . $this->order->id ."]");


	}	
	
	public function createItems()
	{
		$user = Sentry::getUser();
		$date_hire =  null;
		
		if(count($this->instances) == 0)
		{
			Log::debug("This order is empty, please make sure the booking is properly done, check the classes and try again");
			throw new Exception("This order is empty, please make sure the booking is properly done, check the classes and try again");				
		}		
		
		foreach($this->instances as &$input) 
		{
            if ($this->IsProductPurchase)
            {
                $price = $input['price'];
            }
            else
            {
                $price = $this->useOnlinePrice() ? $input['priceOn'] : $input['priceOff'];
            }
            $voucherValue = NULL;
            //for saving voucher from backend
            if(isset($input['vouchersIds']))
            {
                $voucherValue = $input['vouchersIds'];
            }
            //incase voucher id is received as string then convert it to array for proper json_encode
            // this will fix issue of saving record in db like "123"
            if(isset($input['vouchersIds']) && !is_array($input['vouchersIds']))
            {
                $voucherValue = array((int)$input['vouchersIds']);
            }
            //code will work when booking from frontend with voucher
            if (!empty($this->payment['VoucherId']))
            {

                $voucherId = $this->payment['VoucherId'];
                $voucherObj = Voucher::find($voucherId);
                if($input['courseType'] == $voucherObj['course_id'])
                {
                    $orderId = $voucherObj['order_id'];
                    $itemObj=Item::getVoucher($orderId,$voucherId);
                    //$itemObj = Item::where('order_id', '=' ,$orderId)->where('vouchers_ids' ,'=', '['.$voucherId.']')->first();
                    $price = $itemObj['price'];
                }
                $input['vouchersIds'] = $this->payment['VoucherId'];
                //Set Voucher Id for voucher item
                $courseInstance=CourseInstance::find($input['courseInstance']);
                if($courseInstance['course_id']==$voucherObj['course_id'])
                {
                    $voucherValue = array((int)$input['vouchersIds']);
                }
            }

 			$input['subtotal'] = $this->IsProductPurchase ? $input['qty'] * $price : $input['studentQty'] * $price;
			$item_type_id = $this->IsProductPurchase ? Utils::ItemTypeId('Product') : Utils::ItemTypeId($input['itemType']);
			if ($this->IsProductPurchase && $date_hire == null)
				$date_hire = !empty($input['hire_date']) ? $input['hire_date'] : null;
			
                        
			$description = $this->GetItemDescription($input);

            if(!is_null($voucherValue))
            {
                $voucherValue = json_encode($voucherValue);
            }
			$item_data = array(
				'id'=> null,
				'order_id' => $this->order->id,
				'course_instance_id' => $this->IsPublicBooking || $this->IsOnlineBooking ? $input['courseInstance'] : null,
				'group_booking_id' => $this->IsGroupBooking ? $input['groupId'] : null,
				'product_id' => $this->IsProductPurchase ? $input['id'] : null,
				'vouchers_ids' => $this->IsPublicBooking && !empty($input['vouchersIds']) ? $voucherValue : null,
				'item_type_id' => $item_type_id,
				'description' => $description,
				'comments' => $date_hire,
				'qty' => $this->IsProductPurchase ? $input['qty'] : $input['studentQty'],
				'price' => $price,
				'gst' => $this->IsProductPurchase || filter_var($input['applyGst'], FILTER_VALIDATE_BOOLEAN) ? round($input['subtotal'] / 11, 2) : 0,
				'total' => $input['subtotal'],
				'user_id' => $user ? $user->id : null,
				'active' => 1
				);
                        $item = Item::create($item_data);	
			
			if (!empty($input['feeRebook']) && $input['feeRebook'] > 0)
			{
				$description = $this->IsPublicBooking || $this->IsOnlineBooking ? 
					'Rebook fee for instance id: ' . $input['courseInstance'] :
					'Rebook fee for instance id: ' . $input['groupId'];

				$item_data = array(
					'id'=> null,
					'order_id' => $this->order->id,
					'course_instance_id' => $this->IsPublicBooking || $this->IsOnlineBooking ? $input['courseInstance'] : null,
					'group_booking_id' => $this->IsGroupBooking ? $input['groupId'] : null,
					'product_id' => $this->IsProductPurchase ? $input['id'] : null,
					'vouchers_ids' => $this->IsPublicBooking && !empty($input['vouchersIds']) ? $voucherValue : null,
					'item_type_id' => Utils::ItemTypeId('RebookFee'),
					'description' => $description,
					'qty' => 1,
					'price' => $input['feeRebook'],
					'gst' => $this->IsProductPurchase ? round($input['feeRebook'] / 11, 2) : 0,
					'total' => $input['feeRebook'],
					'user_id' => $user ? $user->id : null,
					'active' => 1
					);

				Item::create($item_data);	
			}
			
			if (!empty($input['discount']) && $input['discount'] > 0)
			{
				$description = $this->IsPublicBooking || $this->IsOnlineBooking ? 
					'Discount fee for instance id: ' . $input['courseInstance'] :
					'Discount fee for instance id: ' . $input['groupId'];
				
				$item_data = array(
					'id'=> null,
					'order_id' => $this->order->id,
					'course_instance_id' => $this->IsPublicBooking || $this->IsOnlineBooking ? $input['courseInstance'] : null,
					'group_booking_id' => $this->IsGroupBooking ? $input['groupId'] : null,
					'product_id' => $this->IsProductPurchase ? $input['id'] : null,
					'vouchers_ids' => $this->IsPublicBooking && !empty($input['vouchersIds']) ? $voucherValue : null,
					'item_type_id' => Utils::ItemTypeId('Discount'),
					'description' => $description,
					'qty' => 1,
					'price' => $input['discount'],
					'gst' => 0, // $this->IsProductPurchase ? round($input['discount'] / 11, 2) : 0,
					'total' => $input['discount'],
					'user_id' => $user ? $user->id : null,
					'active' => 1
					);

				Item::create($item_data);	
			}
			
			if($this->IsPublicBooking && $input['isVoucher'])
			{
                                if(!is_array($input['vouchersIds'])){
                                    $input['vouchersIds']=array((int)$input['vouchersIds']);
                                }
				foreach($input['vouchersIds'] as $id)
				{
					$voucher = Voucher::find($id);	
					$voucher->update(array('order_id' => $this->order->id));
				}
			}						
			
			if ($this->IsGroupBooking)
			{
				$group = GroupBooking::find($input['groupId']);	
				$group->update(array('order_id' => $this->order->id));
			}
			
			if ($this->IsProductPurchase)
			{
				$purchase = Purchase::find($this->booking['purchaseId']);	
				$purchase->update(array('order_id' => $this->order->id, 'date_hire'=> $date_hire));		
			}
			
			if($item_data)
				$input['item_id'] = $item->id;

            Log::debug("items created [".json_encode($item_data)."]");
            Log::debug("input received [".json_encode($item_data)."]");

        }
		Log::debug("finish createItems [" . $this->order->id ."]");
		
	}

	public function updateRoster() 
	{
				
		$existing_rosters = array();
		if ($this->order->rosters->count())
		{
			$existing_rosters = $this->order->rosters->toArray();
			foreach($this->order->rosters as $roster)
			{
				$roster->delete();
			}
		}
		
		$mail_out = isset($this->payment['mail_out']) ? filter_var($this->payment['mail_out'], FILTER_VALIDATE_BOOLEAN) : true;

		foreach($this->instances as $instance) 
		{			
			if(array_key_exists('isVoucher', $instance) && $instance['isVoucher'] == false)
			{
				// not voucher create roster			
				foreach($instance['Students'] as $input) 
				{
					set_time_limit(240);

					// we add the order number to teh last name if it is the default
					if ($input['FirstName'] == 'Student' && 
						strpos($input['LastName'], 'Last') !== false && 
						strpos($input['FirstName'], $this->order->id) === false)
					{
						$input['FirstName'] .= $this->order->id;
					}

					$customer = Customer::where('first_name', $input['FirstName'])
						->where('last_name', $input['LastName'])
						->where('email', $input['Email'])
						->first(array('id'));	
                                        
					/* update student with data sent from Central West Courses website */
					if (isset($input['cwc_student']) && $input['cwc_student'] && $customer)
					{
						Log::debug("Updating student with data from Central West Courses web application");
						unset($input['cwc_student']);
						unset($input['FirstName']);
						unset($input['LastName']);
						unset($input['Dob']);
						unset($input['Mobile']);
						unset($input['Email']);
						unset($input['LangEng']);
						unset($input['LangLevel']);
						foreach($input as $key => $value)
						{
							$customer->$key = strlen($value) == 0 ? null : $value;
						}
						$customer->update();
					}
                                        /* update student with data sent from Central West Courses website */
					if (!$customer)
					{
						$cust_data = array(
							'first_name' => $input['FirstName'],
							'last_name' => $input['LastName'],
							'dob' => $this->IsPublicBooking && !empty($input['Dob']) ? $input['Dob'] : null,
							'mobile' => $input['Mobile'],
							'email' => $input['Email'],
							'country_of_birth' => 'AU',
							'islander_origin' => '0',
							'mail_out_email' => $mail_out ? 1 : 0,
							'mail_out_sms' => $mail_out ? 1 : 0,
							'question1' => $this->IsPublicBooking ? $this->booking['q1'] : null,
							'question2' => $this->IsPublicBooking ? $this->booking['q2'] : null,
							'question3' => $this->IsPublicBooking ? $this->booking['q3'] : null,
							'lang_eng' => !empty($input['LangEng']) ? $input['LangEng'] : null,
							'lang_eng_level' => !empty($input['LangLevel']) ? $input['LangLevel'] : null,
							'usi_verified' => (isset($input['UsiVerified']) && filter_var($input['UsiVerified'], FILTER_VALIDATE_BOOLEAN) ) ? 1 : 0,
							'active' => '1'
							);
						$customer = Customer::create($cust_data);
					}
					else
					{
						if (!empty($input['Dob']))
							$customer->dob = $input['Dob'];
						if (!empty($input['Mobile']))
							$customer->mobile = $input['Mobile'];
						if (!empty($input['Email']))
							$customer->email = $input['Email'];
						
						if (!empty($input['q1']))
							$customer->question1 = $input['q1'];
						if (!empty($input['q2']))
							$customer->question2 = $input['q2'];
						if (!empty($input['q3']))
							$customer->question3 = $input['q3'];
						if (!empty($input['LangEng']))
							$customer->lang_eng = $input['LangEng'];
						if (!empty($input['LangLevel']))
							$customer->lang_eng_level = $input['LangLevel'];
						
						$customer->mail_out_email = $mail_out ? 1 : 0;
						$customer->mail_out_sms = $mail_out ? 1 : 0;
						if (isset($input['UsiVerified']) && filter_var($input['UsiVerified'], FILTER_VALIDATE_BOOLEAN) )
							$customer->usi_verified = 1;
						
						$customer->active = 1;
						
						$customer->update();
					}
					
					$input['certificate_id'] = null;
					$input['notes_admin'] = '';
					$input['notes_class'] = '';
					foreach ($existing_rosters as $e_roster)
					{
						
						if ($e_roster['customer_id'] == $customer->id && (
							(isset($e_roster['course_instance_id']) && $e_roster['course_instance_id'] == $input['courseInstance']) || 
							(isset($e_roster['group_booking_id']) && $e_roster['group_booking_id'] == $instance['groupId'])	))
						{
							$input['certificate_id'] = $e_roster['certificate_id'];
							$input['notes_admin'] = $e_roster['notes_admin'];
							$input['notes_class'] = $e_roster['notes_class'];
							break;
						}
						
						//if (!empty($e_roster['certificate_id']) && empty($input['certificate_id']))
						//{
						//	throw new Exception('You cannot change a roster with a certificate [certificate id: ' . $e_roster['certificate_id'] . ']');
						//}
						

					};
					
					$roster_data = array(
						'id'=> null,
						'order_id' => $this->order->id,
						'item_id' => !empty($instance['item_id']) ? $instance['item_id'] : null,
						'course_instance_id' => $this->IsPublicBooking ? $input['courseInstance'] : null,
						'group_booking_id' => $this->IsGroupBooking ? $instance['groupId'] : null,
						'customer_id' => $customer->id,
						'certificate_id' => $input['certificate_id'],
						//'description' => $input['description'],
						'notes_admin' => $this->IsPublicBooking ? $instance['notesAdmin'] . $input['notes_admin'] : null,
						'notes_class' => $this->IsPublicBooking ? $instance['notesClass'] . $input['notes_class']: null
						);
					
					//Log::debug("creating a roster [" . $this->order->id ."], group: [" . $instance['groupId'] . "]");

					$roster = Roster::create($roster_data);
					
					if(!empty($roster_data['certificate_id']))
					{
						$certificate = Certificate::find($roster_data['certificate_id']);
						$certificate->update(array('roster_id' => $roster->id));			
					}
									
				}
			}

		}
		
		//reload the order
		$this->order = Order::find($this->order->id);
		
		Log::debug("finish updateRosters [" . $this->order->id ."]");
		
	}

	public function processVoucher()
	{
		if (!empty($this->payment['VoucherId']))
		{
            //get voucher real price
            $this->payment['VoucherValue'] = $this->_getVoucherPrice();

			$payment_service = PaymentFactory::create('Voucher');
            Log::debug("voucher processing called".json_encode($this->payment));
			$payment_service->process($this->order, $this->payment);
		}
		
		Log::debug("finish processVoucher [" . $this->order->id ."]");
	}

	//Public booking functions

	public function processPayment()
	{
		$payment_service = PaymentFactory::create($this->payment['PaymentMethod']);
		$payment_service->process($this->order, $this->payment);
		Log::debug("finish processPayment [" . $this->order->id ."]");
	}
	
	public function sendMessages()
	{	
		if(	$this->order && ($this->order->rosters->count() || $this->order->vouchers->count() ) )
		{

			if (isset($this->payment['PaymentStatus']) && filter_var($this->payment['PaymentStatus'], FILTER_VALIDATE_BOOLEAN))
			{
				$this->payment['SendEmail'] = $this->payment['Backend'] ? $this->payment['SendEmail'] : 1; // frontend always send email
				$this->payment['SendSMS'] = $this->payment['Backend'] ? $this->payment['SendSMS'] : 1; // frontend always send sms
			}
			else
			{
				$this->payment['SendEmail'] = 0; // notsuccessfull never send email
				$this->payment['SendSMS'] = 0; // notsuccessfull never send sms
			}

			$send_data = array(
				'order_id' => $this->order->id, 
				'SendAdmin'=> true, 
				'SendEmail'=>filter_var($this->payment['SendEmail'], FILTER_VALIDATE_BOOLEAN), 
				'SendSMS'=>filter_var($this->payment['SendSMS'], FILTER_VALIDATE_BOOLEAN),
				'IsGroupBooking'=> $this->IsGroupBooking,
				'IsPublicBooking'  => $this->IsPublicBooking,
				'IsAgentBooking'=> empty($this->order->agent_id) && empty($this->order->company_id) ? false : true,
				'HasVouchers' => $this->order->vouchers->count() ? true : false,
				'success'=> (isset($this->payment['PaymentStatus']) && filter_var($this->payment['PaymentStatus'], FILTER_VALIDATE_BOOLEAN))
			);
            if(App::environment('production'))
            {
                Queue::push('SendMessage', $send_data);
            }
			Log::debug("messages queued [" . $this->order->id ."] " . json_encode($send_data));
			
			// for testing porposes, uncomment line
			//$a = 10 / 0;
		}
		
	}
	
	public function getBookingId()
	{
		return $this->booking['id'];
	}
	
	public function useOnlinePrice()
	{
		
		if ($this->payment['PaymentMethod'] == 'LATER' || $this->payment['PaymentMethod'] == 'CASH' || $this->payment['PaymentMethod'] == 'CASH2' || $this->payment['PaymentMethod'] == 'AGENT')
		{
			return false;
		}
		if ($this->payment['PaymentMethod'] == 'CC' || $this->payment['PaymentMethod'] == 'PAYPALTERM' || $this->payment['PaymentMethod'] == 'CHEQUE' || $this->payment['PaymentMethod'] == 'EFTPOS' || $this->payment['PaymentMethod'] == 'DEPOSIT' || $this->payment['PaymentMethod'] == 'VOUCHER')
		{
			return true;
		}
		return false;
	}
	
	public function updateOrderComments($comments)
	{
		
		if ($this->order)
			$this->order->update(array('comments' => $comments));		
	}

	private function GetItemDescription($input)
	{
		$description = "";
		if ( $this->IsProductPurchase ) 
		{
			$description = $input['product_name'] . ' ' . $input['product_description'];
		}
		elseif ( $this->IsGroupBooking ) 
		{
			$loc = Location::where('id', $input['location'])->remember(720)->first();
			$description = $input['courseName'] . ", " . date('d M Y (D)', strtotime($input['courseDate'])) . " " . $input['courseTime'] . ", " . $loc->address . ', ' . $loc->city . ', ' . $loc->state;
		}
		elseif ( $this->IsOnlineBooking ) 	
		{		
			$description = $input['courseName'] ;
		}
		else	
		{	
			$description = $input['courseName'] . ", " . date('d M Y (D)', strtotime($input['courseDate'])) . " " . $input['courseTime'] . ", " . str_replace("<br>","",$input['courseAddress']);
		}
		
		return $description;
	}

}