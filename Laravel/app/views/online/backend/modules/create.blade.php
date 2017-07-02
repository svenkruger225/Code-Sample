@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create a Online Module ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div id="content">
<div class="page-header">
	<h3>
		Create Online Module

		<div class="pull-right">
			<a href="{{ route('online.modules.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'online.modules.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Course: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', Input::old('name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::textarea('description', Input::old('description'), array('id'=>'description')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('timeout') ? 'error' : '' }}">
				<label class="control-label" for="timeout">Timeout(minutes): </label>
				<div class="controls">
					{{ Form::text('timeout', Input::old('timeout'), array('class'=>'input-mini')) }}
					{{ $errors->first('timeout', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', Input::old('order'), array('class'=>'input-mini')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
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
				{{ Form::submit('Create Online Module', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/modules.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


