@extends('backend/layouts/default')

@section('content')

<h1>Show Payment_method</h1>

<p>{{ link_to_route('backend.payment_methods.index', 'Return to all payment_methods') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Code</th>
				<th>Name</th>
				<th>Fee</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $payment_method->code }}}</td>
					<td>{{{ $payment_method->name }}}</td>
					<td>{{{ $payment_method->fee }}}</td>
					<td>{{{ $payment_method->active }}}</td>
                    <td>{{ link_to_route('backend.payment_methods.edit', 'Edit', array($payment_method->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.payment_methods.destroy', $payment_method->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
