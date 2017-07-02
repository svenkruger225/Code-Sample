@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Booking Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
    <div id="mainContent">
		@include('bookings/backend/booking')
		@include('bookings/common/creditcard')
    </div>
	<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/booking.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
	
@stop