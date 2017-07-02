@extends('backend/layouts/default')

@section('content')

<h1>Show Product</h1>

<p>{{ link_to_route('backend.products.index', 'Return to all products') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Category_id</th>
				<th>Image_id</th>
				<th>Name</th>
				<th>Description</th>
				<th>Stock</th>
				<th>Price</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $product->category_id }}}</td>
					<td>{{{ $product->image_id }}}</td>
					<td>{{{ $product->name }}}</td>
					<td>{{{ $product->description }}}</td>
					<td>{{{ $product->stock }}}</td>
					<td>{{{ $product->price }}}</td>
					<td>{{{ $product->active }}}</td>
                    <td>{{ link_to_route('backend.products.edit', 'Edit', array($product->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.products.destroy', $product->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
