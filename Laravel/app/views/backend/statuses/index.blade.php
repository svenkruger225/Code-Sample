@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Statuses Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>Statuses Management
		<div class="pull-right">
			<a href="{{ route('backend.statuses.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>
@if ($statuses->count())
	<table class="table table-striped table-bordered table-condensed table-hover span6">
		<thead>
			<tr>
				<th>Id</th>
				<th>Status_type</th>
				<th>Name</th>
				<th>Active</th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($statuses as $status)
				<tr>
					<td>{{{ $status->id }}}</td>
					<td>{{{ $status->status_type }}}</td>
					<td>{{{ $status->name }}}</td>
					<td>{{{ $status->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.statuses.destroy', $status->id))) }}
                    <td>
						{{ link_to_route('backend.statuses.edit', 'Edit', array($status->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no statuses
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/statuses.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
