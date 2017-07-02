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
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.reports.staff_financial', 'class'=>'form-inline')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
					{{ Form::select('order_type', $order_types, Input::old('order_type'), array('class'=>'input-medium')) }}		
					{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>


@if (count($result) > 0)

	<table class="table table-striped table-bordered table-condensed table-hover span10">
		<tbody>
			@foreach ($result['staffs'] as $staff)
			<tr>
				<th colspan="3"><h3>{{empty($staff['name']) ? 'No User' : $staff['name']}}</h3></th>
			</tr>
			<tr>
				<th class="span2">Date/Time</th>
				<th class="span2">Qty</th>
				<th class="span2">Total</th>
			</tr>
				@foreach ($staff['transactions'] as $transaction)
				<tr>
					<td>{{{ $transaction->order_date }}}</td>
					<td>{{{ $transaction->qty }}}</td>
					<td><span class="pull-right">{{ Utils::format_currency( $transaction->total) }}</span></td>
				</tr>
				@endforeach
			<tr style="background-color:#F4F4F4">
        		<td><strong>{{$staff['name']}} Totals</strong></td>
				<td>{{$staff['qty']}}</td>
				<td><span class="pull-right">{{ Utils::format_currency( $staff['total']) }}</span></td>
       		</tr>
			<tr style="background-color:#F0F0F0">
        		<td colspan="3">&nbsp;</td>
       		</tr>
			@endforeach
			<tr style="background-color:#F0F0F0">
        		<td colspan="3">&nbsp;</td>
       		</tr>
			<tr style="background-color:#E0FFC1">
        		<td><strong>Grand Totals</strong></td>
				<td>{{$result['qty'] }}</td>
				<td><span class="pull-right">{{ Utils::format_currency( $result['totals'] ) }}</span></td>
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