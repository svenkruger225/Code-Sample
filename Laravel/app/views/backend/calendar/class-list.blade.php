@extends('backend/layouts/vanilla')

{{-- Page title --}}
@section('title')
Class List ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<h4 style="margin:0;">PLEASE FAX IMMEDIATELY AFTER CLASS TO {{{ $result['details']['location_fax'] }}}</h4>
		<p><strong>For any queries or issues please call 02 9211 4292 or  0425 304 774 or 0452 228 562<br>
		If urgent and office staff cannot assist phone call then 0416 073 071<br>
		Office Email: info@coffeeschool.com.au</strong></p>
	</div>
	<div class="row-fluid">
		<div class="pull-left"><h4 style="margin:0;">{{{ $result['details']['location'] }}} - {{date('d/m/Y', strtotime($result['details']['course_date']))}}</h4></div>
		<div class="pull-right">Generated: {{date('d/m/Y h:i A')}}</div>
	</div>
	<div class="row-fluid">
		<div class="pull-left"><h4>{{{ $result['details']['name'] }}} ({{date('h:i A', strtotime($result['details']['time_start']))}} to {{date('h:i A', strtotime($result['details']['time_end']))}}) - Instructors: {{implode(",", $result['details']['instructors'])}}</h4></div>
	</div>
</div>
	<h4>PAID  ({{count($result['paid'])}}) [ {{$result['details']['paid_usi']}} usi done - {{$result['details']['paid_usi_perc']}}% ]</h4>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<td><strong>ID</strong></td>
			<td><strong>Name</strong></td>
			<td><strong>Phone</strong></td>
			<td><strong>Paid</strong></td>
			<td><strong>Owing</strong></td>
			@if($result['details']['course_pair'])
			<td class="span1"><strong>Food Hygiene</strong></td>
			@endif
			<td><strong>USI</strong></td>
			<td><strong>AVETMISS</strong></td>
			<td><strong>Notes</strong></td>
			<td><strong>Special Needs</strong></td>
			<td width="60"><strong>$/Attendance</strong></td>
		</tr>
        @foreach($result['paid'] as $key => $paid)
		<tr>
			<td>{{{$paid['order_id']}}}</td>
			<td>{{{$paid['name']}}}</td>
			<td>{{{$paid['phone']}}}</td>
			<td>${{{$paid['paid']}}}</td>
			<td>${{{$paid['owing']}}}</td>
			@if($result['details']['course_pair'])
			<td class="td-wrap">{{$paid['fh']}}</td>
			@endif
			<td class="td-wrap">{{$paid['usi']}}</td>
			<td class="td-wrap">{{$paid['avetmiss']}}</td>
			<td class="td-wrap">{{$paid['notes']}}</td>
			<td class="td-wrap">{{$paid['needs']}}</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach
	</table>
	<h4>NOT PAID ({{count($result['owing'])}}) [{{$result['details']['notpaid_usi']}} usi done - {{$result['details']['notpaid_usi_perc']}}% ]</h4>
	<table class="table table-striped table-bordered table-condensed">
        <tr>
            <td><strong>ID</strong></td>
            <td><strong>Name</strong></td>
            <td><strong>Phone</strong></td>
            <td><strong>Students</strong></td>
            <td><strong>Paid</strong></td>
            <td><strong>Owing</strong></td>
			@if($result['details']['course_pair'])
			<td class="span1"><strong>Food Hygiene</strong></td>
			@endif
			<td><strong>USI</strong></td>
			<td><strong>AVETMISS</strong></td>
            <td><strong>Notes</strong></td>
            <td><strong>Special Needs</strong></td>
            <td width="60"><strong>$/Attendance</strong></td>
        </tr>
        @foreach($result['owing'] as $key => $owing)
		<tr>
			<td>{{{$owing['order_id']}}}</td>
			<td>{{{$owing['name']}}}</td>
			<td>{{{$owing['phone']}}}</td>
			<td>1</td>
			<td>{{{$owing['paid']}}}</td>
			<td>{{{$owing['owing']}}}</td>
			@if($result['details']['course_pair'])
			<td class="td-wrap">{{$owing['fh']}}</td>
			@endif
			<td class="td-wrap">{{$owing['usi']}}</td>
			<td class="td-wrap">{{$owing['avetmiss']}}</td>
			<td class="td-wrap">{{{$owing['notes']}}}</td>
			<td class="td-wrap">{{{$owing['needs']}}}</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach
	</table>

	<br /><br />
	<div><strong>Trainers to complete each section: </strong></div>
	<div>
	  <table class="table table-striped table-bordered">
		<tbody>
		<tr bgcolor="#F0F0F0">
			<td valign="top" width="25%"><p><strong>Trainers to   complete </strong></p></td>
			<td valign="top" width="25%"><p><strong>Class   summary</strong></p></td>
			<td valign="top" width="25%"><p><strong>Notes &ndash; (list   detail of expenses &amp; send receipts)</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Cash   Total</strong></p></td>
			<td valign="top" width="25%"><p><strong>$</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Expenses</strong></p></td>
			<td valign="top" width="25%"><p><strong>$</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Other</strong></p></td>
			<td valign="top" width="25%"><p><strong>$</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Total Cash to   Bank</strong></p></td>
			<td valign="top" width="25%"><p><strong>$</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Banking deposit date</strong></p></td>
			<td valign="top" width="25%"><p>&nbsp;</p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Certificates Used</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		  <tr>
			<td valign="top" width="25%"><p><strong>Milk Bottles Remaining</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
			<td valign="top" width="25%"><p><strong>&nbsp;</strong></p></td>
		  </tr>
		</tbody>
	  </table>
	</div>

	<p><strong>*&nbsp;Send class lists, bank receipts,   expenses receipts&nbsp;&amp;&nbsp;completed student forms to Sydney each  week. Please fax to (02) 8078 0677</strong></p>
	@if ($result['details']['type'] == 'public')
	<a href='{{URL::route("backend.calendar.classlistupdate", array("id" => $result["details"]["id"],"type" => $result["details"]["type"]))}}' class="btn btn-mini btn-inverse pull-right" title="Class List Update Form" alt="Class List Update Form" target="_blank" >Open Class List Update Form</a>
	@endif
	<br />
	<br />
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>Name</th>
			<th>PRINT CORRECTED SPELLING IF NECESSARY</th>
			<th>Sign to confirm spelling</th>
			<th>Certificate Number</th>
		</tr>
        @foreach($result['paid'] as $key => $paid)
		<tr>
			<td>{{{$paid['name']}}}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach			
        @foreach($result['owing'] as $key => $owing)
		<tr>
			<td>{{{$owing['name']}}}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach			
	</table>
	<br /><br />
</div>
@stop
