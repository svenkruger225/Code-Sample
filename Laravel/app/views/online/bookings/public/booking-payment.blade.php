@extends('online/layouts/default')

{{-- Page title --}}
@section('title')
Online Booking :: 
@stop

{{-- Page content --}}
@section('content')
	<div id="busyindicator"></div>
    <div id="content" class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				@include('online/bookings/public/display-only')
			</div>
		
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Select Payment </h3></div>
						<div class="panel-body">
							@include('online/bookings/public/payment-cc-only')
							<hr class="alert-info" />
							<div class="container-fluid form-horizontal">
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
									@include('online/bookings/public/payment-contact')
									@include('online/bookings/public/payment-acks')
									<div class="form-group"><button class="btn btn-success" data-bind="command: $root.submitPayPalPayment, activity: $root.submitPayPalPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> PAY COURSE</button></div>
									</form>
								</div>
								<!-- PAYPAL DETAILS MODAL -->

								<!-- PAYWAY DETAILS MODAL -->
								<div class="online-panel-body hide" id="payway-payment-embeded" data-bind="with: booking().Payment">
									<form id="payWayForm" name="creditCardorm" method="post" data-bind="attr: {'action': CreditCardPaymentUrl }">
									<div class="form-group"><label class="col-sm-12"><h4>Invoice Details <small class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></small></h4></label></div>
									@include('online/bookings/public/payment-contact')
									@include('online/bookings/public/payment-acks')
									<div class="form-group"><button class="btn btn-success" data-bind="command: $root.submitPayWayPayment, activity: $root.submitPayWayPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> PAY COURSE</button></div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>	

    </div>	
	@include('bookings/common/whatis-usi')
	<script>
	var actn = 'online';
	var back = '0';
	var loc_id = '';
	var order_id = '{{$order_id}}';
	var order_type = '{{$order_type}}';
	var voucher_id = '';
	var ref = '';
	var act_course = '';
	var act_instance = '';
	var act_bundle = '';
	</script>
    <script src="/_scripts/src/app/require.config.js"></script>
    <script data-main="/_scripts/src/app/bootstrapers/booking.online.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
	
@stop
