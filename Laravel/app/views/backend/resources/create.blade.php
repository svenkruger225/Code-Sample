@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create CMS Resource ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/assets/js/ckeditor/ckeditor.js"></script>
<div class="page-header">
	<h3>
		Create CMS Resource
		<div class="pull-right">
			<a href="{{ route('backend.resources.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.resources.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('type') ? 'error' : '' }}">
				<label class="control-label" for="type">Type: </label>
				<div class="controls">
					{{ Form::select('type', $types, Input::old('type'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('type', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::text('description', Input::old('description'), array('class'=>'input-xxlarge')) }}
					{{ $errors->first('description', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('content') ? 'error' : '' }}">
				<label class="control-label" for="content">Content: </label>
				<div class="controls">
					{{ Form::textarea('content', Input::old('content'), array('id'=>'content', 'class'=>'input-xxlarge')) }}
					{{ $errors->first('content', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>  
			<div class="control-group">
				<label class="control-label" for="content">Preview: </label>
				<div class="controls span10">
				<iframe id="iframe-content" frameborder="0" srcdoc="<html><head><link type='text/css' rel='stylesheet' href='/assets/css/frontend/bootstrap.css'><link type='text/css' rel='stylesheet' href='/assets/css/frontend/bootstrap-responsive.css'></head><body><div class='row-fluid'></div></body></html>" style="width: 100%; height: 100%;"></iframe>
				</div>
			</div>  
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					{{ Form::checkbox('active') }}
					{{ $errors->first('active', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>

		</div>
	</div>
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				{{ Form::submit('Create Resource', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/resources.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


