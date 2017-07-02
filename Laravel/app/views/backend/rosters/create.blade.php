@extends('backend/layouts/default')

@section('content')

<h1>Create Roster</h1>

{{ Form::open(array('route' => 'backend.rosters.store')) }}
	<ul>
        <li>
            {{ Form::label('course_instance_id', 'Course_instance_id:') }}
            {{ Form::input('number', 'course_instance_id') }}
        </li>

        <li>
            {{ Form::label('customer_id', 'Customer_id:') }}
            {{ Form::input('number', 'customer_id') }}
        </li>

        <li>
            {{ Form::label('certificate_id', 'Certificate_id:') }}
            {{ Form::input('number', 'certificate_id') }}
        </li>

        <li>
            {{ Form::label('comments', 'Comments:') }}
            {{ Form::textarea('comments') }}
        </li>

        <li>
            {{ Form::label('attendance', 'Attendance:') }}
            {{ Form::text('attendance') }}
        </li>

		<li>
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


