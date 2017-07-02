@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Calendar Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
<h4>Calendar</h4>
</div>
<div class="well container-fluid">
	<div id="calanderDay">
		@foreach ($result as $date =>$parents)
		<h4 class="titleH1">{{{ date("l d F Y", strtotime($date)) }}}</h4>
			@foreach ($parents as $parent =>$locations)
			<h4 class="titleH3">{{{ $parent }}}</h4>
				@foreach ($locations as $name =>$location)
				<h5 class="titleH4">{{{ $name }}}</h5>
				<table class="table table-bordered table-condensed">
					<tbody>
					<tr>
						<td class="span3"><strong>Actions</strong></td>
						<td class="span3"><strong>Course</strong></td>
						<td class="span1"><strong>Students</strong></td>
						<td class="span1"><strong>Max</strong></td>
						<td class="span1"><strong>Full</strong></td>
						<td class="span1"><strong>Auto</strong></td>
						<td class="span1"><strong>Course Date</strong></td>
						<td class="span1"><strong>Start Time</strong></td>
						<td class="span1"><strong>End Time</strong></td>
						<td class="span1"><strong>active</strong></td>
						<td class="span3"><strong>Instructors</strong></td>
					</tr>
					@foreach ($location as $id =>$instance)
					<tr class="{{$instance['class']}}" id="row{{$id}}">
						<td>
							<a href="#" data-bind="click: highlightRow.bind($data,'{{$id}}')"><i class="icon-magic icon-white"></i></a>
							&nbsp;<span><b>{{{$instance['type']}}}</b></span>&nbsp;
							<a href="#" class="btn btn-mini @if ($instance['type'] == 'Purchase') disabled @endif" title="OLGR Class List" alt="OLGR Class List" data-bind="click: openOlgrClassList.bind($data,'{{$id}}','{{$instance['type']}}')"><i class="icon-list"></i> Olgr</a>
							<a href="#" class="btn btn-mini @if ($instance['type'] == 'Purchase') disabled @endif" title="Class List" alt="Class List" data-bind="click: openClassList.bind($data,'{{$id}}','{{$instance['type']}}')"><i class="icon-list icon-white"></i> Class</a>
							@if ($instance['is_course_accredited'] == 0)
							<a href="#" class="btn btn-mini @if ($instance['type'] == 'Purchase') disabled @endif" title="Email Certificate to all class students" data-bind="click: sendCertificatesEmail.bind($data, '{{$id}}')">Email Cert.</a>
							@endif
							<a href="#" class="btn btn-mini @if ($instance['type'] == 'Purchase') disabled @endif" title="Merge Certificates of all class students" data-bind="click: downloadClassCertificates.bind($data, '{{$id}}')">Merge Cert.</a>
							<a href="#" class="btn btn-mini @if ($instance['type'] == 'Purchase') disabled @endif" title="Open Class List" alt="Class List" data-bind="click: showClassList.bind($data,'{{$id}}')"><i class="icon-folder-open icon-white"></i> <span class="showHide" id="showHideClassList{{$id}}">Show</span></a>
						</td>
						<td>{{{ $instance['course'] }}}</td>
						<td>{{{ $instance['students'] }}} ({{{ $instance['paid'] }}})</td>
						<td>{{ $instance['maximum_students'] }}</td>
						<td>@if($instance['full'] == '1') Y @else N @endif</td>
						<td>@if($instance['maximum_auto'] == '1') Y @else N @endif</td>
						<td>{{ $instance['course_date'] }}</td>
						<td>{{ $instance['time_start'] }}</td>
						<td>{{ $instance['time_end'] }}</td>
					 
						<td>@if($instance['active'] == '1') Y @else N @endif</td>
						<td style="text-align:left;">
						@if ( count($instance['instructors']) > 0 )
							{{{ implode(", ",$instance['instructors']) }}}
						@endif
						</td>
					</tr>
					<!-- ko if: $data.classList().length > 0 && $data.instanceId() == '{{$id}}' -->
					<tr><td colspan="12">
					<table class="table table-condensed">
					<tr>
						<td class="span2"></td>
						<td class="span1">Order ID</td>
						<td class="span2">First Name</td>
						<td class="span2">Surname</td>
						<td class="span2">Email Address</td>
						<td class="span1">Phone</td>
						<td class="span1">Mobile</td>
						<td class="span1">Paid</td>
						<td class="span1">Owing</td>
						<td class="span1">Certificate</td>
						<td class="span1">USI</td>
						<td class="span1">Avetmiss</td>
						<td class="span1">Agent</td>
					</tr>
					<!-- ko foreach: $data.classList -->
					<tr>
						<td>
						<a href="#" class="btn btn-mini" data-bind="click: openCertificateForm.bind($data, customer_id(), id())" title="Manage Certificate">Certificate</a>
						<!-- ko ifnot: is_course_accredited() -->
						<a href="#" class="btn btn-mini" data-bind="click: sendCertificateToStudent.bind($data, id(), certificate_id())" title="Email Certificate to Student">Email Cert.</a>
						<!-- /ko -->
						</td>
						<td><span data-bind="html: order_id"></span></td>
						<td><span data-bind="html: first_name"></span></td>
						<td><span data-bind="html: last_name"></span></td>
						<td><span data-bind="html: email"></span></td>
						<td><span data-bind="html: phone"></span></td>
						<td><span data-bind="html: mobile"></span></td>
						<td><span data-bind="html: Paid"></span></td>
						<td><span data-bind="html: Owing"></span></td>
						<td><span data-bind="html: certificate_id"></span></td>
						<td><span data-bind="html: usi_verified"></span></td>
						<td><span data-bind="html: avetmiss_done"></span></td>
						<td><span data-bind="html: agent_id"></span></td>
					</tr>
					<!-- /ko -->
					</table>
					</td></tr>
					<!-- /ko -->
					@endforeach
					</tbody>
				</table>
			@endforeach				
		@endforeach				
	@endforeach				
					
	</div>

</div>

@include('backend/calendar/roster-details')
@include('backend/calendar/certificate')
@include('backend/calendar/transaction')

	<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/calendar.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
