@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
New Purchase ::
@parent
@stop

{{-- Page content --}}
@section('content')
	<div id="content">
		<div id="cb">
			<form name="cbForm" class="locationRadio" id="cbForm" method="post" >
			<div class="row-fluid">
				<div class="span4">			
					<h4>Select city</h4>
					@include('bookings/purchase/locations')
                    
					<h4>Select Products</h4>
					@include('bookings/purchase/products')

					<h4>Order Details</h4>
					@include('bookings/purchase/purchase-details')

				</div>
				<div class="span4">			
					<h4>Confirm Details</h4>
					@include('bookings/purchase/comfirmation')
					
					<h4>Invoice Details</h4>
					@include('bookings/purchase/invoice')
				</div>
				<div class="span4">			
                    
					<h4>Payment</h4>
					@include('bookings/purchase/payment')
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
	@include('bookings/common/creditcard') 
	<script>
	var actn = 'purchase';
	var back = '1';
	var order_id = '{{$order_id}}';
	</script>
	
    <script src="/_scripts/src/app/require.config.js"></script>
    <script data-main="/_scripts/src/app/bootstrapers/purchases.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop
	