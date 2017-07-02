@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Messages Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Messages Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.messages.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.messages.index', 'class'=>'form-inline')) }}
				{{ Form::select('messages_location', $locations, Input::old('messages_location'), array('class'=>'input-large')) }}				
				{{ Form::select('messages_course', $courses, Input::old('messages_course'), array('class'=>'input-large')) }}				
				{{ Form::select('messages_type', $messagetypes, Input::old('messages_type'), array('class'=>'input-large')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if (count($messages) > 0)
	<table class="table table-striped table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<th class="span2">location</th>
				<th class="span3">Course</th>
				<th class="span2">Message_id</th>
				<th class="span3">Subject</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($messages as $message)
				<tr>
					<td>{{{ $message->location_id != null && $message->location != null ? $message->location->name : $message->location_id }}}</td>
					<td>{{{ $message->course_id != null ? $message->course->name : '' }}}</td>
					<td>{{{ $message->type->name }}}</td>
					<td>{{{ $message->subject }}}</td>
					<td>{{{ $message->active == '1' ? 'x' : '' }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.messages.destroy', $message->id))) }}
                    <td>
						<a href="#" class='btn btn-mini btn-primary' data-bind='click: testEmailCmd.bind($data, {{ $message->id }} )'>Test Email</a>
						<a href="{{ route('backend.messages.edit', array($message->id)) }}" class="btn btn-mini btn-info">Edit</a>
						<a href="{{ route('backend.messages.clone', array($message->id)) }}" class="btn btn-mini btn-primary">Clone</a>
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd', 'data-bind'=> 'click: deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no messages
@endif

    <div class="modal hide" id="test-email-modal">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Test Email</h3>
	    </div>
        <div class="modal-body-payment form-horizontal">
			<div class="control-group"></div>
		    <div class="control-group"> 
		        <label class="control-label" for="email">Email :</label>
		        <div class="controls">
					<input type="text" name="email" id="email" value="" data-bind="value: testData().email"/>
				</div>
		    </div>
		</div >
	    <div class="modal-footer ">
	        <div class="control-group">
				<button class="btn btn-success" data-bind="click: submitTestEmail"><i class="icon-white icon-thumbs-up"></i> Submit Test</button>
	        </div>
	    </div>
    </div>

</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/messages.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop