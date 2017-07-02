@extends('backend/layouts/default')

@section('content')

<h1>All Messages</h1>

<p>{{ link_to_route('backend.messagetypes.create', 'Add new messagetype') }}</p>

@if ($messagetypes->count())
	<table class="table table-striped table-bordered table-condensed table-hover span8">
		<thead>
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Active</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($messagetypes as $messagetype)
				<tr>
					<td>{{{ $messagetype->id }}}</td>
					<td>{{{ $messagetype->name }}}</td>
					<td>{{{ $messagetype->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.messagetypes.destroy', $messagetype->id))) }}
                    <td>
					{{ link_to_route('backend.messagetypes.edit', 'Edit', array($messagetype->id), array('class' => 'btn btn-mini btn-info')) }}
                         {{ Form::submit('Delete', array('class' => 'btn btn-mini btn-danger')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no messagetypes
@endif

@stop