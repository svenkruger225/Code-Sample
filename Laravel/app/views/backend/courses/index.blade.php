@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>Course Management
		<div class="pull-right">
			<a href="{{ route('backend.courses.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>

@if ($courses->count())
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th>Id</th>
				<th>Short_name</th>
				<th>Name</th>
				<th>Description</th>
				<th>Type</th>
				<th>Order</th>
				<th>Gst</th>
				<th>Active</th>
				<th>Myob_code</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($courses as $Course)
				<tr>
					<td>{{{ $Course->id }}}</td>
					<td>{{{ $Course->short_name }}}</td>
					<td>{{{ $Course->name }}}</td>
					<td>{{{ $Course->description }}}</td>
					<td>{{{ $Course->type }}}</td>
					<td>{{{ $Course->order }}}</td>
					<td>{{{ $Course->gst == 1 ? 'x' : '' }}}</td>
					<td>{{{ $Course->active == 1 ? 'x' : '' }}}</td>
					<td>{{{ $Course->myob_code }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.courses.destroy', $Course->id))) }}
                    <td>
						{{ link_to_route('backend.courses.edit', 'Edit', array($Course->id), array('class' => 'btn btn-mini btn-info editCmd', 'id'=>"Edit$Course->id", 'data-bind'=>"click: EditCourse.bind(&#36;data,'Edit$Course->id'), clickBubble: false")) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd', 'data-bind'=>'click: DeleteCourse, clickBubble: false')) }}
						@if ($Course->modules->count())
							<a href="/online/modules/?course_id={{ $Course->id }}" class="btn btn-mini btn-warning" data-bind="clickBubble: false">View Modules</a>
						@endif
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no Courses
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/courses.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop