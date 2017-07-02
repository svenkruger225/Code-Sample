@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Search Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
	<div class="page-header">
		@include('bookings/search/search')
	</div>
	<div id="cb">
	
	@if (count($orders) > 0)
	{{ $orders->links() }}
    <table class="table table-bordered table-condensed table-hover">
		<tbody>
			<tr>
				<th class="span1"></th>
				<th class="span1">Customer ID</th>
				<th class="span1">Order ID</th>
				<th class="span1">Items</th>
				<th class="span1">Agent/Company</th>
				<th class="span2">Name</th>
				<th class="span2">Email Address</th>
				<th class="span1">Phone</th>
				<th class="span1">Mobile</th>
				<th class="span1">Paid</th>
				<th class="span1">Owing</th>
				<th class="span1">Status</th>
				<th class="span1">Modified</th>
			</tr>
	
			@include('bookings/search/result')
		</tbody>
	</table>
	{{ $orders->links() }}
	@else
		<h5>No Results</h5>
	@endif

	</div>
	
	@include('bookings/search/payments')
	@include('backend/calendar/transaction')

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/booking.search.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop
	