@extends('backend/layouts/default')

@section('content')

<h1>Edit Message</h1>
{{ Form::model($messagetype, array('method' => 'PATCH', 'route' => array('backend.messagetypes.update', $messagetype->id))) }}
	<ul>
        <li>
            {{ Form::label('name', 'Name:') }}
            {{ Form::text('name') }}
        </li>

        <li>
            {{ Form::label('active', 'Active:') }}
            <input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $messagetype->active == '1' ? 'checked' : '' }} />
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-mini btn-info')) }}
			{{ link_to_route('backend.messagetypes.show', 'Cancel', $messagetype->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop