@extends('backend/layouts/default')

@section('content')

<h1>Show Item</h1>

<p>{{ link_to_route('backend.items.index', 'Return to all items') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Order_id</th>
				<th>Customer_id</th>
				<th>Qty</th>
				<th>Price</th>
				<th>Total</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $item->order_id }}}</td>
					<td>{{{ $item->customer_id }}}</td>
					<td>{{{ $item->qty }}}</td>
					<td>{{{ $item->price }}}</td>
					<td>{{{ $item->total }}}</td>
                    <td>{{ link_to_route('backend.items.edit', 'Edit', array($item->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.items.destroy', $item->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
