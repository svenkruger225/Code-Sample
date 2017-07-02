@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create a Course Repeat ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Create Course Repeat

		<div class="pull-right">
			<a href="{{ route('backend.courserepeats.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.courserepeats.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Course_id: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location_id: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="monday">Repeat on: </label>
				<div class="controls inline">
					<table class="table table-striped table-bordered table-condensed">
						<tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th><th>Monthly</th></tr>
						<tr><td>{{ Form::checkbox('monday') }}</td><td>{{ Form::checkbox('tuesday') }}</td><td>{{ Form::checkbox('wednesday') }}</td>
							<td>{{ Form::checkbox('thursday') }}</td><td>{{ Form::checkbox('friday') }}</td><td>{{ Form::checkbox('saturday') }}</td>
							<td>{{ Form::checkbox('sunday') }}</td>
							<td>{{ Form::checkbox('monthly') }}</td></tr>
					</table>
				</div>
			</div>
			<div class="control-group {{ $errors->has('start_date') ? 'error' : '' }}">
				<label class="control-label" for="start_date">Start_date: </label>
				<div class="controls">
					{{ Form::text('start_date', Input::old('start_date'), array('class'=>'input-small', 'id'=>'start_date')) }}
					{{ $errors->first('start_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('end_date') ? 'error' : '' }}">
				<label class="control-label" for="end_date">End_date: </label>
				<div class="controls">
					{{ Form::text('end_date', Input::old('end_date'), array('class'=>'input-small', 'id'=>'end_date')) }}
					{{ $errors->first('end_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('time_start') ? 'error' : '' }}">
				<label class="control-label" for="time_start">Time_start: </label>
				<div class="controls">
					{{ Form::select('time_start', $course_times, Input::old('time_start'), array('class'=>'input-small')) }}				
					{{ $errors->first('time_start', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('time_end') ? 'error' : '' }}">
				<label class="control-label" for="time_end">Time_end: </label>
				<div class="controls">
					{{ Form::select('time_end', $course_times, Input::old('time_end'), array('class'=>'input-small')) }}				
					{{ $errors->first('time_end', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('maximum_students') ? 'error' : '' }}">
				<label class="control-label" for="maximum_students">Maximum_students: </label>
				<div class="controls">
					{{ Form::text('maximum_students', Input::old('maximum_students'), array('class'=>'input-small')) }}
					{{ $errors->first('maximum_students', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('maximum_alert') ? 'error' : '' }}">
				<label class="control-label" for="maximum_alert">Maximum_alert: </label>
				<div class="controls">
					{{ Form::text('maximum_alert', Input::old('maximum_alert'), array('class'=>'input-small')) }}
					{{ $errors->first('maximum_alert', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('maximum_auto') ? 'error' : '' }}">
				<label class="control-label" for="maximum_auto">Maximum_auto: </label>
				<div class="controls">
					{{ Form::checkbox('maximum_auto') }}
					{{ $errors->first('maximum_auto', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					{{ Form::checkbox('active') }}
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

		</div>
	</div>
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				{{ Form::submit('Create Repeat', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/repeats.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


