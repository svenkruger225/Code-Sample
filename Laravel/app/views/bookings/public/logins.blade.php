	<div class="pull-left">
		<button data-bind="'click': displayGiftVoucherForm" ><i class="icon-large icon-white icon-gift"></i> Use a Gift Voucher.</button><br><br>
	</div>
	<div class="controls offset2">
		<span class="location-selector">Select location <i class="icon-chevron-right"></i> </span> <select class="location-selector" onchange="window.location=this.value">
			@foreach ( $locations as $location )
			<option value="/bookings/{{$location->name}}" {{strtolower($page->location_name) == strtolower($location->name) ? 'selected' : '' }}>{{strtoupper($location->name)}}</option>
			<optgroup style="margin: 1px 0;"></optgroup>
			@endforeach
		</select>
	</div>
	<div id="cbLogins">   
		<div id="gvcb" class="modal hide bg-payment">
			<div class="modal-header">
				<a class="close" data-dismiss="modal">x</a>
				<h2>Use a Gift Voucher</h2>
			</div>
			<div class="modal-body form-horizontal table-condensed bg-payment">
				<div class="control-group">
					<label class="control-label" for="">Gift Voucher ID:</label>
					<div class="controls"><input type="text" name="voucher_id" id="voucher_id" value="" data-bind="value: voucherId"/></div>
				</div>                
			</div>
			<div class="modal-footer bg-payment">
				<div class="control-group">
					<button class="btn btn-success" data-bind="click: retrieveGiftVoucher"><i class="icon-white icon-ok"></i>&nbsp;Submit</button>
				</div>
			</div> 
		</div>
	</div>      
	
	<div style="display:none;" data-bind="'visible': booking().DisplayVoucher() || booking().DisplayAgent() || booking().PurchaseId.Visible()">
	<!-- ko if: booking().DisplayVoucher() -->
	<br><h4 class="info">
		Gift Voucher: <span data-bind="html: booking().Payment().Voucher().id"></span> - <span data-bind="html: booking().Payment().Voucher().message"></span>
	</h4>
    <!-- /ko -->
	<!-- ko if: booking().PurchaseId.Visible() -->
	<h2 style="margin:20px 0 0 10px;border-bottom:1px solid #E0DCD8;padding:0 0 20px;">
		Order ID: <span data-bind="'html': booking().BookingUniqueId"></span>
	</h2>
    <!-- /ko -->
	</div>
