@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Payment Methods Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>Payment Methods Management
		<div class="pull-right">
			<a href="{{ route('backend.payment_methods.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>

@if ($payment_methods->count())
	<table class="table table-striped table-bordered table-condensed table-hover span8">
		<thead>
			<tr>
				<th>Id</th>
				<th>Code</th>
				<th>Name</th>
				<th>Fee</th>
				<th>Type</th>
				<th>Show</th>
				<th>Order</th>
				<th>Active</th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($payment_methods as $payment_method)
				<tr>
					<td>{{{ $payment_method->id }}}</td>
					<td>{{{ $payment_method->code }}}</td>
					<td>{{{ $payment_method->name }}}</td>
					<td>{{{ $payment_method->fee }}}</td>
					<td>{{{ $payment_method->pay_type }}}</td>
					<td>{{{ $payment_method->show_online }}}</td>
					<td>{{{ $payment_method->order }}}</td>
					<td>{{{ $payment_method->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.payment_methods.destroy', $payment_method->id))) }}
                    <td>
						{{ link_to_route('backend.payment_methods.edit', 'Edit', array($payment_method->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no payment_methods
@endif
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/methods.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
