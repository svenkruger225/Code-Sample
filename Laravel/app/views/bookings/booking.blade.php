@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
New Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')
	<div id="content">
		@include('backend.bookings.logins')
		<div id="cb">
			<form name="cbForm" class="locationRadio" id="cbForm" data-bind="attr: {action: otherPaymentUrl()}" method="post" >
			<div class="row-fluid">
				<div class="span4">			
					<h4>Select city</h4>
					@include('backend/common/locations')
                    
					<h4>Select Course Details</h4>
					@include('backend/bookings/courses-details-admin')

				</div>
				<div class="span4">			
					<h4>Details</h4>
					@include('backend/bookings/details')
				</div>
				<div class="span4">			
					<h4>Confirm Details</h4>
					@include('backend/common/comfirmation')
					
					<h4>Invoice Details</h4>
					@include('backend/common/invoice')
                    
					<h4>Payment</h4>
					@include('backend/bookings/payment')
				</div>
			</div>
			</form>
		</div>

	<!--
	   <div style="border:white dotted thin">
			<h4>data() JSON</h4>
			<pre data-bind="text: ko.utils.debugInfo(booking)"></pre>
		</div>
	-->

	</div>
	@include('backend/common/creditcard')
	<script>
	var actn = 'booking';
	var order_id = '{{$order_id}}';
	</script>
	
	<script src="/_scripts/src/app/require.config.js"></script>
	<script data-main="/_scripts/src/app/bootstrapers/bookings.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop
	