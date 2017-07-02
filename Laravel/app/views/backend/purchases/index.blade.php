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
		<div class="span2 pull-right">
			<a href="{{ route('backend.orders.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
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

@if (count($purchases) > 0)

{{ $purchases->appends(array('from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}


	<table class="table table-striped table-bordered table-condensed table-hover">
		<thead>
			<tr>
				<th class="span1">Id</th>
				<th class="span2">Location</th>
				<th class="span2">Customer</th>
				<th class="span1">Order</th>
				<th class="span1">Date Hire</th>
				<th class="span3">Notes</th>
				<th class="span3">Description</th>
				<th class="span1">Total</th>
				<th class="span1">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($purchases as $purchase)
				<tr>
					<td>{{{ $purchase->id }}}</td>
					<td>{{{ $purchase->location ? $purchase->location->name : $purchase->location_id}}}</td>
					<td>{{{ $purchase->customer ? $purchase->customer->fullName : $purchase->customer_id}}}</td>
					<td>{{{ $purchase->order_id }}}</td>
					<td>{{{ $purchase->date_hire ? $purchase->date_hire : ''}}}</td>
					<td>{{{ $purchase->notes}}}</td>
					<td>{{{ $purchase->description }}}</td>
					<td>{{{ $purchase->order->total }}}</td>
                    <td><a href="{{ route('backend.orders.edit', array($purchase->id)) }}" class="btn btn-mini btn-info">Edit</a>
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

{{ $purchases->appends(array('from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}
	
@else
	There are no orders
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/purchases.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
