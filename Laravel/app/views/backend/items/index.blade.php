@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Order Items Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>Order Items Management
		<div class="pull-right">
			<a href="{{ route('backend.items.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>


@if ($items->count())
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th>Order_id</th>
				<th>product_id</th>
				<th>Qty</th>
				<th>Price</th>
				<th>Total</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($items as $item)
				<tr>
					<td>{{{ $item->order_id }}}</td>
					<td>{{{ $item->product_id }}}</td>
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
			@endforeach
		</tbody>
	</table>
@else
	There are no items
@endif

@stop
