@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Financial Transactions ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

	<div class="page-header">
		<div class="row-fluid">
			<div class="span12">  
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.reports.transactions', 'class'=>'form-inline')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
					{{ Form::select('order_type', $order_types, Input::old('order_type'), array('class'=>'input-medium')) }}		
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-medium')) }}				
					{{ Form::select('backend', array('2'=>'Back&Front', '1'=>'Backend', '0'=>'Frontend'), Input::old('backend'), array('class'=>'input-medium')) }}				
					{{ Form::select('method_id', $methods, Input::old('method_id'), array('class'=>'input-medium')) }}				
					{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>


@if (count($result) > 0)

	<table class="table table-striped table-bordered table-condensed table-hover span10">
		<thead>
			<tr>
				<th class="span2">Date/Time</th>
				<th class="span2">Location</th>
				<th class="span2">Course</th>
				<th class="span2">Customer</th>
				<th class="span2">Order</th>
				<th class="span2">Invoice</th>
				<th class="span2">Transaction</th>
				<th class="span2">Method</th>
				<th class="span2">Paid</th>
				<th class="span2">End</th>
				<th class="span2">User</th>
				<th class="span4">Notes</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($result['transactions'] as $transaction)
				<tr class="{{$transaction->class}}">
					<td>{{{ $transaction->payment_date }}}</td>
					<td>{{{ $transaction->location }}}</td>
					<td>{{{ $transaction->course }}}</td>
					<td>{{{ $transaction->customer }}}</td>
					<td><a href="{{$transaction->link}}" target="_blank">{{ $transaction->order_id }}</a></td>
					<td><a href="/backend/invoices/download/{{$transaction->invoice_id}}" target="_blank">{{ $transaction->invoice_id }}</a></td>
					<td><span class="pull-left">{{{ $transaction->payment_id }}}</span></td>
					<td><span class="pull-left">{{{ $transaction->method }}}</span></td>
					<td><span class="pull-right">{{ Utils::format_currency( $transaction->paid) }}</span></td>
					<td><span class="text-center">{{{ $transaction->end }}}</span></td>
					<td><span class="pull-left">{{{ $transaction->user }}}</span></td>
					<td><span class="pull-left">{{{ $transaction->notes }}}</span></td>
				</tr>
			@endforeach
			<tr style="background-color:#E0FFC1">
        		<td><strong>Grand Total</strong></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-right">{{ Utils::format_currency( $result['totals']['received']) }}</span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
				<td><span class="pull-left"> </span></td>
       		</tr>
		</tbody>
	</table>

@else
	There are no transactions
@endif



</div>
<div style="clear:both;">time total = {{Utils::getmicrotime() - $start}} </div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/financials.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop