@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Update Course Class ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Update Course Class

		<div class="pull-right">
			<a href="{{ route('backend.instances.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::model($courseinstance, array('method' => 'PATCH', 'route' => array('backend.instances.update', $courseinstance->id), 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group">
				<label class="control-label" for="course_id">Course: </label>
				<div class="controls">
					<input type="hidden" name="course_id" value="{{$courseinstance->course->id}}" />
					<span>{{$courseinstance->id}} - {{$courseinstance->course->name }}</span>				
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location:</label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $courseinstance->location->id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('instructor_id') ? 'error' : '' }}">
				<label class="control-label" for="instructor_id">Instructors: </label>
				<div class="controls">
					{{ Form::select('instructor[]', $instructors, $courseinstance->instructors, array('multiple', 'class'=>'input-xlarge')) }}				
					{{ $errors->first('instructor_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('course_date') ? 'error' : '' }}">
				<label class="control-label" for="course_date">Course Date: </label>
				<div class="controls">
					{{ Form::text('course_date', $courseinstance->course_date, array('id'=>'course_date','class'=>'input-medium')) }}
					{{ $errors->first('course_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('time_start') ? 'error' : '' }}">
				<label class="control-label" for="time_start">Time Start: </label>
				<div class="controls">
					{{ Form::select('time_start', $course_times, $courseinstance->start_time, array('class'=>'input-small')) }}				
					{{ $errors->first('time_start', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('time_end') ? 'error' : '' }}">
				<label class="control-label" for="time_end">Time End: </label>
				<div class="controls">
					{{ Form::select('time_end', $course_times, $courseinstance->end_time, array('class'=>'input-small')) }}				
					{{ $errors->first('time_end', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('students') ? 'error' : '' }}">
				<label class="control-label" for="students">Students: </label>
				<div class="controls">
					{{ Form::text('students', $courseinstance->students, array('class'=>'input-mini')) }}
					{{ $errors->first('students', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Special: </label>
				<div class="controls">
					<table class="table table-striped table-bordered table-condensed " style="max-width:600px;">
						<thead>
							<tr>
								<th>Price Original</th>
								<th>Price OffLine</th>
								<th>Price OnLine</th>
								<th>Active</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="hidden" name="price_id" value="{{$courseinstance->special ? $courseinstance->special->id : ''}}"/>
								<input type="text" name="price_original" class="input-medium" value="{{$courseinstance->special ? $courseinstance->special->price_original : ''}}"/></td>
								<td><input type="text" name="price_offline" class="input-medium" value="{{$courseinstance->special ? $courseinstance->special->price_offline : ''}}" /></td>
								<td><input type="text" name="price_online" class="input-medium" value="{{$courseinstance->special ? $courseinstance->special->price_online : ''}}" /></td>
								<td>{{ Form::select('price_active', array('1'=> 'active', '0'=> 'inactive'), $courseinstance->special ? $courseinstance->special->active : '0', array('class'=>'input-small')) }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<div class="control-group {{ $errors->has('maximum_students') ? 'error' : '' }}">
				<label class="control-label" for="maximum_students">Maximum students: </label>
				<div class="controls">
					{{ Form::text('maximum_students', $courseinstance->maximum_students, array('class'=>'input-mini')) }}
					{{ $errors->first('maximum_students', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('maximum_alert') ? 'error' : '' }}">
				<label class="control-label" for="maximum_alert">Maximum alert: </label>
				<div class="controls">
					{{ Form::text('maximum_alert', $courseinstance->maximum_alert, array('class'=>'input-mini')) }}
					{{ $errors->first('maximum_alert', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('maximum_auto') ? 'error' : '' }}">
				<label class="control-label" for="maximum_auto">Maximum auto: </label>
				<div class="controls">
					<input type="hidden" name="maximum_auto" value="0" /><input type="checkbox" name="maximum_auto" value="1" {{ $courseinstance->maximum_auto == '1' ? 'checked' : '' }} />
					{{ $errors->first('maximum_auto', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('full') ? 'error' : '' }}">
				<label class="control-label" for="full">Class full: </label>
				<div class="controls">
					<input type="hidden" name="full" value="0" /><input type="checkbox" name="full" value="1" {{ $courseinstance->full == '1' ? 'checked' : '' }} />
					{{ $errors->first('full', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('cancelled') ? 'error' : '' }}">
				<label class="control-label" for="cancelled">Cancelled: </label>
				<div class="controls">
					<input type="hidden" name="cancelled" value="0" /><input type="checkbox" name="cancelled" value="1" {{ $courseinstance->cancelled == '1' ? 'checked' : '' }} />
					{{ $errors->first('cancelled', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $courseinstance->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update Class', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>
{{ Form::close() }}
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/courseinstances.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop