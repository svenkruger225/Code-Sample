@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Export MYOB ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

	<div class="page-header">
		<div class="row-fluid">
			<div class="span12">  
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.avetmiss.export', 'class'=>'form-inline')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" /> &nbsp;&nbsp;
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" /> &nbsp;&nbsp;
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" /> &nbsp;&nbsp;
					Report Type: {{ Form::select('report_type', $types, Input::old('report_type'), array('class'=>'input-medium')) }}		
					{{ Form::submit('Export', array('class' => 'btn btn-info loadCmd')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>

@if (count($avetmiss) > 0)

	<table class="table table-striped table-bordered table-hover table-condensed span10">
		<thead>
			<tr>
        		<th class="span1">File Name</th>
        		<th class="span1">File Size</th>
        		<th class="span1">File Date</th>
				<th class="span1"></th>
			</tr>
		</thead>
		<tbody>
		@foreach ($avetmiss as $item)
			<tr>
        		<td>{{$item['name']}}</td>
        		<td>{{$item['size']}}</td>
        		<td>{{$item['date']}}</td>
				<td><a href="/backend/avetmiss/downloadfile?path={{$item['path']}}" target="_blank" class="btn btn-small btn-primary"> Download</a>
				<a href="/backend/avetmiss/removefile?path={{$item['path']}}" class="btn btn-small btn-danger"> Remove</a></td>
			</tr>
		@endforeach
			<tr><td colspan="4"> </td></tr>
			<tr><td colspan="4">total = {{count($avetmiss)}} </td></tr>
			<tr><td colspan="4">time total = {{Utils::getmicrotime() - $start}}</td></tr>
		</tbody>
	</table>

@else
	There are no files
@endif



</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/financials.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop