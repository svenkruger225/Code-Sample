@extends('online/public/course')

{{-- Page title --}}
@section('title')
@stop

{{-- Page content --}}
@section('course-content')

<ul>
	@foreach ( $data->course->modules as $index => $module_details )
	<li>
		<a href="/online/module/{{$module_details->id}}">{{sprintf("%02d", $index + 1)}} - {{$module_details->name}}</a>
	</li>
	@endforeach
</ul>
	
@stop
