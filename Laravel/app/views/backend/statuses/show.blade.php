@extends('backend/layouts/default')

@section('content')

<h1>Show Status</h1>

<p>{{ link_to_route('backend.statuses.index', 'Return to all statuses') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Status_type</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $status->name }}}</td>
					<td>{{{ $status->status_type }}}</td>
					<td>{{{ $status->active }}}</td>
                    <td>{{ link_to_route('backend.statuses.edit', 'Edit', array($status->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.statuses.destroy', $status->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
