@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Rosters Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Online Rosters Management</h4></div>
	</div>
</div>

@if (count($courses) > 0)

<div class="accordion" id="rosters_accordion">
@foreach ($courses as $course)
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle collapsed alert-info" data-toggle="collapse" data-parent="#rosters_accordion" href="#collapse{{$course->id}}">
				<h3>{{$course->name}}</h3>
			</a>
		</div>
		<div id="collapse{{$course->id}}" class="accordion-body collapse" style="height: 0px;">
			<div class="accordion-inner">

				<table class="table table-striped table-brostered table-condensed table-hover">
					<thead>
						<tr>
							<th class="span1">Roster Id</th>
							<th class="span3">Student</th>
							<th class="span1">Total</th>
							<th class="span1">Paid</th>
							<th class="span1">UserName</th>
							<th class="span1">Actions</th>
						</tr>
					</thead>

					<tbody>
						@foreach ($course->rosters as $roster)
							<tr>
								<td>{{{ $roster->id }}}</td>
								<td>{{ $roster->customer ? '<a href="/backend/customers/' . $roster->customer_id . '/edit" class="btn btn-link btn-info" target="_blank">'.$roster->customer->name.'</a>' : $roster->customer_id}}</td>
								<td>{{{ $roster->total }}}</td>
								<td>{{{ $roster->paid }}}</td>
								<td>{{{ $roster->customer && $roster->customer->user ? $roster->customer->user->username : ''}}}</td>
								<td>
									<a href="/online/progress/{{$roster->id}}" target="_blank" class="btn btn-mini btn-info">Progress</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>

			</div>
		</div>
	</div>
@endforeach
</div>

@else
	There are no courses
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/rosters.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
