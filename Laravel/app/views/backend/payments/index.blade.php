@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Payments Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Payments Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.payments.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.payments.index', 'class'=>'form-inline')) }}
				{{ Form::text('order_id', Input::old('search'), array('class'=>'input-medium', 'placeholder'=>'Search Order Id')) }}				
				{{ Form::text('from', Input::old('from'), array('id'=>'date_from','class'=>'input-small')) }}
				{{ Form::text('to', Input::old('to'), array('id'=>'date_to','class'=>'input-small')) }}
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if (count($payments) > 0)
{{ $payments->links() }}
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th class="span1">Order_id</th>
				<th class="span1">Payment Date</th>
				<th class="span1">Payment Method</th>
				<th class="span3">Comments</th>
				<th class="span1">Status</th>
				<th class="span1">End</th>
				<th class="span1">Total</th>
				<th class="span1">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($payments as $payment)
				<tr>
					<td>{{{ $payment->order_id }}}</td>
					<td>{{{ $payment->payment_date }}}</td>
					<td>{{{ $payment->method ? $payment->method->name : $payment->payment_method_id}}}</td>
					<td>{{{ $payment->comments }}}</td>
					<td>{{{ $payment->status->name }}}</td>
					<td>{{{ $payment->backend == '1' ? 'Backend' : 'Frontend' }}}</td>
					<td><span class="pull-right">${{{ $payment->total }}}</span></td>
                    <td>{{ link_to_route('backend.payments.edit', 'Edit', array($payment->id), array('class' => 'btn btn-mini btn-info')) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
{{ $payments->links() }}
@else
	There are no payments
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/payments.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
