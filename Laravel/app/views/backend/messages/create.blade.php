@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Message ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div id="content">
<div class="page-header">
	<h3>
		Create Message
		<div class="pull-right">
			<a href="{{ route('backend.messages.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.messages.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Course: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('message_id') ? 'error' : '' }}">
				<label class="control-label" for="message_id">Message_id: </label>
				<div class="controls">
					{{ Form::select('message_id', $messagetypes, Input::old('message_id'), array('class'=>'input-medium')) }}				
					{{ $errors->first('message_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('subject') ? 'error' : '' }}">
				<label class="control-label" for="subject">Subject: </label>
				<div class="controls">
					{{ Form::text('subject', Input::old('subject'), array('class'=>'input-xxlarge')) }}
					{{ $errors->first('subject', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('attachments') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Attachments: </label>
				<div class="controls">
					{{ Form::select('attachments[]', $attachments, Input::old('attachments'), array('multiple' => true, 'size' => '6', 'class'=>'input-xxlarge')) }}				
					{{ $errors->first('attachments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('body') ? 'error' : '' }}">
				<label class="control-label" for="body">Body: </label>
				<div class="controls ">
					{{ Form::textarea('body', Input::old('body'), array('id'=>'body', 'style'=>'display:none;')) }}
					{{ $errors->first('body', '<span class="help-inline">:message</span>') }}
				</div>
			</div>  
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					{{ Form::checkbox('active', '1', (Input::old('active') == '1')) }}
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
				{{ Form::submit('Create Message', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>

<script type="text/javascript">
    $(document).ready(function() {
		CKEDITOR.replace( 'body', {  on: {instanceReady: function(ev) {ev.editor.resize( '100%', '400', true );}	}	});
    });
</script>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/messages.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop


