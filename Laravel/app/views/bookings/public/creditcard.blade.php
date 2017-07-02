
<div id="cbPaymentEmbeded" >
	
	<!-- OTHER PAYMENTS -->
    <div class="modal-creditcard hide" id="other-payment" data-bind="with: booking().Payment">
        <form id="otherPaymentForm" name="otherPaymentForm" method="post" data-bind="attr: {'action': OtherPaymentUrl }">
		<div class="row-fluid alert-info"><div class="span12"><h4 class="pull-left">Invoice Details</h4> <h4 class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></h4></div></div>
		<div class="row-fluid alert-info"><div class="span12"><p>&nbsp;</p></div></div>
		<div class="row-fluid"><div class="span2">First Name:</div><div class="span10"><input type="text" id="first_name" name="first_name" value="" data-bind="value: FirstName"/></div></div>
		<div class="row-fluid"><div class="span2">Last Name :</div><div class="span10"><input type="text" name="last_name" id="last_name" value="" data-bind="value: LastName"/></div></div>
		<div class="row-fluid"><div class="span2">Dob :</div><div class="span10"><input type="text" id="dob" name="dob" value="" data-bind="value: Dob"/></div></div>
		<div class="row-fluid"><div class="span2">Mobile :</div><div class="span10"><input type="text" id="mobile" name="mobile" value="" data-bind="value: Mobile"/></div></div>
		<div class="row-fluid"><div class="span2">Email :</div><div class="span10"><input type="text" name="email" id="email" value="" data-bind="value: Email"/></div></div>
		<div class="row-fluid"><div class="span12"><p><input type="checkbox" name="handbook" id="handbook" data-bind="checked: handbook"> I have read and agree to the <a href="/participant_handbook" target="_blank">Participant Handbook</a><span  class="validationMessage" data-bind='validationMessage: handbook'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p><input type="checkbox" name="TAC" id="TAC" data-bind="checked: TAC"> I have read and agree to the <a href="/privacy_terms_conditions" target="_blank">Privacy Policy. Terms &amp; Conditions</a><span class="validationMessage" data-bind='validationMessage: TAC'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p data-bind="visible: BookingHasCoffeeCourse"><input type="checkbox" name="CoffeeAck" id="CoffeeAck" data-bind="checked: CoffeeAck"> I have understood that to satisfy the Professional Barista Course requirements successful<br>completion of SITXFSA101 Use hygienic practices for food safety is required prior.</a><span class="validationMessage" data-bind='validationMessage: CoffeeAck'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p><strong>Please do not make your booking twice. If there is a query, please contact our office (02) 9211 9779</strong></p></div></div>
        <div class="row-fluid"><button class="btn btn-success pull-right" data-bind="command: $root.submitOtherPayment, activity: $root.submitOtherPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> MAKE BOOKING</button></div>
	    </form>
    </div>
<!-- END OTHER PAYMENTS -->

<!-- PAYPAL DETAILS MODAL -->
    <div class="modal-creditcard hide" id="paypal-payment" data-bind="with: booking().Payment">
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
		<div class="row-fluid alert-info"><div class="span12"><h4 class="pull-left">Invoice Details</h4> <h4 class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></h4></div></div>
		<div class="row-fluid alert-info"><div class="span12"><p>&nbsp;</p></div></div>
		<div class="row-fluid"><div class="span2">First Name:</div><div class="span10"><input type="text" id="first_name" name="first_name" value="" data-bind="value: FirstName"/></div></div>
		<div class="row-fluid"><div class="span2">Last Name :</div><div class="span10"><input type="text" name="last_name" id="last_name" value="" data-bind="value: LastName"/></div></div>
		<div class="row-fluid"><div class="span2">Dob :</div><div class="span10"><input type="text" id="dob" name="dob" value="" data-bind="value: Dob"/></div></div>
		<div class="row-fluid"><div class="span2">Mobile :</div><div class="span10"><input type="text" id="mobile" name="mobile" value="" data-bind="value: Mobile"/></div></div>
		<div class="row-fluid"><div class="span2">Email :</div><div class="span10"><input type="text" name="email" id="email" value="" data-bind="value: Email"/></div></div>
		<div class="row-fluid"><div class="span12"><p><input type="checkbox" name="handbook" id="handbook" data-bind="checked: handbook"> I have read and agree to the <a href="/participant_handbook" target="_blank">Participant Handbook</a><span  class="validationMessage" data-bind='validationMessage: handbook'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p><input type="checkbox" name="TAC" id="TAC" data-bind="checked: TAC"> I have read and agree to the <a href="/privacy_terms_conditions" target="_blank">Privacy Policy. Terms &amp; Conditions</a><span class="validationMessage" data-bind='validationMessage: TAC'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p data-bind="visible: BookingHasCoffeeCourse"><input type="checkbox" name="CoffeeAck" id="CoffeeAck" data-bind="checked: CoffeeAck"> I have understood that to satisfy the Professional Barista Course requirements successful<br>completion of SITXFSA101 Use hygienic practices for food safety is required prior.</a><span class="validationMessage" data-bind='validationMessage: CoffeeAck'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p><strong>Please do not make your booking twice. If there is a query, please contact our office (02) 9211 9779</strong></p></div></div>
		<div class="row-fluid"><button class="btn btn-success" data-bind="command: $root.submitPayPalPayment, activity: $root.submitPayPalPayment.isExecuting"><i class="icon-white icon-thumbs-up"></i> MAKE BOOKING</button></div>
	    </form>
    </div>
<!-- PAYPAL DETAILS MODAL -->								

<!-- CREDIT CARD DETAILS MODAL -->

    <div class="modal-creditcard hide" id="creditcard-payment" data-bind="with: booking().Payment">
        <form id="paymentForm" name="paymentForm" method="post">
        <input type="hidden" id="EPS_MERCHANT" name="EPS_MERCHANT"/>
        <input type="hidden" id="EPS_TXNTYPE" name="EPS_TXNTYPE"/>
        <input type="hidden" id="EPS_REFERENCEID" name="EPS_REFERENCEID"/>
        <input type="hidden" id="EPS_AMOUNT" name="EPS_AMOUNT" data-bind="value: Amount"/>
        <input type="hidden" id="EPS_TIMESTAMP" name="EPS_TIMESTAMP"/>
        <input type="hidden" id="EPS_FINGERPRINT" name="EPS_FINGERPRINT"/>
        <input type="hidden" id="EPS_RESULTURL" name="EPS_RESULTURL"/>
        <input type="hidden" id="EPS_REDIRECT" name="EPS_REDIRECT"/>
        <input type="hidden" id="EPS_IP" name="EPS_IP"/>
		<div class="row-fluid alert-info"><div class="span12"><h4 class="pull-left">Invoice Details</h4> <h4 class="pull-right" data-bind="html: paymentTitle() + ' (' + Amount.Price() + ')'"></h4></div></div>
		<div class="row-fluid alert-info"><div class="span12"><p>&nbsp;</p></div></div>
		<div class="row-fluid"><div class="span2">First Name:</div><div class="span10"><input type="text" id="first_name" name="first_name" value="" data-bind="value: FirstName"/></div></div>
		<div class="row-fluid"><div class="span2">Last Name :</div><div class="span10"><input type="text" name="last_name" id="last_name" value="" data-bind="value: LastName"/></div></div>
		<div class="row-fluid"><div class="span2">Dob :</div><div class="span10"><input type="text" id="dob" name="dob" value="" data-bind="value: Dob"/></div></div>
		<div class="row-fluid"><div class="span2">Mobile :</div><div class="span10"><input type="text" id="mobile" name="mobile" value="" data-bind="value: Mobile"/></div></div>
		<div class="row-fluid"><div class="span2">Email :</div><div class="span10"><input type="text" name="email" id="email" value="" data-bind="value: Email"/></div></div>
		<div class="row-fluid"><div class="span12"><p><input type="checkbox" name="handbook" id="handbook" data-bind="checked: handbook"> I have read and agree to the <a href="/participant_handbook" target="_blank">Participant Handbook</a><span  class="validationMessage" data-bind='validationMessage: handbook'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p><input type="checkbox" name="TAC" id="TAC" data-bind="checked: TAC"> I have read and agree to the <a href="/privacy_terms_conditions" target="_blank">Privacy Policy. Terms &amp; Conditions</a><span class="validationMessage" data-bind='validationMessage: TAC'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p data-bind="visible: BookingHasCoffeeCourse"><input type="checkbox" name="CoffeeAck" id="CoffeeAck" data-bind="checked: CoffeeAck"> I have understood that to satisfy the Professional Barista Course requirements successful<br>completion of SITXFSA101 Use hygienic practices for food safety is required prior.</a><span class="validationMessage" data-bind='validationMessage: CoffeeAck'></span></p></div></div>
		<div class="row-fluid"><div class="span12"><p><strong>Please do not make your booking twice. If there is a query, please contact our office (02) 9211 9779</strong></p></div></div>
		<div class="row-fluid"><button class="btn btn-primary" data-bind="command: $root.submitcreditcardCmd, activity: $root.submitcreditcardCmd.isExecuting"><i class="icon-white icon-thumbs-up"></i> MAKE BOOKING</button></div>
	    </form>
    </div>

<!-- CREDIT CARD DETAILS MODAL -->

</div>


<div class="modal hide bg-payment" id="creditcard-submit-message" style="color:#000;">
	<div class="modal-header alert-info" style="color:#000;">
		<h2>CoffeeSchool Securepay Payment</h2>
	</div>
	<div class="modal-body bg-payment">
		<div>
			<div id="submit-message" style="color:#000;">
				<h3>Please Wait - Your payment is being processed.</h3>
				<p>If you do not receive a confirmation within 2 minutes please call your closest office.</p>
				<ul>
					<li>NSW - 02 9211 9779</li>
					<li>VIC - 03 9329 6550</li>
					<li>QLD - 07 3257 2001</li>
					<li>WA - 08 9486 4771</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="modal hide bg-payment" id="paypal-submit-message" style="color:#000;">
	<div class="modal-header alert-info" style="color:#000;">
		<h2>CoffeeSchool PayPal Payment</h2>
	</div>
	<div class="modal-body bg-payment">
		<div>
			<div id="submit-message" style="color:#000;">
				<h3>Please Wait - Your are being redirect to PayPal Secure Payment Form.</h3>
				<p>If you do not receive a confirmation within 2 minutes please call your closest office.</p>
				<ul>
					<li>NSW - 02 9211 9779</li>
					<li>VIC - 03 9329 6550</li>
					<li>QLD - 07 3257 2001</li>
					<li>WA - 08 9486 4771</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="modal hide bg-payment" id="booking-submit-message" style="color:#000;">
	<div class="modal-header alert-info" style="color:#000;">
		<h2>CoffeeSchool Pay later booking</h2>
	</div>
	<div class="modal-body bg-payment">
		<div>
			<div id="submit-message" style="color:#000;">
				<h3>Please Wait – Your booking is being processed.</h3>
				<p>If you do not receive a confirmation within 2 minutes please call your closest office.</p>
				<ul>
					<li>NSW - 02 9211 9779</li>
					<li>VIC - 03 9329 6550</li>
					<li>QLD - 07 3257 2001</li>
					<li>WA - 08 9486 4771</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="modal hide bg-payment" id="booking-thankyou-message" style="color:#000;">
	<div class="modal-header alert-info">
		<a class="close" data-dismiss="modal">x</a>
		<h2>CoffeeSchool Thank You</h2>
	</div>
	<div class="modal-body bg-payment">
		<div>
			<div id="submit-message" style="color:#000;">
				<h3>The booking has been processed successfully.</h3>
			</div>
		</div>
	</div>
</div>

<div class="modal hide bg-payment" id="booking-preprocessing-message" style="color:#000;">
	<div class="modal-header alert-info" style="color:#000;">
		<h2>CoffeeSchool pre processing booking</h2>
	</div>
	<div class="modal-body bg-payment">
		<div>
			<div id="preprocessing-message" style="color:#000;">
				<h3>Please Wait - Your booking is being pre-processed.</h3>
				<p>If you do not see the payment form within 1/2 minutes please call your closest office.</p>
				<ul>
					<li>NSW - 02 9211 9779</li>
					<li>VIC - 03 9329 6550</li>
					<li>QLD - 07 3257 2001</li>
					<li>WA - 08 9486 4771</li>
				</ul>
			</div>
		</div>
	</div>
</div>