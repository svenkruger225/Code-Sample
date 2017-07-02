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
				<th class="span1">Id</th>
				<th class="span3">Name</th>
				<th class="span4">Description</th>
				<th class="span1">Price</th>
				<th class="span1">Gst</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($products as $product)
				<tr>
					<td>{{{ $product->id }}}</td>
					<td>{{{ $product->name }}}</td>
					<td>{{{ $product->description }}}</td>
					<td>{{{ $product->price }}}</td>
					<td>{{{ $product->gst == '1' ? 'X' : '' }}}</td>
					<td>{{{ $product->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.products.destroy', $product->id))) }}
                    <td>
						{{ link_to_route('backend.products.edit', 'Edit', array($product->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
				</tr>
                    {{ Form::close() }}
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
