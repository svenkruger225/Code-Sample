@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Page ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Create Page
		<div class="pull-right">
			<a href="{{ route('backend.cms.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.cms.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group {{ $errors->has('parent_id') ? 'error' : '' }}">
				<label class="control-label" for="parent_id">Parent: </label>
				<div class="controls">
					{{ Form::select('parent_id', $parents, Input::old('parent_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('parent_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('route') ? 'error' : '' }}">
				<label class="control-label" for="name">Route: </label>
				<div class="controls">
					{{ Form::text('route', Input::old('route'), array('class'=>'input-xlarge')) }} (address bar)
					{{ $errors->first('route', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', Input::old('name'), array('class'=>'input-xlarge')) }} (main navigation)
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('title') ? 'error' : '' }}">
				<label class="control-label" for="title">Title: </label>
				<div class="controls">
					{{ Form::text('title', Input::old('title'), array('class'=>'input-xxlarge')) }} (page title)
					{{ $errors->first('title', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="title">Order: </label>
				<div class="controls">
					{{ Form::text('order', Input::old('order'), array('class'=>'input-small')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Link to Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Link to Course: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('url') ? 'error' : '' }}">
				<label class="control-label" for="url">URL: </label>
				<div class="controls">
					{{ Form::text('url', Input::old('url'), array('id'=>'url', 'class'=>'input-xlarge')) }} or {{ Form::select('urlselect', $urls, '', array('id'=>'urlselect', 'class'=>'input-xlarge')) }}				
					{{ $errors->first('url', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('view_name') ? 'error' : '' }}">
				<label class="control-label" for="view_name">View: </label>
				<div class="controls">
					{{ Form::select('view_name', $views, Input::old('view_name'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('view_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('meta_description') ? 'error' : '' }}">
				<label class="control-label" for="meta_description">meta description: </label>
				<div class="controls">
					{{ Form::textarea('meta_description', Input::old('meta_description'), array('class'=>'input-xxlarge')) }}
					{{ $errors->first('meta_description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('meta_keywords') ? 'error' : '' }}">
				<label class="control-label" for="meta_keywords">meta keywords: </label>
				<div class="controls">
					{{ Form::textarea('meta_keywords', Input::old('meta_keywords'), array('class'=>'input-xxlarge')) }}
					{{ $errors->first('meta_keywords', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('version') ? 'error' : '' }}">
				<label class="control-label" for="title">Version: </label>
				<div class="controls">
					{{ Form::text('version', Input::old('version'), array('class'=>'input-medium')) }}
					{{ $errors->first('version', '<span class="help-inline">:message</span>') }}
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
				{{ Form::submit('Create Page', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/cms.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


