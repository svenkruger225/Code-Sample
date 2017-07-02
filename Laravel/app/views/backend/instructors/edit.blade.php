@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Update Instructor ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Update Instructor

		<div class="pull-right">
			<a href="{{ route('backend.instructors.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::model($instructor, array('method' => 'PATCH', 'route' => array('backend.instructors.update', $instructor->id), 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
		
			<div class="control-group {{ $errors->has('user_id') ? 'error' : '' }}">
				<label class="control-label" for="user_id">Instructor Name: </label>
				<div class="controls">		
					{{ Form::label('user', $instructor->name, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('user_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('mobile') ? 'error' : '' }}">
				<label class="control-label" for="mobile">Mobile</label>
				<div class="controls">
					{{ Form::text('mobile', $instructor->mobile, array('class'=>'input-large')) }}				
					{{ $errors->first('mobile', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
				<label class="control-label" for="email">Email</label>
				<div class="controls">
					{{ Form::text('email', $instructor->email, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location</label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $instructor->location_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
		
			<div class="control-group {{ $errors->has('courses') ? 'error' : '' }}">
				<label class="control-label" for="courses">Courses: </label>
				<div class="controls">		
					{{ Form::select('courses[]', $courses, $instructor->courses, array('multiple'=> true, 'size' => 8,  'class'=>'input-xlarge')) }}				
					{{ $errors->first('courses', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="title">Activated: </label>
				<div class="controls">
					<input type="hidden" name="activated" value="0" /><input type="checkbox" name="activated" value="1" {{ $instructor->activated == '1' ? 'checked' : '' }} />
					{{ $errors->first('activated', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

		</div>
	</div>
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				{{ Form::submit('Update Instructor', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>
{{ Form::close() }}
@stop