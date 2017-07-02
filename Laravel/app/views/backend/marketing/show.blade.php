@extends('backend/layouts/default')

@section('content')

<h1>Show Email</h1>

<p>{{ link_to_route('backend.emails.index', 'Return to all emails') }}</p>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Course_id</th>
				<th>Message_id</th>
				<th>Subject</th>
				<th>Body</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $email->course_id }}}</td>
					<td>{{{ $email->message_id }}}</td>
					<td>{{{ $email->subject }}}</td>
					<td>{{{ $email->body }}}</td>
					<td>{{{ $email->active }}}</td>
                    <td>{{ link_to_route('backend.emails.edit', 'Edit', array($email->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.emails.destroy', $email->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop