@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Tasks Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
		<div class="row-fluid">
			<div class="span12">  
				<div class="span4">  
					<h3>Tasks Management</h3>
				</div> 
				<div class="span8">  
					{{ Form::open(array('method' => 'POST', 'route' => 'backend.tasks.run', 'class'=>'form-inline')) }}
						{{ Form::token() }}
						From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" /> &nbsp;&nbsp;
						To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" /> &nbsp;&nbsp;
						or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" /> &nbsp;&nbsp;
						Task Type: {{ Form::select('report_type', Array(''=>'Select Task', 'DailyTasks'=>'DailyTasks', 'HourlyTask'=>'HourlyTask','UsiTask'=>'UsiTask'), Input::old('report_type'), array('id'=>'report_type', 'class'=>'input-medium')) }}		
						{{ Form::submit('Run', array('class' => 'btn btn-info loadCmd')) }}
					{{ Form::close() }}
				</div> 
			</div> 
		</div>
</div>

@if (count($results) > 0)

	<table class="table table-bordered table-striped table-condensed table-hover">
		<tbody>

		@if ($results['type'] == 'Daily')
		
			<tr><td colspan="4"><h3>Course SMS Reminders</h3></td></tr>
			@if(count($results['messages']) > 0)
					@foreach ( $results['messages'] as $key => $course )
					<tr><td colspan="4"><b>{{$course['course_name']}}</b></td></tr>
					@foreach ( $course['messages'] as $message )
					<tr>
						<td width="30">&nbsp;</td>
						<td colspan="3">{{$message}}</td>
					</tr>
					@endforeach
					@endforeach
			@endif
			<tr><td><hr /></td></tr>
			
			<tr><td colspan="4"><h3>Classes Without Trainers</h3></td></tr>
			@if(count($results['notrainers']) > 0)
				@foreach ( $results['notrainers'] as $course )
				<tr>
					<td width="100">{{$course['course_type']}}</td>
					<td width="200"><b>{{$course['course_name']}}</b></td>
					<td width="200">{{$course['location']}}</td>
					<td width="200">{{$course['class']}}</td>
				</tr>
				@endforeach
			@endif
			<tr><td colspan="4"><hr /></td></tr>
			
			<tr><td colspan="4"><h3>Course Repeats</h3></td></tr>
			@if(count($results['repeats']) > 0)
				@foreach ( $results['repeats'] as $course )
				<tr>
					<td><b>{{$course['course_name']}}</b></td>
					<td colspan="3">{{$course['message']}}</td>
				</tr>
				@endforeach
			@endif
			<tr><td colspan="4"><hr /></td></tr>
			
			<tr><td colspan="4"><h3>SMS Account Balance : {{$results['balance']}}</h3></td></tr>
			<tr><td colspan="4"><hr /></td></tr>
			
			<tr><td colspan="4"><h3>Log files deleted : {{$results['cleaning']}}</h3></td></tr>
			<tr><td colspan="4"><hr /></td></tr>
		@endif
		@if ($results['type'] == 'Hourly')
			
			<tr><td colspan="4"><h3>Open Orders</h3></td></tr>
			@if(count($results['open_orders']) > 0)
				<tr>
					<td><b>Order Id</b></td>
					<td>End</td>
					<td>Total to Pay</td>
					<td>Payment Message</td>
				</tr>
				@foreach ( $results['open_orders'] as $order )
				<tr>
					<td><b>{{$order['OrderId']}}</b></td>
					<td><b>{{$order['Backend'] == '1' ? 'Backend' : 'Frontend' }}</b></td>
					<td>{{$order['TotalToPay']}}</td>
					<td class="td-wrap">{{$order['PaymentMessage']}}</td>
				</tr>
				@endforeach
			@endif
			
		@endif

		@if ($results['type'] == 'Usi')
			
			<tr><td colspan="4"><h3>USI Reminders</h3></td></tr>
			@if(count($results['messages']) > 0)
					@foreach ( $results['messages'] as $key => $course )
					<tr><td colspan="4"><b>{{isset($course['course_name']) ? $course['course_name'] : 'No Students'}}</b></td></tr>
					@if(isset($course['messages']))
						@foreach ( $course['messages'] as $message )
						<tr>
							<td width="30">&nbsp;</td>
							<td colspan="3">{{$message}}</td>
						</tr>
						@endforeach
					@endif
					@endforeach
			@endif
			
		@endif

		</tbody>
	</table>

@endif


</div>	
<div style="clear:both;">time total = {{Utils::getmicrotime() - $start}} </div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/dailytasks.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop