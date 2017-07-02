@extends('backend/layouts/default')

@section('content')

<h1>Show Message</h1>

<p>{{ link_to_route('backend.messages.index', 'Return to all messages') }}</p>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Name</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $message->name }}}</td>
					<td>{{{ $message->active }}}</td>
                    <td>{{ link_to_route('backend.messages.edit', 'Edit', array($message->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.messages.destroy', $message->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop