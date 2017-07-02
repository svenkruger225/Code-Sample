@extends('backend/layouts/vanilla')

{{-- Page title --}}
@section('title')
Daily Tasks ::
@parent
@stop

{{-- Page content --}}
@section('content')

<table width="100%" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="3">
	<tbody>
		<tr>
			<td bgcolor="#ffffff">Course SMS Reminders</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">{{$messages['couse_name']}}</td>
		</tr>
		@foreach ( $messages['messages'] as $message )
		<tr>
			<td bgcolor="#ffffff">{{$message}}</td>
		</tr>
		@endforeach
		<tr>
			<td bgcolor="#ffffff">--------------------------------------------------------</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">Classes Without Trainers</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">{{$notrainers['couse_name']}}</td>
		</tr>
		@foreach ( $notrainers['courses'] as $course )
		<tr>
			<td bgcolor="#ffffff">{{$course}}</td>
		</tr>
		@endforeach
		<tr>
			<td bgcolor="#ffffff">--------------------------------------------------------</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">Course Repeats</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">{{$repeats['couse_name']}}</td>
		</tr>
		@foreach ( $repeats['courses'] as $course )
		<tr>
			<td bgcolor="#ffffff">{{$course}}</td>
		</tr>
		@endforeach
		<tr>
			<td bgcolor="#ffffff">--------------------------------------------------------</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">SMS Account Balance</td>
		</tr>
		<tr>
			<td bgcolor="#ffffff">{{$balance}}</td>
		</tr>
	</tbody>
</table>

@stop