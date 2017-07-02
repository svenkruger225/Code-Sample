@extends('backend/layouts/default')

@section('content')

<h1>Show Voucher</h1>

<p>{{ link_to_route('backend.vouchers.index', 'Return to all vouchers') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Customer_id</th>
				<th>Course_id</th>
				<th>Location_id</th>
				<th>Expiry_date</th>
				<th>Status_id</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $voucher->customer_id }}}</td>
					<td>{{{ $voucher->course_id }}}</td>
					<td>{{{ $voucher->location_id }}}</td>
					<td>{{{ $voucher->expiry_date }}}</td>
					<td>{{{ $voucher->status_id }}}</td>
					<td>{{{ $voucher->active }}}</td>
                    <td>{{ link_to_route('backend.vouchers.edit', 'Edit', array($voucher->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.vouchers.destroy', $voucher->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop