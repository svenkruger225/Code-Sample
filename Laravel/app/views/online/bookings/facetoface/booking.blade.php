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
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Select {{$locations[0]->course->name}} Class date </h3></div>
					<div class="panel-body">
						@include('online/bookings/facetoface/courses-details')
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Confirm Course(s)</h3></div>
					<div class="panel-body">
						@include('online/bookings/public/comfirmation')
					</div>
				</div>
			</div>
		
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Enter Your Details </h3></div>
					<div class="panel-body">
						@include('online/bookings/public/details')
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Select Payment </h3></div>
						<div class="panel-body">
							@include('online/bookings/public/payment-cc-only')
							<hr class="alert-info" />
							<div class="container-fluid form-horizontal">
								@include('online/bookings/public/payment-forms')
							</div>
							@include('online/bookings/public/payment-modals')
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
