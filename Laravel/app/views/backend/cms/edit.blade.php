@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Page Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Page Update

		<div class="pull-right">
			<a href="{{ route('backend.cms.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($page, array('method' => 'PATCH', 'route' => array('backend.cms.update', $page->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group {{ $errors->has('parent_id') ? 'error' : '' }}">
				<label class="control-label" for="parent_id">Parent: </label>
				<div class="controls">
					{{ Form::select('parent_id', $parents, $page->parent_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('parent_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<!-- <div class="control-group {{ $errors->has('route') ? 'error' : '' }}">
				<label class="control-label" for="name">Route: </label>
				<div class="controls">
					{{ Form::text('route', $page->route, array('class'=>'input-xlarge')) }} (address bar)
					{{ $errors->first('route', '<span class="help-inline">:message</span>') }}
				</div>
			</div> -->
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $page->name, array('class'=>'input-xlarge')) }} (main navigation)
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('title') ? 'error' : '' }}">
				<label class="control-label" for="title">Title: </label>
				<div class="controls">
					{{ Form::text('title', $page->title, array('class'=>'input-xxlarge')) }} (page title)
					{{ $errors->first('title', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="title">Order: </label>
				<div class="controls">
					{{ Form::text('order', $page->order, array('class'=>'input-small')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Link to Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $page->location_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Link to Course: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, $page->course_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('url') ? 'error' : '' }}">
				<label class="control-label" for="url">URL: </label>
				<div class="controls">
					{{ Form::text('url', $page->url, array('id'=>'url', 'class'=>'input-xlarge')) }} or {{ Form::select('urlselect', $urls, '', array('id'=>'urlselect', 'class'=>'input-xlarge')) }}				
					{{ $errors->first('url', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('view_name') ? 'error' : '' }}">
				<label class="control-label" for="view_name">View: </label>
				<div class="controls">
					{{ Form::select('view_name', $views, $page->view_name, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('view_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('meta_description') ? 'error' : '' }}">
				<label class="control-label" for="meta_description">meta description: </label>
				<div class="controls">
					{{ Form::textarea('meta_description', $page->meta_description, array('rows'=>'6', 'class'=>'input-xxlarge')) }}
					{{ $errors->first('meta_description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('meta_keywords') ? 'error' : '' }}">
				<label class="control-label" for="meta_keywords">meta keywords: </label>
				<div class="controls">
					{{ Form::textarea('meta_keywords', $page->meta_keywords, array('rows'=>'6', 'class'=>'input-xxlarge')) }}
					{{ $errors->first('meta_keywords', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('version') ? 'error' : '' }}">
				<label class="control-label" for="title">Version: </label>
				<div class="controls">
					{{ Form::text('version', $page->version, array('class'=>'input-medium')) }}
					{{ $errors->first('version', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $page->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.cms.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/cms.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop