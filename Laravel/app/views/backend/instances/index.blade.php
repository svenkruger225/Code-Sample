@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Courses Classes Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Courses Classes Management</h4></div>
		<div class="span2 pull-right">
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.instances.create', 'class'=>'form-inline')) }}
				{{ Form::hidden('l_id', Input::old('l_id'), array('class'=>'lid')) }}
				{{ Form::hidden('c_id', Input::old('c_id'), array('class'=>'cid')) }}
				{{ Form::hidden('from', Input::old('from'), array('class'=>'from')) }}
				{{ Form::hidden('to', Input::old('to'), array('class'=>'to')) }}
				{{ Form::submit('Create New', array('class' => 'btn btn-small btn-info')) }}
			{{ Form::close() }}
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.instances.index', 'class'=>'form-inline')) }}
				{{ Form::select('l_id', $locations, Input::old('l_id'), array('id'=>'locationList','class'=>'input-medium')) }}
				{{ Form::select('c_id', $courses, Input::old('c_id'), array('id'=>'courseList','class'=>'input-large')) }}
				{{ Form::text('from', Input::old('from'), array('id'=>'date_from','class'=>'input-small')) }}
				{{ Form::text('to', Input::old('to'), array('id'=>'date_to','class'=>'input-small')) }}
				{{ Form::submit('Load', array('class' => 'btn btn-info')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if ( count($courseinstances) > 0 )
	
	{{ $courseinstances->appends(array('lid'=>Input::old('lid'),'cid'=>Input::old('cid'),'from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}

	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th data-bind="sort: { arr: courseInstances, prop: 'location_id' }">Location</th>
				<th>Instructors</th>
				<th data-bind="sort: { arr: courseInstances, prop: 'course_date' }">Date</th>
				<th>Start</th>
				<th>End</th>
				<th>Students</th>
				<th>Max</th>
				<th>alert @</th>
				<th>auto</th>
				<th>Cancelled</th>
				<th>Special</th>
				<th>Active</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ( $courseinstances as $instance )
				<tr>
					<td>{{{ $instance->location ? $instance->location->name : $instance->location_id}}}</td>
					<td>
						@if ( count($instance->instructors) > 0 )
						<table class="table table-striped table-bordered table-condensed">
							@foreach ( $instance->instructors as $instructor )
							<tr><td>{{{ $instructor->first_name }}} {{{ $instructor->last_name }}}</td></tr>
							@endforeach
						</table>
						@endif
					</td>
					<td>{{{ $instance->course_date }}}</td>
					<td>{{{ $instance->start_time }}}</td>
					<td>{{{ $instance->end_time }}}</td>
					<td>{{{ $instance->students }}}</td>
					<td>{{{ $instance->maximum_students }}}</td>
					<td>{{{ $instance->maximum_alert }}}</td>
					<td>{{{ $instance->maximum_auto }}}</td>
					<td>{{{ $instance->cancelled }}}</td>
					<td>{{{ $instance->special ? $instance->special->price_online : '' }}}</td>
					<td>{{{ $instance->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.instances.destroy', $instance->id))) }}
					{{ Form::hidden('l_id', Input::old('l_id'), array('class'=>'lid')) }}
					{{ Form::hidden('c_id', Input::old('c_id'), array('class'=>'cid')) }}
					{{ Form::hidden('from', Input::old('from'), array('class'=>'from')) }}
					{{ Form::hidden('to', Input::old('to'), array('class'=>'to')) }}
                    <td>
						<a href="{{ route('backend.instances.edit', array($instance->id)) }}" class="btn btn-mini btn-info">Edit</a>
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
	
	{{ $courseinstances->appends(array('lid'=>Input::old('lid'),'cid'=>Input::old('cid'),'from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}

@else
	There are no Classes for the selected course
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/courseinstances.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

   


@stop