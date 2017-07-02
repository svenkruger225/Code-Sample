@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
New Group Booking ::
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
					@include('bookings/group/locations')
                    
					<h4>Select Course Details</h4>
					@include('bookings/group/courses')

				</div>
				<div class="span4">			
					<h4>Group Details</h4>
					@include('bookings/group/group-details')
					<h4>Students Details</h4>
					@include('bookings/group/details')
				</div>
				<div class="span4">			
					<h4>Confirm Details</h4>
					@include('bookings/group/comfirmation')
					
					<h4>Invoice Details</h4>
					@include('bookings/common/invoice')
                    
					<h4>Payment</h4>
					@include('bookings/group/payment')
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
	@include('bookings/common/whatis-usi')
	<script>
	var actn = 'group';
	var back = '1';
	var order_id = '{{$order_id}}';
	</script>
	
    <script src="/_scripts/src/app/require.config.js"></script>
    <script data-main="/_scripts/src/app/bootstrapers/booking.group.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop
	