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
			<h3 style="margin:0;">Group Course Booking</h3>
		</div>
		<div class="row-fluid">
			<div class="pull-left"><h4 style="margin:0;">{{{ $result['details']['location'] }}} - {{date('d/m/Y', strtotime($result['details']['course_date']))}}</h4></div>
			<div class="pull-right">Generated: {{date('d/m/Y h:i A')}}</div>
		</div>
	</div>



	<table class="table table-striped table-bordered">
		<tr>
    		<th class='span1'>Instructors:</th>
			<td>{{implode(",", $result['details']['instructors'])}}</td>
		</tr>
		<tr>
    		<th class='span1'>Group:</th>
			<td>{{{ $result['details']['groupname'] }}}</td>
		</tr>
		<tr>
    		<th class='span1'>Contact:</th>
			<td>{{{ $result['details']['groupcontact'] }}}</td>
		</tr>
		<tr>
    		<th class='span1'>Phone:</th>
			<td>{{{ $result['details']['groupphone'] }}}</td>
		</tr>
		<tr>
    		<th class='span1'>Fax:</th>
			<td>{{{ $result['details']['groupfax'] }}}</td>
		</tr>
		<tr>
			<th class='span1'>{{{ $result['details']['groupcourse'] }}}:</th>
			<td>{{{ $result['details']['groupstudents'] }}}</td>
		</tr>
		<tr>
    		<th class='span1'>Notes:</th>
			<td>{{{ $result['details']['groupnotes'] }}}</td>
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
		@if($result['details']['course_pair'])
		<td class="span1"><strong>Food Hygiene</strong></td>
		@endif
		<td><strong>Notes</strong></td>
		<td><strong>Special Needs</strong></td>
		<td width="4%"><strong>USI</strong></td>
		<td width="6%"><strong>AVETMISS</strong></td>
		<td width="7%"><strong>$/Attendance</strong></td>
	</tr>
@if (
	($result['details']['course_name'] == 'RSA') && 
	($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
	($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
)	
</table>
@endif
	
@foreach($result['all'] as $key => $student)
	@if (
		($result['details']['course_name'] == 'RSA') && 
		($result['details']['state'] == 'VIC' || $result['details']['state'] == 'NSW') || 
		($result['details']['course_name'] == 'RCG' && $result['details']['state'] == 'NSW')
	)	
	<table class="table table-bordered table-condensed">
	@endif
		<tr>
			<td class="span1">{{{$student['order_id']}}}</td>
			<td class="span3">{{{$student['name']}}}</td>
			@if($result['details']['course_pair'])
			<td class="td-wrap">{{$student['fh']}}</td>
			@endif
			<td class="td-wrap">{{$student['notes']}}</td>
			<td class="td-wrap">{{$student['needs']}}</td>
			<td width="4%" class="td-wrap">{{$student['usi']}}</td>
			<td width="6%" class="td-wrap">{{$student['avetmiss']}}</td>
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
						<td class="span2 td-wrap"><b>title:</b> {{$student['title']}}</td>
						<td class="td-wrap"><b>gender:</b> {{$student['gender']}}</td>
						<td class="td-wrap"><b>surname:</b> {{$student['surname']}}</td>
						<td class="td-wrap"><b>given:</b> <b>{{$student['given']}}</b></td>
						<td class="td-wrap"><b>middle:</b> {{$student['middle']}}</td>
						<td class="td-wrap"><b>dob:</b> {{$student['dob']}}</td>
						<td class="td-wrap"><b>suburb:</b> {{$student['suburb']}}</td>
						<td class="td-wrap"><b>postcode:</b> {{$student['post_code']}}</td>
						<td class="td-wrap"><b>state:</b> {{$student['state']}}</td>
					</tr>
				@else
					<tr style="background-color: #f9f9f9;">
						<td class="span2 td-wrap"><b>title:</b> {{$student['title']}}</td>
						<td class="td-wrap"><b>gender:</b> {{$student['gender']}}</td>
						<td class="td-wrap"><b>surname:</b> {{$student['surname']}}</td>
						<td class="td-wrap"><b>given:</b> <b>{{$student['given']}}</b></td>
						<td class="td-wrap"><b>middle:</b> {{$student['middle']}}</td>
						<td class="td-wrap"><b>dob:</b> {{$student['dob']}}</td>
						<td class="td-wrap"><b>mobile:</b> {{$student['mobile']}}</td>
						<td class="td-wrap"><b>email:</b> {{$student['email']}}</td>
					</tr>
					<tr style="background-color: #ffffff;">
						<td class="td-wrap"><b>country:</b> {{$student['country']}}</td>
						<td class="td-wrap"><b>unit:</b> {{$student['unit']}}</td>
						<td class="td-wrap"><b>number:</b> {{$student['number']}}</td>
						<td class="td-wrap"><b>street:</b> {{$student['address']}}</td>
						<td class="td-wrap"><b>suburb:</b> {{$student['suburb']}}</td>
						<td class="td-wrap"><b>state:</b> {{$student['state']}}</td>
						<td class="td-wrap"><b>postcode:</b> {{$student['post_code']}}</td>
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
	  <table cellspacing="0" cellpadding="0" width="100%" border="1">
		<tbody>
		  <tr>
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
			<td valign="top" width="25%"><p><strong>_________bottles</strong></p></td>
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
