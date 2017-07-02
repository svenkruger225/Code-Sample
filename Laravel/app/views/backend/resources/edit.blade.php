@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Resource Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/assets/js/ckeditor/ckeditor.js"></script>
<div class="page-header">
	<h3>
		Resource Update

		<div class="pull-right">
			<a href="{{ route('backend.resources.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($resource, array('method' => 'PATCH', 'route' => array('backend.resources.update', $resource->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group {{ $errors->has('type') ? 'error' : '' }}">
				<label class="control-label" for="type">Type: </label>
				<div class="controls">
					{{ Form::select('type', $types, $resource->type, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('type', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::text('description', $resource->description, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('description', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('content') ? 'error' : '' }}">
				<label class="control-label" for="content">Content: </label>
				<div class="controls">
					{{ Form::textarea('content', $resource->content, array('id'=>'content', 'class'=>'input-xxlarge')) }}
					{{ $errors->first('content', '<span class="help-inline">:resource</span>') }}
				</div>
			</div>  
			<!--<div class="control-group">
				<label class="control-label" for="content">Preview: </label>
				<div class="controls">
					<iframe id="iframe-content" frameborder="0" src="/backend/resources/{{$resource->id}}" style="width: 100%; height: 100%;"></iframe>
				</div>
			</div> --> 
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $resource->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/resources.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop