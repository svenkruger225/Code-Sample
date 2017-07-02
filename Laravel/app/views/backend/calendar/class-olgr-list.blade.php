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
		If urgent and office staff cannot assist phone call then 0426 883 100<br>
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
<table class="table table-bordered table-condensed" style="margin-bottom: 0px !important;">
	<tr>
		<td colspan="3"><h4>PAID  ({{count($result['paid'])}})</h4></td>
		<td colspan="2"><h4>{{$result['details']['paid_usi']}} usi done - {{$result['details']['paid_usi_perc']}}%</h4></td>
		<td colspan="2"><h4>{{$result['details']['paid_no_usi']}} (NO NO) - {{$result['details']['paid_no_usi_perc']}}%</h4></td>
		<td colspan="3"><h4>Total Amount: ${{$result['details']['paidtotal']}}</h4></td>
		@if($result['details']['course_pair'])
		<td></td>
		@endif
	</tr>
</table>
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)	
<table class="table table-striped table-bordered table-condensed" style="margin-bottom: 0px !important;">
@else
<table class="table table-striped table-bordered table-condensed">
@endif
	<tr>
		<td class="span1"><strong>ID</strong></td>
		<td class="span3"><strong>Name</strong></td>
		<td class="span2"><strong>Phone</strong></td>
		@if($result['details']['course_pair'])
		<td class="span1"><strong>Food Hygiene</strong></td>
		@endif
		<td><strong>Notes</strong></td>
		<td><strong>Special Needs</strong></td>
		<td width="4%"><strong>USI</strong></td>
		<td width="6%"><strong>AVETMISS</strong></td>
		<td width="6%"><strong>Paid</strong></td>
		<td width="6%"><strong>Owing</strong></td>
		<td width="7%"><strong>$/Attendance</strong></td>
	</tr>
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)	
</table>
@endif

@foreach($result['paid'] as $key => $paid)
	@if (
		($result['details']['course_name'] == 'RSA') && 
		($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
		($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
	)	
	<table class="table table-bordered table-condensed">
	@endif
		<tr>
			
			<td><a href="/backend/booking/search/{{{ $paid['order_id'] }}}" data-bind="attr: { href : '/backend/booking/search/{{{ $paid['order_id']}}}' }" class="btn btn-mini" >{{{$paid['order_id']}}}</a></td>
			<td class="span3 td-wrap"><b>{{{$paid['name']}}}</b></td>
			<td class="span2">{{{$paid['phone']}}}</td>
			@if($result['details']['course_pair'])
			<td class="span1">{{$paid['fh']}}</td>
			@endif
			<td class="td-wrap">{{$paid['notes']}}</td>
			<td class="td-wrap">{{$paid['needs']}}</td>
			<td width="4%">{{$paid['usi']}}</td>
			<td width="6%">{{$paid['avetmiss']}}</td>
			<td width="6%">${{{$paid['paid']}}}</td>
			<td width="6%">${{{$paid['owing']}}}</td>
			<td width="7%">&nbsp;</td>
		</tr>
		
		@if (
			($result['details']['course_name'] == 'RSA') && 
			($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
			($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
		)
		<tr>
			<td colspan="{{ ($result['details']['course_pair']) ? 11 : 10 }}">
				<table class="table table-bordered table-condensed" style="margin-bottom: 0px !important;">
				@if ($result['details']['state'] == 'VIC')
					<tr>
						<td class="span2 td-wrap"><b>title:</b> {{$paid['title']}}</td>
						<td class="td-wrap"><b>gender:</b> {{$paid['gender']}}</td>
						<td class="td-wrap"><b>surname:</b> {{$paid['surname']}}</td>
						<td class="td-wrap"><b>given:</b> <b>{{$paid['given']}}</b></td>
						<td class="td-wrap"><b>middle:</b> {{$paid['middle']}}</td>
						<td class="td-wrap"><b>dob:</b> {{$paid['dob']}}</td>
						<td class="td-wrap"><b>suburb:</b> {{$paid['suburb']}}</td>
						<td class="td-wrap"><b>postcode:</b> {{$paid['post_code']}}</td>
						<td class="td-wrap"><b>state:</b> {{$paid['state']}}</td>
					</tr>
				@else
					<tr style="background-color: #f9f9f9;">
						<td class="span2 td-wrap"><b>title:</b> {{$paid['title']}}</td>
						<td class="td-wrap"><b>gender:</b> {{$paid['gender']}}</td>
						<td class="td-wrap"><b>surname:</b> {{$paid['surname']}}</td>
						<td class="td-wrap"><b>given:</b> <b>{{$paid['given']}}</b></td>
						<td class="td-wrap"><b>middle:</b> {{$paid['middle']}}</td>
						<td class="td-wrap"><b>dob:</b> {{$paid['dob']}}</td>
						<td class="td-wrap"><b>mobile:</b> {{$paid['mobile']}}</td>
						<td class="td-wrap"><b>email:</b> {{$paid['email']}}</td>
					</tr>
					<tr style="background-color: #ffffff;">
						<td class="td-wrap"><b>country:</b> {{$paid['country']}}</td>
						<td class="td-wrap"><b>unit:</b> {{$paid['unit']}}</td>
						<td class="td-wrap"><b>number:</b> {{$paid['number']}}</td>
						<td class="td-wrap"><b>street:</b> {{$paid['address']}}</td>
						<td class="td-wrap"><b>suburb:</b> {{$paid['suburb']}}</td>
						<td class="td-wrap"><b>state:</b> {{$paid['state']}}</td>
						<td class="td-wrap"><b>postcode:</b> {{$paid['post_code']}}</td>
						<td class="td-wrap">&nbsp;</td>
					</tr>
				@endif
				</table>
			</td>
		</tr>
		@endif
		
	@if (
		($result['details']['course_name'] == 'RSA') && 
		($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
		($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
	)	
	</table>
	@endif
@endforeach
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)
@else	
</table>
@endif
<table class="table table-bordered table-condensed" style="margin-bottom: 0px !important;">
		<tr>
			<td colspan="3"><h4>NOT PAID ({{count($result['owing'])}})</td>
			<td colspan="2"><h4>{{$result['details']['notpaid_usi']}} usi done - {{$result['details']['notpaid_usi_perc']}}%</h4></td>
			<td colspan="2"><h4>{{$result['details']['notpaid_no_usi']}} (NO NO) - {{$result['details']['notpaid_no_usi_perc']}}%</h4></td>
			<td colspan="3"><h4>Total Amount: ${{$result['details']['notPaidTotal']}}</h4></td>
			@if($result['details']['course_pair'])
			<td></td>
			@endif
		</tr>
</table>
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)	
<table class="table table-striped table-bordered table-condensed" style="margin-bottom: 0px !important;">
@else
<table class="table table-striped table-bordered table-condensed">
@endif
    <tr>
        <td class="span1"><strong>ID</strong></td>
        <td class="span3"><strong>Name</strong></td>
        <td class="span2"><strong>Phone</strong></td>
		@if($result['details']['course_pair'])
		<td class="span1"><strong>Food Hygiene</strong></td>
		@endif
        <td><strong>Notes</strong></td>
        <td><strong>Special Needs</strong></td>
		<td width="4%"><strong>USI</strong></td>
		<td width="6%"><strong>AVETMISS</strong></td>
        <td width="6%"><strong>Paid</strong></td>
        <td width="6%"><strong>Owing</strong></td>
        <td width="7%"><strong>$/Attendance</strong></td>
    </tr>
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)	
</table>
@endif
@foreach($result['owing'] as $key => $owing)
	@if (
		($result['details']['course_name'] == 'RSA') && 
		($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
		($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
	)	
	<table class="table table-bordered table-condensed">
	@endif
		<tr>
			<td class="span1">{{{$owing['order_id']}}}</td>
			<td class="span3"><b>{{{$owing['name']}}}</b></td>
			<td class="span2">{{{$owing['phone']}}}</td>
			@if($result['details']['course_pair'])
			<td class="span1">{{$owing['fh']}}</td>
			@endif
			<td class="td-wrap">{{{$owing['notes']}}}</td>
			<td class="td-wrap">{{{$owing['needs']}}}</td>
			<td width="4%">{{$owing['usi']}}</td>
			<td width="6%">{{$owing['avetmiss']}}</td>
			<td width="6%">{{{$owing['paid']}}}</td>
			<td width="6%">{{{$owing['owing']}}}</td>
			<td width="7%">&nbsp;</td>
		</tr>
		
		@if (
			($result['details']['course_name'] == 'RSA') && 
			($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
			($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
		)
		<tr>
			<td colspan="{{ ($result['details']['course_pair']) ? 11 : 10 }}">
				<table class="table table-bordered table-condensed" style="margin-bottom: 0px !important;">
				@if ($result['details']['state'] == 'VIC')
					<tr>
						<td class="span2 td-wrap"><b>title:</b> {{$owing['title']}}</td>
						<td class="td-wrap"><b>gender:</b> {{$owing['gender']}}</td>
						<td class="td-wrap"><b>surname:</b> {{$owing['surname']}}</td>
						<td class="td-wrap"><b>given:</b> <b>{{$owing['given']}}</b></td>
						<td class="td-wrap"><b>middle:</b> {{$owing['middle']}}</td>
						<td class="td-wrap"><b>dob:</b> {{$owing['dob']}}</td>
						<td class="td-wrap"><b>suburb:</b> {{$owing['suburb']}}</td>
						<td class="td-wrap"><b>postcode:</b> {{$owing['post_code']}}</td>
						<td class="td-wrap"><b>state:</b> {{$owing['state']}}</td>
					</tr>
				@else
					<tr style="background-color: #f9f9f9;">
						<td class="span2 td-wrap"><b>title:</b> {{$owing['title']}}</td>
						<td class="td-wrap"><b>gender:</b> {{$owing['gender']}}</td>
						<td class="td-wrap"><b>surname:</b> {{$owing['surname']}}</td>
						<td class="td-wrap"><b>given:</b> <b>{{$owing['given']}}</b></td>
						<td class="td-wrap"><b>middle:</b> {{$owing['middle']}}</td>
						<td class="td-wrap"><b>dob:</b> {{$owing['dob']}}</td>
						<td class="td-wrap"><b>mobile:</b> {{$owing['mobile']}}</td>
						<td class="td-wrap"><b>email:</b> {{$owing['email']}}</td>
					</tr>
					<tr style="background-color: #ffffff;">
						<td class="td-wrap"><b>country:</b> {{$owing['country']}}</td>
						<td class="td-wrap"><b>unit:</b> {{$owing['unit']}}</td>
						<td class="td-wrap"><b>number:</b> {{$owing['number']}}</td>
						<td class="td-wrap"><b>street:</b> {{$owing['address']}}</td>
						<td class="td-wrap"><b>suburb:</b> {{$owing['suburb']}}</td>
						<td class="td-wrap"><b>state:</b> {{$owing['state']}}</td>
						<td class="td-wrap"><b>postcode:</b> {{$owing['post_code']}}</td>
						<td class="td-wrap">&nbsp;</td>
					</tr>
				@endif
				</table>
			</td>
		</tr>
		@endif
	@if (
		($result['details']['course_name'] == 'RSA') && 
		($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
		($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
	)	
	</table>
	@endif
@endforeach
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)
@else	
</table>
@endif

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
@if ($result['details']['course_name'] == 'RSA' || $result['details']['course_name'] == 'RCG')
<script>
	$(function(){ 
        var url = $(location).attr('href');
        url = url.split('?');
        url = url[0].split('#');
        window.location.href = url[0] + '/csv';
	});
</script>
@endif
@stop
