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
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<td><strong>ID</strong></td>
			<td><strong>Name</strong></td>
			@if($result['details']['course_pair'])
			<td class="span1"><strong>Food Hygiene</strong></td>
			@endif
			<td><strong>USI</strong></td>
			<td><strong>AVETMISS</strong></td>
			<td><strong>Notes</strong></td>
			<td><strong>Special Needs</strong></td>
			<td width="60"><strong>$/Attendance</strong></td>
		</tr>
        @foreach($result['all'] as $key => $student)
		<tr>
			<td>{{{$student['order_id']}}}</td>
			<td>{{{$student['name']}}}</td>
			@if($result['details']['course_pair'])
			<td class="td-wrap">{{$student['fh']}}</td>
			@endif
			<td class="td-wrap">{{$student['usi']}}</td>
			<td class="td-wrap">{{$student['avetmiss']}}</td>
			<td class="td-wrap">{{$student['notes']}}</td>
			<td class="td-wrap">{{$student['needs']}}</td>
			<td>&nbsp;</td>
		</tr>
        @endforeach
	</table>



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

@stop
