@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Order Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Order Management</h4></div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.orders.index', 'class'=>'form-inline')) }}
				{{ Form::text('from', Input::old('from'), array('id'=>'date_from','class'=>'input-small')) }}
				{{ Form::text('to', Input::old('to'), array('id'=>'date_to','class'=>'input-small')) }}
				{{ Form::button('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if (count($orders) > 0)

{{ $orders->appends(array('from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}


	<table class="table table-striped table-bordered table-condensed table-hover">
		<thead>
			<tr>
				<th class="span1">Id</th>
				<th class="span1">Type</th>
				<th class="span2">Customer</th>
				<th class="span1">Order Date</th>
				<th class="span2">Agent</th>
				<th class="span2">Company</th>
				<th class="span3">Comments</th>
				<th class="span1">Status</th>
				<th class="span1">Total</th>
				<th class="span1">Paid</th>
				<th class="span1">User</th>
				<th class="span1">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($orders as $order)
				<tr>
					<td>{{{ $order->id }}}</td>
					<td>{{{ $order->order_type }}}</td>
					<td>{{{ $order->customer ? $order->customer->fullName : $order->customer_id}}}</td>
					<td>{{{ $order->order_date }}}</td>
					<td>{{{ $order->agent ? $order->agent->name : ''}}}</td>
					<td>{{{ $order->company ? $order->company->name : ''}}}</td>
					<td>{{{ $order->comments }}}</td>
					<td>{{{ $order->status->name }}}</td>
					<td>{{{ $order->total }}}</td>
					<td>{{{ $order->paid }}}</td>
					<td>{{{ $order->user ? $order->user->username : ''}}}</td>
                    <td>
						<a href="{{ route('backend.orders.edit', array($order->id)) }}" class="btn btn-mini btn-info">Edit</a>
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

{{ $orders->appends(array('from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}
	
@else
	There are no orders
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/orders.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
