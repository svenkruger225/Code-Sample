@extends('backend/layouts/vanilla')

{{-- Page title --}}
@section('title')
Class Update List ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="pull-left"><h4 style="margin:0;">{{{ $result['details']['location'] }}} - {{date('d/m/Y', strtotime($result['details']['course_date']))}}</h4></div>
		<div class="pull-right">Generated: {{date('d/m/Y h:i A')}}</div>
	</div>
	<div class="row-fluid">
		<div class="pull-left"><h4>{{{ $result['details']['name'] }}} ({{date('h:i A', strtotime($result['details']['time_start']))}} to {{date('h:i A', strtotime($result['details']['time_end']))}}) - Instructors: {{implode(",", $result['details']['instructors'])}}</h4></div>
	</div>
</div>

	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th width="20%">Name</th>
			<th width="25%">PRINT CORRECTED SPELLING IF NECESSARY</th>
			<th width="15%">Sign to confirm spelling</th>
			<th width="10%">Date of Birth</th>
			<th width="20%">email</th>
			<th width="10%">Certificate Number</th>
		</tr>
        @foreach($result['paid'] as $key => $paid)
		<tr>
			<td>{{{$paid['name']}}}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach			
		<tr>
			<td colspan="6"><b>NOT PAID</b></td>
		</tr>
        @foreach($result['owing'] as $key => $owing)
		<tr>
			<td>{{{$owing['name']}}}</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach			
	</table>
</div>
@stop
