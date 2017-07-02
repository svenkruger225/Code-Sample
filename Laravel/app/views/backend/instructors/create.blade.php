@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Instructor ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Create Instructor

		<div class="pull-right">
			<a href="{{ route('backend.instructors.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.instructors.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			
			<div class="control-group {{ $errors->has('user_id') ? 'error' : '' }}">
				<label class="control-label" for="user_id">User: </label>
				<div class="controls">		
					{{ Form::select('user_id', $users, '', array('class'=>'input-xlarge')) }}				
					{{ $errors->first('user_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			
			<div class="control-group {{ $errors->has('courses') ? 'error' : '' }}">
				<label class="control-label" for="courses">Courses: </label>
				<div class="controls">		
					{{ Form::select('courses[]', $courses, array(''), array('multiple', 'class'=>'input-xlarge')) }}				
					{{ $errors->first('courses', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="title">Active: </label>
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
				{{ Form::submit('Create Instructor', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>
{{ Form::close() }}

@stop


