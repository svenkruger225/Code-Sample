@extends('online/public/course')

{{-- Page title --}}
@section('title')
@stop

{{-- Page content --}}
@section('course-content')

	<div>
		<div>{{$data->module->description}}</div>
		@if ($data->module->steps && $data->module->steps->count())
		<a href="/online/step/{{$data->module->steps->first()->id}}" class="btn btn-default btn-md pull-right" role="button">Start Module</a>
		@endif
	</div>

@stop