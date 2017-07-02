@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Locations Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Locations Management

		<div class="pull-right">
			<a href="{{ route('backend.locations.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>

@if ($locations->count())
	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th>id</th>
				<th>Parent_id</th>
				<th>Name</th>
				<th>Short_name</th>
				<th>Address</th>
				<th>City</th>
				<th>State</th>
				<th>Active</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($locations as $location)
				<tr>
					<td>{{{ $location->id }}}</td>
					<td>{{{ $location->parent_id }}}</td>
					<td>{{{ $location->name }}}</td>
					<td>{{{ $location->short_name }}}</td>
					<td>{{{ $location->address }}}</td>
					<td>{{{ $location->city }}}</td>
					<td>{{{ $location->state }}}</td>
					<td>{{{ $location->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.locations.destroy', $location->id))) }}
                    <td>
						{{ link_to_route('backend.locations.edit', 'Edit', array($location->id), array('class' => 'btn btn-mini btn-info')) }}
	                    {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>

				@foreach ($location->children as $loc)
					<tr>
						<td>{{{ $loc->id }}}</td>
						<td>{{{ $loc->parent_id }}}</td>
						<td>{{{ $loc->name }}}</td>
						<td>{{{ $loc->short_name }}}</td>
						<td>{{{ $loc->address }}}</td>
						<td>{{{ $loc->city }}}</td>
						<td>{{{ $loc->state }}}</td>
						<td>{{{ $loc->active }}}</td>
						{{ Form::open(array('method' => 'DELETE', 'route' => array('backend.locations.destroy', $loc->id))) }}
						<td>
							{{ link_to_route('backend.locations.edit', 'Edit', array($loc->id), array('class' => 'btn btn-mini btn-info')) }}
	                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
						</td>
						{{ Form::close() }}
					</tr>
				@endforeach
				<tr>
					<td colspan="10"><hr /></td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no Locations
@endif
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/locations.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop