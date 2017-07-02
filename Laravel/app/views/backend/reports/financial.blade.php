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
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.reports.financial', 'class'=>'form-inline')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
					{{ Form::select('order_type', $order_types, Input::old('order_type'), array('class'=>'input-medium')) }}		
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('id'=> 'location_id', 'class'=>'input-medium')) }}				
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('id'=> 'course_id', 'class'=>'input-medium')) }}				
					{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>


@if (count($result) > 0)

	<table class="table table-striped table-bordered table-hover span8">
		<thead>
			<tr>
        		<th class="span2">Date</th>
				<th class="span2">Received</th>
				<th class="span2">Owing</th>
				<th class="span2">Total</th>
				<th class="span2">Students Paid</th>
				<th class="span2">Students Not Paid</th>
				<th class="span2">Students Total</th>
				<th class="span1"></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($result['payments'] as $payment)
				<tr>
					<td>{{{ date('F d Y', strtotime($payment['payment_date'])) }}}</td>
					<td><span class="pull-right">{{ Utils::format_currency( $payment['received']) }}</span></td>
					<td><span class="pull-right">{{ Utils::format_currency( $payment['owing']) }}</span></td>
					<td><span class="pull-right">{{ Utils::format_currency( $payment['total']) }}</span></td>
					<td><span class="pull-right">{{{ $payment['students_paid'] }}}</span></td>
					<td><span class="pull-right">{{{ $payment['students_owing'] }}}</span></td>
					<td><span class="pull-right">{{{ $payment['students'] }}}</span></td>
                    <td><a href="#" class="btn btn-mini btn-info" data-bind="click: displayOwingInfo.bind($data, '{{$payment['owing'] > 0 ? $payment['payment_date'] : null}}')">Owing Info</a></a></td>
				</tr>
			@endforeach
			<tr style="background-color:#E0FFC1">
        		<td><strong>Grand Total</strong></td>
				<td><span class="pull-right">{{ Utils::format_currency( $result['totals']['received']) }}</span></td>
				<td><span class="pull-right">{{ Utils::format_currency( $result['totals']['owing']) }}</span></td>
				<td><span class="pull-right">{{ Utils::format_currency( $result['totals']['total']) }}</span></td>
				<td><span class="pull-right">{{{ $result['totals']['students_paid'] }}}</span></td>
				<td><span class="pull-right">{{{ $result['totals']['students_owing'] }}}</span></td>
				<td><span class="pull-right">{{{ $result['totals']['students'] }}}</span></td>
				<td><span class="pull-right"> </span></td>
       		</tr>
		</tbody>
	</table>

@else
	There are no payments
@endif



</div>

<div style="clear:both;">time total = {{Utils::getmicrotime() - $start}} </div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/financials.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop