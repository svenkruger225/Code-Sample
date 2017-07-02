@extends('backend/layouts/default')

@section('content')

<h1>Show Payment</h1>

<p>{{ link_to_route('backend.payments.index', 'Return to all payments') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Invoice_id</th>
				<th>Payment_date</th>
				<th>Payment_method</th>
				<th>Status</th>
				<th>Total</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $payment->invoice_id }}}</td>
					<td>{{{ $payment->payment_date }}}</td>
					<td>{{{ $payment->payment_method }}}</td>
					<td>{{{ $payment->status }}}</td>
					<td>{{{ $payment->total }}}</td>
                    <td>{{ link_to_route('backend.payments.edit', 'Edit', array($payment->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.payments.destroy', $payment->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
