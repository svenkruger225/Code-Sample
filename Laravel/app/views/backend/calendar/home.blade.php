@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Calendar Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
	<div class="page-header">
		@include('backend/calendar/search')
	</div>
	<div class="container-fluid">
	</div>
</div>
@stop