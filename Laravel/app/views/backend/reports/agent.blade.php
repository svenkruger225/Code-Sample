@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Financial Totals ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

	<div class="page-header">
		<div class="row-fluid">
			<div class="span12">  
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.reports.agent', 'class'=>'form-inline', 'id'=>'agent-report-form')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
					{{ Form::select('agent_id', $agents, Input::old('agent_idf'), array('class'=>'input-medium')) }}		
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('id'=> 'location_id', 'class'=>'input-medium')) }}				
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('id'=> 'course_id', 'class'=>'input-medium')) }}				
					{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd', 'id'=>'agent-report-submit')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>

		@if (count($result) > 0)
			@foreach ($result as $agent_name => $entries)
			<div class="row-fluid">
				<div class="span12">  
					<table class="table table-striped table-bordered table-hover">
						<caption><h2>{{$agent_name}}</h2></caption>
						<thead>
							<tr>
        						<th class="span1">Date</th>
								<th class="span1">OrderId</th>
								<th class="span2">Student Name</th>
								<th class="span2">Course Name</th>
								<th class="span2">Location</th>
								<th class="span1">Type</th>
								<th class="span1">Price</th>
								<th class="span1">Paid</th>
								<th class="span1">Commission</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($entries as $entry)
								<tr>
									<td>{{{ date('d/m/Y', strtotime($entry->order_date)) }}}</td>
									<td>{{$entry->order_id}}</td>
									<td>{{$entry->full_name}}</td>
									<td>{{$entry->course_name}}</td>
									<td>{{$entry->location_name}}</td>
									<td>{{$entry->type}}</td>
									<td><span class="pull-right">{{ Utils::format_currency( $entry->price) }}</span></td>
									<td><span class="pull-right">{{ Utils::format_currency( $entry->paid) }}</span></td>
									<td></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			@endforeach
			
			<script>
				$(function() { 
					$action = $("#agent-report-form").attr("action");
					$("#agent-report-form").attr("action", $action + "/csv");
					$("#agent-report-submit").click();
					$("#agent-report-form").attr("action", $action);
				});
			</script>			
			
		@else
		<div class="row-fluid">
			<div class="span12">  
			<p>There are no payments</p>
			</div>
		</div>
		@endif


</div>

<div style="clear:both;">time total = {{Utils::getmicrotime() - $start}} </div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/financials.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop