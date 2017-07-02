@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Products Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>Products Management
		<div class="pull-right">
			<a href="{{ route('backend.products.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>
@if ($products->count())
	<table class="table table-striped table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<th width="15%">Category</th>
				<th width="20%">Photo</th>
				<th width="20%">Name</th>
				<th width="20%">Description</th>
				<th width="5%">Stock</th>
				<th width="8%">Price</th>
				<th width="8%">Options</th>
				<th width="5%">Active</th>
				<th width="12%" colspan="2">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($products as $product)
				<tr>
					<td>{{{ $product->category->name }}}</td>
					<td>@if ($product->photo) {{{ $product->photo->name }}} @endif</td>
					<td>{{{ $product->name }}}</td>
					<td>{{{ $product->description }}}</td>
					<td>{{{ $product->stock }}}</td>
					<td>{{{ $product->price }}}</td>
					<td>
						<table class="table table-striped table-bordered table-condensed">
						@foreach ($product->options as $option)
							<tr>
							<td width="30%">{{{ $option->name }}}</td>
							<td width="45%">${{{ $option->value }}}</td>
							<td width="5%">${{{ $option->stock }}}</td>
							<td width="20%">${{{ $option->price }}}</td>
							</tr>
						@endforeach
						</table>
					</td>
					<td>{{{ $product->active }}}</td>
                    <td>{{ link_to_route('backend.products.edit', 'Edit', array($product->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.products.destroy', $product->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no products
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/products.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
