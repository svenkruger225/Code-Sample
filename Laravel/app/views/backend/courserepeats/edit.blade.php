@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Repeat Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Course Repeat Update

		<div class="pull-right">
			<a href="{{ route('backend.courserepeats.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($courserepeat, array('method' => 'PATCH', 'route' => array('backend.courserepeats.update', $courserepeat->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Course_id: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, $courserepeat->course_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location_id: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $courserepeat->location_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="monday">Repeat on: </label>
				<div class="controls inline">
					<table class="table table-striped table-bordered table-condensed span11">
						<tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th><th>Monthly</th></tr>
						<tr>
							<td><input type="hidden" name="monday" value="0" /><input type="checkbox" name="monday" value="1" {{ $courserepeat->monday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="tuesday" value="0" /><input type="checkbox" name="tuesday" value="1" {{ $courserepeat->tuesday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="wednesday" value="0" /><input type="checkbox" name="wednesday" value="1" {{ $courserepeat->wednesday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="thursday" value="0" /><input type="checkbox" name="thursday" value="1" {{ $courserepeat->thursday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="friday" value="0" /><input type="checkbox" name="friday" value="1" {{ $courserepeat->friday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="saturday" value="0" /><input type="checkbox" name="saturday" value="1" {{ $courserepeat->saturday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="sunday" value="0" /><input type="checkbox" name="sunday" value="1" {{ $courserepeat->sunday == '1' ? 'checked' : '' }} /></td>
							<td><input type="hidden" name="monthly" value="0" /><input type="checkbox" name="monthly" value="1" {{ $courserepeat->monthly == '1' ? 'checked' : '' }} /></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="control-group {{ $errors->has('start_date') ? 'error' : '' }}">
				<label class="control-label" for="start_date">Start_date: </label>
				<div class="controls">
					{{ Form::text('start_date', $courserepeat->start_date, array('class'=>'input-small', 'id'=>'start_date')) }}
					{{ $errors->first('start_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('end_date') ? 'error' : '' }}">
				<label class="control-label" for="end_date">End_date: </label>
				<div class="controls">
					{{ Form::text('end_date', $courserepeat->end_date, array('class'=>'input-small', 'id'=>'end_date')) }}
					{{ $errors->first('end_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('time_start') ? 'error' : '' }}">
				<label class="control-label" for="time_start">Time_start: </label>
				<div class="controls">
					{{ Form::select('time_start', $course_times, $courserepeat->time_start, array('class'=>'input-small')) }}				
					{{ $errors->first('time_start', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('time_end') ? 'error' : '' }}">
				<label class="control-label" for="time_end">Time_end: </label>
				<div class="controls">
					{{ Form::select('time_end', $course_times, $courserepeat->time_end, array('class'=>'input-small')) }}				
					{{ $errors->first('time_end', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('maximum_students') ? 'error' : '' }}">
				<label class="control-label" for="maximum_students">Maximum_students: </label>
				<div class="controls">
					{{ Form::text('maximum_students', $courserepeat->maximum_students, array('class'=>'input-small')) }}
					{{ $errors->first('maximum_students', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('maximum_alert') ? 'error' : '' }}">
				<label class="control-label" for="maximum_alert">Maximum_alert: </label>
				<div class="controls">
					{{ Form::text('maximum_alert', $courserepeat->maximum_alert, array('class'=>'input-small')) }}
					{{ $errors->first('maximum_alert', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('maximum_auto') ? 'error' : '' }}">
				<label class="control-label" for="maximum_auto">Maximum_auto: </label>
				<div class="controls">
					<input type="hidden" name="maximum_auto" value="0" /><input type="checkbox" name="maximum_auto" value="1" {{ $courserepeat->maximum_auto == '1' ? 'checked' : '' }} />
					{{ $errors->first('maximum_auto', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $courserepeat->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update Repeat', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.courserepeats.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/repeats.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop