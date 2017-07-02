@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Attachments Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>Attachments Management
		<div class="pull-right">
			<a href="{{ route('backend.attachments.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>

@if ($attachments->count())
	<table class="table table-striped table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<th class="span1">Id</th>
				<th class="span2">Name</th>
				<th class="span4">Path</th>
				<th class="span1">Type</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($attachments as $attachment)
				<tr>
					<td>{{{ $attachment->id }}}</td>
					<td>{{{ $attachment->name }}}</td>
					<td>{{{ $attachment->path }}}</td>
					<td>{{{ $attachment->type }}}</td>
					<td>{{{ $attachment->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.attachments.destroy', $attachment->id))) }}
                    <td>
						{{ link_to_route('backend.attachments.edit', 'Edit', array($attachment->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no attachments
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/attachments.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop