@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Group Bookings Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Group Bookings Management</h4></div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.groupinstances.index', 'class'=>'form-inline')) }}
				{{ Form::select('lid', $locations, Input::old('lid'), array('id'=>'locationList','class'=>'input-medium')) }}
				{{ Form::select('cid', $courses, Input::old('cid'), array('id'=>'courseList','class'=>'input-large')) }}
				{{ Form::text('from', Input::old('from'), array('id'=>'date_from','class'=>'input-small')) }}
				{{ Form::text('to', Input::old('to'), array('id'=>'date_to','class'=>'input-small')) }}
				{{ Form::button('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if ( count($group_bookings) > 0 )
	
	{{ $group_bookings->appends(array('lid'=>Input::old('lid'),'cid'=>Input::old('cid'),'from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}

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
				<th>Active</th>
				<th colspan="2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ( $group_bookings as $instance )
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
					<td>{{{ $instance->time_start }}}</td>
					<td>{{{ $instance->time_end }}}</td>
					<td>{{{ $instance->students }}}</td>
					<td>{{{ $instance->maximum_students }}}</td>
					<td>{{{ $instance->maximum_alert }}}</td>
					<td>{{{ $instance->maximum_auto }}}</td>
					<td>{{{ $instance->cancelled }}}</td>
					<td>{{{ $instance->active }}}</td>
                    <td>
						<a href="{{ route('backend.groupinstances.edit', array($instance->id)) }}?lid={{Input::old('lid')}}&cid={{Input::old('cid')}}&from={{Input::old('from')}}&to={{Input::old('to')}}" class="btn btn-mini btn-info">Edit</a>
					</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.groupinstances.destroy', $instance->id))) }}
                            {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
	
	{{ $group_bookings->appends(array('lid'=>Input::old('lid'),'cid'=>Input::old('cid'),'from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}

@else
	There are no Classes for the selected course
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/courseinstances.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

   


@stop