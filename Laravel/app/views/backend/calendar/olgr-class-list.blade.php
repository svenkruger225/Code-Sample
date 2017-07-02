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
			<div class="pull-left"><h4 style="margin:0;">{{{ $result['details']['location'] }}} - {{date('d/m/Y', strtotime($result['details']['course_date']))}}</h4></div>
			<div class="pull-right">Generated: {{date('d/m/Y h:i A')}}</div>
		</div>
		<div class="row-fluid">
			<div class="pull-left"><h4>{{{ $result['details']['name'] }}} ({{date('h:i A', strtotime($result['details']['time_start']))}} to {{date('h:i A', strtotime($result['details']['time_end']))}}) - Instructors: {{implode(",", $result['details']['instructors'])}}</h4></div>
		</div>
	</div>
	<table class="table table-bordered table-condensed">
	@if ($result['details']['state'] == 'VIC')
        @foreach($result['all'] as $key => $student)
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
		<tr>
			<td colspan="9" style="height: 20px;"></td>
		</tr>
        @endforeach
	@else
        @foreach($result['all'] as $key => $student)
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
		<tr>
			<td colspan="8" style="background-color: #ffffff; height: 30px;"></td>
		</tr>
        @endforeach
    @endif
	</table>
	
</div>
<script>
	$(function(){ 
        var url = $(location).attr('href');
        url = url.split('?');
        url = url[0].split('#');
        window.location.href = url[0] + '/csv';
	});
</script>

@stop
