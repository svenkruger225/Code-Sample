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
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.reports.financialentries', 'class'=>'form-inline')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
					{{ Form::select('order_type', $order_types, Input::old('order_type'), array('class'=>'input-medium')) }}		
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-medium')) }}				
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-medium')) }}				
					{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>


@if (count($items) > 0)

	<table class="table table-striped table-bordered table-hover table-condensed span12">
		<thead>
			<tr>
        		<th class="span1">Order Id</th>
				<th class="span1">order date</th>
				<th class="span1">course date</th>
				<th class="span1">End</th>
				<th class="span2">Customer</th>
				<th class="span1">item type</th>
				<th class="span2">item descr</th>
				<th class="span1">item qty</th>
				<th class="span1">item price</th>
				<th class="span1">item total</th>
				<th class="span1">paid</th>
				<th class="span1">is paid</th>
				<th class="span2">methods</th>
				<th class="span1">voucher_course_id</th>
				<th class="span1">location_id</th>
				<th class="span1">course_id</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($items as $item)
				<tr>
					<td>
					<button class="btn btn-mini" data-bind="click: openBookingForm.bind($data,'{{$item->order_id}}','{{$item->order_type}}')" >{{$item->order_id}}</button>
					</td>
					<td>{{{ date('d/m/Y', strtotime($item->the_date)) }}}</td>
					<td>{{{ date('d/m/Y', strtotime($item->order_date)) }}}</td>
					<td>{{$item->backend == '1' ? 'Backend' : 'Frontend'}}</td>
					<td class="td-wrap">{{$item->customer_name}}</td>
					<td>{{$item->item_type}}</td>
					<td class="td-wrap">{{$item->description}}</td>
					<td>{{$item->qty}}</td>
					<td><span class="pull-right">{{ Utils::format_currency( $item->price) }}</span></td>
					<td><span class="pull-right">{{ Utils::format_currency( $item->total) }}</span></td>
					<td>{{$item->paid}}</td>
					<td>{{$item->paid >= $item->total ? 'yes' : 'no'}}</td>
					<td class="td-wrap">{{$item->methods}}</td>
					<td>{{$item->voucher_course_id}}</td>
					<td>{{$item->location_id}}</td>
					<td>{{$item->course_id}}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no entries
@endif



</div>

<div style="clear:both;">time total = {{Utils::getmicrotime() - $start}} </div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/financials.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop