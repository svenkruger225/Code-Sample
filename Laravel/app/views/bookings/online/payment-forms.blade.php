<!-- OTHER PAYMENTS -->
<div class="online-panel-body hide" id="other-payment-embeded" data-bind="with: booking().Payment">
	<form id="otherPaymentForm" name="otherPaymentForm" method="post" data-bind="attr: {'action': OtherPaymentUrl }">
	<div class="form-group"><label class="col-sm-12"><h4>Invoice Details <small class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></small></h4></label></div>
	@include('bookings/online/payment-contact')
	@include('bookings/online/payment-acks')
	<div class="form-group"><button class="btn btn-success pull-right" data-bind="command: $root.submitOtherPayment, activity: $root.submitOtherPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> MAKE BOOKING</button></div>
	</form>
</div>
<!-- END OTHER PAYMENTS -->

<!-- PAYPAL DETAILS MODAL -->
<div class="online-panel-body hide" id="paypal-payment-embeded" data-bind="with: booking().Payment">
	<form id="paypalForm" name="otherPaymentForm" method="post" action="/api/booking/submitToPayPal">
	<input type="hidden" name="OrderId" data-bind="value: OrderId"/>
	<input type="hidden" name="Backend" data-bind="value: Backend"/>
	<input type="hidden" name="VoucherId" data-bind="value: Voucher() == undefined ? '' : Voucher().id"/>
	<input type="hidden" name="VoucherValue" data-bind="value: Voucher() == undefined ? '' : Voucher().total"/>
	<input type="hidden" name="Gst" data-bind="value: Gst"/>
	<input type="hidden" name="SendEmail" data-bind="value: SendEmail"/>
	<input type="hidden" name="SendSMS" data-bind="value: SendSMS"/>
	<input type="hidden" name="TotalToPay" data-bind="value: TotalToPay()"/>
	<input type="hidden" name="FrontendUrl" data-bind="value: FrontendUrl()"/>
	<input type="hidden" name="IsPublicBooking" data-bind="value: IsPublicBooking()"/>
	<input type="hidden" name="IsGroupBooking" data-bind="value: IsGroupBooking()"/>
	<input type="hidden" name="IsProductPurchase" data-bind="value: IsProductPurchase()"/>
	<input type="hidden" name="IsMachineHire" data-bind="value: IsMachineHire()"/>
	<div class="form-group"><label class="col-sm-12"><h4>Invoice Details PayPal <small class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></small></h4></label></div>
	@include('bookings/online/payment-contact')
	@include('bookings/online/payment-acks')
	<div class="form-group"><button class="btn btn-success" data-bind="command: $root.submitPayPalPayment, activity: $root.submitPayPalPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> MAKE BOOKING</button></div>
	</form>
</div>
<!-- PAYPAL DETAILS MODAL -->

<!-- PAYWAY DETAILS MODAL -->
<div class="online-panel-body hide" id="payway-payment-embeded" data-bind="with: booking().Payment">
	<form id="payWayForm" name="creditCardorm" method="post" data-bind="attr: {'action': CreditCardPaymentUrl }">
	<div class="form-group"><label class="col-sm-12"><h4>Invoice Details <small class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></small></h4></label></div>
	@include('bookings/online/payment-contact')
	@include('bookings/online/payment-acks')
	<div class="form-group"><button class="btn btn-success" data-bind="command: $root.submitPayWayPayment, activity: $root.submitPayWayPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> MAKE BOOKING</button></div>
	</form>
</div>
					
