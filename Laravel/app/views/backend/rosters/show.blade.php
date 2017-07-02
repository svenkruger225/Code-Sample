@extends('backend/layouts/default')

@section('content')

<h1>Show Roster</h1>

<p>{{ link_to_route('backend.rosters.index', 'Return to all rosters') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Course_instance_id</th>
				<th>Customer_id</th>
				<th>Certificate_id</th>
				<th>Comments</th>
				<th>Attendance</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $roster->course_instance_id }}}</td>
					<td>{{{ $roster->customer_id }}}</td>
					<td>{{{ $roster->certificate_id }}}</td>
					<td>{{{ $roster->comments }}}</td>
					<td>{{{ $roster->attendance }}}</td>
                    <td>{{ link_to_route('backend.rosters.edit', 'Edit', array($roster->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.rosters.destroy', $roster->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop