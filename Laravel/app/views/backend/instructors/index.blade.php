@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Instructors Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Instructors Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.users.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create User</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.instructors.index', 'class'=>'form-inline')) }}
				{{ Form::text('instructor_search', Input::old('instructor_search'), array('class'=>'input-medium', 'placeholder'=>'Search Name by')) }}				
				{{ Form::select('instructor_course_id', $courses, Input::old('instructor_course_id'), array('class'=>'input-xlarge')) }}				
				{{ Form::select('instructor_state', $states, Input::old('instructor_state'), array('class'=>'input-large')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if ($instructors->count())

{{ $instructors->links() }}


	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>User_id</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone</th>
				<th>City</th>
				<th>State</th>
				<th>Course Types</th>
				<th>Activated</th>
				<th class="span1">Message<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllMessageList}"/></th>
				<th class="span1"><a href="#" class="btn btn-mini" data-bind="click: openMessageForm.bind($data, 'User')"><i class="icon-envelope icon-white"></i> Open Bulk<BR>Message Form</a></th>
			</tr>
		</thead>

		<tbody>
			@foreach ($instructors as $instructor)
				<tr>
					<td><span class="instructor_id">{{{ $instructor->id }}}</span></td>
					<td><span class="instructor_name">{{{ $instructor->name }}}</span></td>
					<td><span class="instructor_email">{{{ $instructor->email }}}</span></td>
					<td><span class="instructor_mobile">{{{ $instructor->mobile }}}</span></td>
					<td>{{{ $instructor->business_city }}}</td>
					<td>{{{ $instructor->business_state }}}</td>
					<td>
						<table class="table table-striped table-bordered table-condensed">
						@foreach ($instructor->courses as $course)
							<tr><td>{{{ $course->name }}}</td></tr>
						@endforeach
						</table>
					</td>
					<td>{{{ $instructor->activated ? 'yes' : 'no' }}}</td>
					<td><input type="checkbox" class="messagelist" value="{{$instructor->id}}" data-bind="'event': {'click' : updateMessageList}"/></td>
                    <td>
						{{ link_to_route('backend.instructors.edit', 'Edit', array($instructor->id), array('class' => 'btn btn-mini btn-info')) }}
						@if ( ! is_null($instructor->deleted_at))
						<a href="{{ route('restoring/instructor', $instructor->id) }}" class="btn btn-mini btn-warning">@lang('button.restore')</a>
						@else
						@if (Sentry::getId() !== $instructor->id)
						<a href="{{ route('deleting/instructor', $instructor->id) }}" class="btn btn-mini btn-danger deleteCmd">@lang('button.delete')</a>
						@else
						<span class="btn btn-mini btn-danger disabled">@lang('button.delete')</span>
						@endif
						@endif
						<a href="#" class="btn btn-mini btn-warning" data-bind="click: openSingleMessageForm.bind($data, 'User','{{$instructor->id}}','{{$instructor->name}}','{{$instructor->email}}','{{$instructor->mobile}}')"><i class="icon-envelope icon-white"></i> Message</a>
				    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

{{ $instructors->links() }}

@else
	There are no instructors
@endif
@include('backend/common/bulk-message')
@include('backend/common/message')
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/users.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop