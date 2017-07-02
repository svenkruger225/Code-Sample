@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
New Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')
	<div id="content">
              @if ($group !='agent')
		@include('bookings/backend/logins')
              @endif  
		<div id="cb">
			<form name="cbForm" class="locationRadio" id="cbForm" data-bind="attr: {action: otherPaymentUrl()}" method="post" >
			<div class="row-fluid">
				<div class="span4">			
					<h4>Select city</h4>
					@include('bookings/common/locations')
                    
					<h4>Select Course Details</h4> 
                                        @if ($group !='agent')
                                        <div>
                                            <input type="checkbox" data-bind="checked: booking().overrideValidation" value="1">Override Validation
                                        </div>
                                            <div id="instancesByLocation" class="localActivityIndicator">
                                            @include('bookings/backend/courses-details-admin')
                                            </div>
                                        @else
                                            <div id="instancesByLocation" class="localActivityIndicator">
                                            @include('bookings/backend/courses-details-agent')
                                            </div>
                                        @endif
					
				</div>
				<div class="span4">	
                                        @if ($group !='agent')
                                        <h4>Details</h4>
                                        @else
                                        <h4>Agent Details</h4>
                                        @endif
					@include('bookings/backend/details')
				</div>
				<div class="span4">			
					<h4>Confirm Details</h4>
					@include('bookings/common/comfirmation')
					
					<h4>Invoice Details</h4>
					@include('bookings/common/invoice')
                    
					<h4>Payment</h4>
					@include('bookings/common/payment')
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
	@include('bookings/common/usi-create-form')
	@include('bookings/common/whatis-usi')
	</div>
	<!-- must keep this template outside the content div -->
	@include('bookings/common/creditcard')
	<script>
	var actn = 'booking';
	var back = '1';
	var order_id = '{{$order_id}}';
	var order_type = '{{$order_type}}';
	</script>
	
    <script src="/_scripts/src/app/require.config.js"></script>
    <script data-main="/_scripts/src/app/bootstrapers/bookings.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop
	