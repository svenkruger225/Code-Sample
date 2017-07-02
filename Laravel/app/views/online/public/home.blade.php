@extends('online/layouts/default')

{{-- Page title --}}
@section('title')
@stop

{{-- Page content --}}
@section('content')

<h2>Home</h2>

<p></p>

@include('online/common/courses-grid')
	
@stop
