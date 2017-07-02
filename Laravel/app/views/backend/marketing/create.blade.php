@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Marketing Message ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div id="content">
<div class="page-header">
	<h3>
		Create Marketing Message
		<div class="pull-right">
			<a href="{{ route('backend.marketing.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.marketing.store', 'class'=>'form-horizontal')) }}

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
			<div class="control-group {{ $errors->has('date_from') ? 'error' : '' }}">
				<label class="control-label" for="date_from">Date_from: </label>
				<div class="controls">
					{{ Form::text('date_from', Input::old('date_from'), array('class'=>'input-small', 'id'=>'date_from')) }}
					{{ $errors->first('date_from', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_to') ? 'error' : '' }}">
				<label class="control-label" for="date_to">Date_to: </label>
				<div class="controls">
					{{ Form::text('date_to', Input::old('date_to'), array('class'=>'input-small', 'id'=>'date_to')) }}
					{{ $errors->first('date_to', '<span class="help-inline">:message</span>') }}
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
					{{ Form::select('attachments[]', $attachments, Input::old('attachments'), array('id'=>'attachments', 'multiple' => true, 'size' => '6', 'class'=>'input-xxlarge')) }}				
			        &nbsp;<a href="#" class='btn btn-small btn-success' data-bind='click: openUploadAttachmentForm'>Upload Attachment</a>
					{{ $errors->first('attachments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('body') ? 'error' : '' }}">
				<label class="control-label" for="body">Email Body: </label>
				<div class="controls ">
					{{ Form::textarea('body', Input::old('body'), array('id'=>'body', 'style'=>'display:none;', 'data-bind'=>"doubleClick: openInsertField")) }}
					{{ $errors->first('body', '<span class="help-inline">:message</span>') }}
				</div>
			</div>  
			<div class="control-group {{ $errors->has('sms_body') ? 'error' : '' }}">
				<label class="control-label" for="sms_body">SMS Body: </label>
				<div class="controls ">
					{{ Form::textarea('sms_body', Input::old('sms_body'), array('id'=>'sms_body', 'style'=>'width:90%;', 'rows'=>'3', 'data-bind'=>"value: smsBody, event:{'doubleClick': openInsertField, 'blur':checkSmsBodyLength}")) }}
					{{ $errors->first('sms_body', '<span class="help-inline">:message</span>') }}
				</div>
			</div>  
			<div class="control-group {{ $errors->has('send_via') ? 'error' : '' }}">
				<label class="control-label" for="send_via">Send Via: </label>
				<div class="controls">
					{{ Form::select('send_via', $via_options, Input::old('send_via'), array('class'=>'input-large')) }}				
					{{ $errors->first('send_via', '<span class="help-inline">:message</span>') }}
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
				{{ Form::submit('Create Message', array('class' => 'btn btn-small btn-info', 'data-bind'=>"click :checkSmsBodyLength")) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

@include('backend.common.upload-attachment')

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/marketing.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop


