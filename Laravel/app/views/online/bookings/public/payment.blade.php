<div class="container-fluid form-horizontal">
<div class="online-panel-body">
	<div class="form-group">
	<label class="col-sm-1">
		<input type="radio" class='check-control cbPaymentCredit' id="PaymentMethodPayWay" name="PaymentMethod" value="CC" data-bind="checked: booking().PaymentMethod, click: displayPayWayFormPublic"/> 
	</label>
	<label class="col-sm-11" style="margin-top:10px">
		Credit Card [Visa / Mastercard] = <span id="online_total" data-bind="html: booking().OnLineTotal.Price"></span>
	</label>
	</div>					
	<div class="form-group">
	<label class="col-sm-1">
		<input type="radio" class='check-control cbPaymentCredit' id="PaymentMethodPayPal" name="PaymentMethod" value="CC" data-bind="checked: booking().PaymentMethod, click: displayPayPalFormPublic"/> 
	</label>
	<label class="col-sm-11" style="margin-top:10px">
		Paypal [includes: Visa / Mastercard / Amex] = <span id="online_total" data-bind="html: booking().OnLineTotal.Price"></span>
	</label>
	</div>					
	<div class="form-group" data-bind="visible: booking().ShowCashOnDayOption()">
	<label class="col-sm-1">
		<input type="radio" class='check-control cbPaymentOther' id="PaymentMethodLater" name="PaymentMethod" value="LATER" data-bind="checked: booking().PaymentMethod, click: displayOtherPaymentPublic"> 
	</label>
	<label class="col-sm-11" style="margin-top:10px">
		Cash on Day = <span id="offline_total" data-bind="html: booking().OffLineTotal.Price"></span>
	</label> 
	</div>					
	<div class="form-group" data-bind="visible: booking().ShowPayWithVoucherOption()">
	<label class="col-sm-1">
		<input type="radio" class='check-control cbPaymentOther' id="PaymentMethodVoucher" name="PaymentMethod" value="LATER" data-bind="checked: booking().PaymentMethod, click: displayOtherPaymentPublic"/> 
	</label>
	<label class="col-sm-11" style="margin-top:10px">
		Paying with Gift Voucher = <span  data-bind="html: booking().OffLineTotal.Price"></span>
	</label> 
	</div>					
</div>
</div>

