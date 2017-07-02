@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Message Marketing Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div id="content">
<div class="page-header">
	<h3>
		Message Marketing Update

		<div class="pull-right">
			<a href="#" class='btn btn-small btn-primary' data-bind='click: testEmailCmd.bind($data, {{ $message->id }} )'>Test Email</a> | 
			<a href="#" class='btn btn-small btn-warning' data-bind='click: processEmailCmd.bind($data, {{ $message->id }} )'>Process Email</a> | 
			<a href="{{ route('backend.marketing.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($message, array('method' => 'PATCH', 'route' => array('backend.marketing.update', $message->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $message->location_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Course: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, $message->course_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_from') ? 'error' : '' }}">
				<label class="control-label" for="date_from">Date_from: </label>
				<div class="controls">
					{{ Form::text('date_from', $message->date_from, array('class'=>'input-small', 'id'=>'date_from')) }}
					{{ $errors->first('date_from', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_to') ? 'error' : '' }}">
				<label class="control-label" for="date_to">Date_to: </label>
				<div class="controls">
					{{ Form::text('date_to', $message->date_to, array('class'=>'input-small', 'id'=>'date_to')) }}
					{{ $errors->first('date_to', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('subject') ? 'error' : '' }}">
				<label class="control-label" for="subject">Subject: </label>
				<div class="controls">
					{{ Form::text('subject', $message->subject, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('subject', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('attachments') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Attachments: </label>
				<div class="controls">
					{{ Form::select('attachments[]', $attachments, $message->attachments->lists('id'), array('id'=>'attachments', 'multiple' => true, 'size' => '6', 'class'=>'input-xxlarge')) }}				
			        &nbsp;<a href="#" class='btn btn-small btn-success' data-bind='click: openUploadAttachmentForm'>Upload Attachment</a>
					{{ $errors->first('attachments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('body') ? 'error' : '' }}">
				<label class="control-label" for="body">Email Body: </label>
				<div class="controls " data-bind="doubleClick: openInsertField">
					{{ Form::textarea('body', $message->body, array('id'=>'body', 'style'=>'display:none;', 'data-bind'=>"doubleClick: openInsertField")) }}
					{{ $errors->first('body', '<span class="help-inline">:message</span>') }}
				</div>
			</div>  
			<div class="control-group {{ $errors->has('sms_body') ? 'error' : '' }}">
				<label class="control-label" for="sms_body">SMS Body: </label>
				<div class="controls " data-bind="doubleClick: openInsertField">
					{{ Form::textarea('sms_body', $message->sms_body, array('id'=>'sms_body', 'style'=>'width:90%;', 'rows'=>'3', 'data-bind'=>"value: smsBody, event:{'doubleClick': openInsertField, 'blur':checkSmsBodyLength}")) }}
					{{ $errors->first('sms_body', '<span class="help-inline">:message</span>') }}
				</div>
			</div>  
			<div class="control-group {{ $errors->has('send_via') ? 'error' : '' }}">
				<label class="control-label" for="send_via">Send Via: </label>
				<div class="controls">
					{{ Form::select('send_via', $via_options, $message->via, array('class'=>'input-large')) }}				
					{{ $errors->first('send_via', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $message->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update', array('class' => 'btn btn-small btn-info', 'data-bind'=>"click :checkSmsBodyLength")) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

<div class="modal hide" id="insert-fields-modal">
	<div class="modal-header alert-info">
		<a class="close" data-dismiss="modal">x</a>
		<h3>Select Dynamic Field to Insert</h3>
	</div>
    <div class="modal-body-payment form-horizontal">
		<div class="control-group"></div>
		<div class="control-group"> 
		    <label class="control-label" for="message">Fields :</label>
		    <div class="controls">
				{{ Form::select('field', $fields, '', array('id'=>'field', 'class'=>'input-large')) }}				
			</div>
		</div>
	</div >
	<div class="modal-footer ">
	    <div class="control-group">
			<button class="btn btn-success" data-bind="click: insertField"><i class="icon-white icon-thumbs-up"></i> Insert</button>
	    </div>
	</div>
</div>


@include('backend.common.test-email-sms')
@include('backend.common.upload-attachment')

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/marketing.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop