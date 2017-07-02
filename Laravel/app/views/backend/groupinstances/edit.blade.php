@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Update Group Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Update Group Booking

		<div class="pull-right">
			<a href="{{ route('backend.groupinstances.index', array('lid'=>Input::old('lid'),'cid'=>Input::old('cid'),'from'=>Input::old('from'),'to'=>Input::old('to'))) }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::model($groupinstance, array('method' => 'PATCH', 'route' => array('backend.groupinstances.update', $groupinstance->id), 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	{{ Form::hidden('lid', Input::old('lid'), array('id'=>'lid','class'=>'input-medium lid')) }}
	{{ Form::hidden('cid', Input::old('cid'), array('id'=>'cid','class'=>'input-large cid')) }}
	{{ Form::hidden('from', Input::old('from'), array('id'=>'from','class'=>'input-small from')) }}
	{{ Form::hidden('to', Input::old('to'), array('id'=>'to','class'=>'input-small to')) }}
	
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group">
				<label class="control-label" for="id">Id: </label>
				<div class="controls">
					<span>{{$groupinstance->id}}				
				</div>
			</div>
			<div class="control-group {{ $errors->has('group_name') ? 'error' : '' }}">
				<label class="control-label" for="group_name">Group Name: </label>
				<div class="controls">
					{{ Form::text('group_name', $groupinstance->group_name, array('class'=>'input-xxlarge')) }}				
					{{ $errors->first('group_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="course_id">Course: </label>
				<div class="controls">
					{{ Form::hidden('course_id', $groupinstance->course_id) }}				
					{{ Form::label('course_name', $groupinstance->course->name) }}				
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $groupinstance->lid, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('instructor_id') ? 'error' : '' }}">
				<label class="control-label" for="instructor_id">Instructors: </label>
				<div class="controls">
					{{ Form::select('instructor[]', $instructors, $groupinstance->instructors, array('multiple', 'class'=>'input-xlarge')) }}				
					{{ $errors->first('instructor_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order Id: </label>
				<div class="controls">
					{{ Form::text('order_id', $groupinstance->order_id, array('class'=>'input-small')) }}				
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('customer_id') ? 'error' : '' }}">
				<label class="control-label" for="customer_id">Customer Id: </label>
				<div class="controls">
					{{ Form::text('customer_id', $groupinstance->customer_id, array('class'=>'input-small')) }}	-  <span>{{$groupinstance->customer ? $groupinstance->customer->full_name : ''}}</span>			
					{{ $errors->first('customer_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('course_date') ? 'error' : '' }}">
				<label class="control-label" for="course_date">Course Date: </label>
				<div class="controls">
					{{ Form::text('course_date', $groupinstance->course_date, array('class'=>'input-medium')) }}
					{{ $errors->first('course_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('time_start') ? 'error' : '' }}">
				<label class="control-label" for="time_start">Time Start: </label>
				<div class="controls">
					{{ Form::text('time_start', $groupinstance->time_start, array('class'=>'input-medium')) }}
					{{ $errors->first('time_start', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('time_end') ? 'error' : '' }}">
				<label class="control-label" for="time_end">Time End: </label>
				<div class="controls">
					{{ Form::text('time_end', $groupinstance->time_end, array('class'=>'input-small')) }}
					{{ $errors->first('time_end', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('students') ? 'error' : '' }}">
				<label class="control-label" for="students">Students: </label>
				<div class="controls">
					{{ Form::hidden('students', $groupinstance->students) }}				
					{{ Form::label('students_label', $groupinstance->students) }}				
				</div>
			</div>

			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::textarea('description', $groupinstance->description, array('rows'=> 4, 'class'=>'input-xxlarge')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('notes') ? 'error' : '' }}">
				<label class="control-label" for="notes">Notes: </label>
				<div class="controls">
					{{ Form::textarea('notes', $groupinstance->notes, array('rows'=> 4, 'class'=>'input-xxlarge')) }}
					{{ $errors->first('notes', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $groupinstance->active == '1' ? 'checked' : '' }} />
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

@stop