@extends('backend/layouts/default')

@section('content')

<h1>Show Attachment</h1>

<p>{{ link_to_route('backend.attachments.index', 'Return to all attachments') }}</p>

<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Name</th>
				<th>Path</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $attachment->name }}}</td>
					<td>{{{ $attachment->path }}}</td>
					<td>{{{ $attachment->active }}}</td>
                    <td>{{ link_to_route('backend.attachments.edit', 'Edit', array($attachment->id), array('class' => 'btn btn-mini btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.attachments.destroy', $attachment->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop