<?php

interface BookingInterface
{

	public function initiatePayPalPurchase();
	public function submitToPayPal();
	public function cancelPayPalPurchase();
	public function completePayPalPurchase();

	public function payWayPurchase();
	public function submitToPayWay();
	public function cancelPayWayPurchase();
	public function completePayWayPurchase();
	public function queuePayWayServerResponse();
	public function processPayWayResponse($order, $parameters);
	
	
	public function processPurchase();
	public function transactionalPurchase();

	
	// Public Booking functions
	
	public function createOrder();
	public function createItems();
	
	public function updateRoster();

	public function processVoucher();

	//Public booking functions

	public function processPayment();
	
	public function sendMessages();
	
	public function getBookingId();
	
	public function useOnlinePrice();
	
}