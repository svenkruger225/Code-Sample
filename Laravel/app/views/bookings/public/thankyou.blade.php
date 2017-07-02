@extends('frontend/layouts/booking')

{{-- Page title --}}
@section('title')
Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')
	
	@include('bookings/common/thankyou')
	
@stop

