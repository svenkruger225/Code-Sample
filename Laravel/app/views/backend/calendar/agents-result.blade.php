@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Calendar Management ::
@parent
@stop

{{-- Page content --}}
@section('content')

<style>
	.course3 {
		background-color: #0099CC;
	}

	.course4 {
		background-color: #009966;
	}

	.course5 {
		background-color: #999966;
	}

	.course6 {
		background-color: #CC99CC;
	}

	.course7 {
		background-color: #FF9966;
	}

	.course8 {
		background-color: #CC6633;
	}

	.course9 {
		background-color: #CC9933;
	}

</style>


<div class="page-header">
	<div class="row-fluid">
		<div><h3>Welcome {{Sentry::getUser()->name}}</h3></div>
	</div>
	<div class="row-fluid">
		<div class="span12"><h4>Sydney time is {{date('d/m/Y h:i:s A', time())}}</h4></div> 
	</div>

</div>
<div class="well container-fluid">
	<div id="calanderDay">
		<table class="table table-bordered table-condensed">
			<tbody>
			<tr>
				<td class="span3"><strong>Location</strong></td>
				<td class="span3"><strong>Course Name</strong></td>
				<td class="span2"><strong>Course Date</strong></td>
				<td class="span2"><strong>Time Start</strong></td>
				<td class="span2"><strong>Seat Remaining</strong></td>
			</tr>
			@foreach ($result as $key => $instance)
			<tr class="course{{$instance['course_id']}}">
				<td>{{{ $instance['location_name'] }}}</td>
				<td>{{{ $instance['course_name'] }}}</td>
				<td>{{{ $instance['course_date'] }}}</td>
				<td>{{{ $instance['start_time'] }}}</td>
				<td>{{{ $instance['vacancies'] }}}</td>
			</tr>
			@endforeach
			</tbody>
		</table>
				
	</div>

</div>

@stop
