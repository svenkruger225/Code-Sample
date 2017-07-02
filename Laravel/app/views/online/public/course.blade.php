@extends('online/layouts/default')

{{-- Page title --}}
@section('title')
@stop

{{-- Page content --}}
@section('content')
    <div id="content" class="row">
        <div class="col-sm-3 col-md-3">
			@include('online/common/online-nav')
        </div>
        <div class="col-sm-9 col-md-9">
			@if (isset($data->module) && !is_null($data->module)) 
				<h4>{{$data->module->name}}</h4>
			@else
				<h4>{{$data->course->description}}</h4>
			@endif
            <div class="well">
				@yield('course-content')
			</div>
        </div>
    </div>

@stop
