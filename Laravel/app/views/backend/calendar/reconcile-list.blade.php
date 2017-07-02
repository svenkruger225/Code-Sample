@extends('backend/layouts/vanilla')

{{-- Page title --}}
@section('title')
Reconcile ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="pull-left"><h3 style="margin:0;">{{{ $result['details']['location'] }}} - {{date('d/m/Y', strtotime($result['details']['course_date']))}}</h3></div>
		<div class="pull-right">Generated: {{date('d/m/Y h:i A')}}</div>
	</div>
	<div class="row-fluid">
		<div class="pull-left"><h4>{{{ $result['details']['name'] }}} ({{date('h:i A', strtotime($result['details']['time_start']))}} to {{date('h:i A', strtotime($result['details']['time_end']))}}) - Instructors: {{implode(",", $result['details']['instructors'])}}</h4></div>
		<div class="pull-right"><a class="btn btn-small " href="/backend/booking/newBooking" target="_blank">ADD WALK-IN...</a></div>
	</div>
</div>
	<h4>PAID  ({{count($result['paid'])}})</h4>
	<table class="table table-bordered table-condensed">
        <tr>
            <th class="span1">ID</th>
            <th class="span3">Name</th>
            <th class="span2">Phone</th>
            <th class="span1">Paid</th>
            <th class="span1">Owing</th>
            <th class="span2">No Show<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllNoShowList}"/></th>
            <th class="span1">Message<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllMessageList}"/></th>
            <th class="span1"><a href="#" class="btn btn-mini" data-bind="click: openMessageForm.bind($data, 'Paid')"><i class="icon-envelope"></i> Open Bulk<BR>Message Form</a></th>
        </tr>
        @foreach($result['paid'] as $key => $paid)
		<tr id="row{{$paid['roster_id']}}">
			<td>
			<a href="#" data-bind="click: highlightRow.bind($data,'{{$paid['roster_id']}}')"><i class="icon-magic"></i></a>&nbsp;&nbsp; 
			{{{$paid['order_id']}}}</td>
			<td>{{{$paid['name']}}}</td>
			<td>{{{$paid['phone']}}}</td>
			<td>${{{$paid['paid']}}}</td>
			<td>${{{$paid['owing']}}}</td>
			<td><input type="checkbox" class="noshowlist" value="{{$paid['roster_id']}}" data-bind="'event': {'click' : updateNoShowList}"/></td>
			<td><input type="checkbox" class="messagelist" value="{{$paid['roster_id']}}" data-bind="'event': {'click' : updateMessageList}"/></td>
			<td>
				<!-- <a href="#" class="btn btn-mini" data-bind="click: openRosterDetails.bind($data, '{{$paid['roster_id']}}')"><i class="icon-edit"></i> Edit Roster</a> -->
				<a href="#" class="btn btn-mini" data-bind="click: editBooking.bind($data, {{$paid['order_id']}})"><i class="icon-edit"></i> Edit Order</a>
			</td>
		</tr>
        @endforeach
    </table>
	<div class="pull-right">
		<a href="#" class="btn btn-small" data-bind="click: updatePaidList"><i class="icon-check"></i> UPDATE ALL PAID</a>
	</div>
	
	<h4>NOT PAID ({{count($result['owing'])}})</h4>
	<table class="table table-bordered table-condensed">
        <tr>
            <th class="span1">ID</th>
            <th class="span3">Name</th>
            <th class="span2">Phone</th>
            <th class="span1">Paid</th>
            <th class="span1">Owing</th>
            <th class="span1">Deactivate<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllDeactivateList}"/></th>
            <th class="span1">Paid Cash<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllPaidCashList}"/></th>
            <th class="span1">Message<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllMessageNotPaidList}"/></th>
            <th class="span1"><a href="#" class="btn btn-mini" data-bind="click: openMessageForm.bind($data, 'NotPaid')"><i class="icon-envelope"></i> Open Bulk<br>Message Form</a></th>
        </tr>
        @foreach($result['owing'] as $key => $owing)
		<tr id="row{{$owing['roster_id']}}">
			<td>
			<a href="#" data-bind="click: highlightRow.bind($data,'{{$owing['roster_id']}}')"><i class="icon-magic"></i></a>&nbsp;&nbsp;
			{{{$owing['order_id']}}}</td>
			<td>{{{$owing['name']}}}</td>
			<td>{{{$owing['phone']}}}</td>
			<td>${{{$owing['paid']}}}</td>
			<td>${{{$owing['owing']}}}</td>
			<td>@if($owing['paid'] == '0')<input type="checkbox" class="deactivatelist" value="{{$owing['roster_id']}}" data-bind="'event': {'click' : updateDeactivateList}"/>@endif</td>
			<td>@if($owing['paid'] == '0')<input type="checkbox" class="paidcashlist" value="{{$owing['roster_id']}}" data-bind="'event': {'click' : updatePaidCashList}"/>@endif</td>
			<td><input type="checkbox" class="messageNotPaidlist" value="{{$owing['roster_id']}}" data-bind="'event': {'click' : updateMessageNotPaidList}"/></td>
			<td>
				<!-- <a href="#" class="btn btn-mini" data-bind="click: openRosterDetails.bind($data, '{{$owing['roster_id']}}')"><i class="icon-edit"></i> Edit Roster</a> -->
				<a href="#" class="btn btn-mini" data-bind="click: editBooking.bind($data, {{$owing['order_id']}})"><i class="icon-edit"></i> Edit Order</a>		
			</td>
		</tr>
        @endforeach
		
    </table>
	<div class="pull-right">
		<a href="#" class="btn btn-small" data-bind="click: updateNotPaidList"><i class="icon-check"></i> UPDATE ALL NOT PAID</a>
	</div>

@include('backend/common/bulk-message')

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/reconcile.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop
