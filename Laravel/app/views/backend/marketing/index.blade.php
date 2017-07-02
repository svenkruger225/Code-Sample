@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Marketing Messages Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Marketing Messages Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.marketing.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.marketing.index', 'class'=>'form-inline')) }}
				{{ Form::select('messages_location', $locations, Input::old('messages_location'), array('class'=>'input-large')) }}				
				{{ Form::select('messages_course', $courses, Input::old('messages_course'), array('class'=>'input-large')) }}				
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
				<th class="span3">From/To</th>
				<th class="span3">Subject</th>
				<th class="span2">Send Via</th>
				<th class="span2">Emails</th>
				<th class="span2">Mobiles</th>
				<th class="span2">Sent Count</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($messages as $message)
				<tr>
					<td>{{{ $message->location_id != null && $message->location != null ? $message->location->name : $message->location_id }}}</td>
					<td>{{{ $message->course_id != null ? $message->course->name : '' }}}</td>
					<td>{{{ $message->from_to }}}</td>
					<td>{{{ $message->subject }}}</td>
					<td>{{{ $message->send_via }}}</td>
					<td>{{{ $message->email_count }}}</td>
					<td>{{{ $message->sms_count }}}</td>
					<td>{{{ $message->sent_count }}}</td>
					<td>{{{ $message->active == '1' ? 'x' : '' }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.marketing.destroy', $message->id))) }}
                    <td>
						<a href="{{ route('backend.marketing.edit', array($message->id)) }}" class="btn btn-mini btn-info">Edit</a>
						<a href="{{ route('backend.marketing.clone', array($message->id)) }}" class="btn btn-mini btn-primary">Clone</a>
						<a href="#" class='btn btn-mini btn-primary' data-bind='click: testEmailCmd.bind($data, {{ $message->id }} )'>Test Email</a>
						<a href="#" class='btn btn-mini btn-warning' data-bind='click: processEmailCmd.bind($data, {{ $message->id }} )'>Process Email</a>
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

@include('backend.common.test-email-sms')

</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/marketing.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop