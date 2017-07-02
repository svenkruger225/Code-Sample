@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Trainers Rosters ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

	<div class="page-header">
		<div class="row-fluid">
			<div class="span12">  
				{{ Form::open(array('method' => 'POST', 'route' => 'backend.reports.trainerrosters', 'class'=>'form-inline')) }}
					{{ Form::token() }}
					From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
					To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
					or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-medium')) }}				
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-medium')) }}				
					{{ Form::select('trainer_id', $trainers, Input::old('trainer_id'), array('class'=>'trainers-rosters-id input-large')) }}		
					{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
				{{ Form::close() }}
			</div> 
		</div>
	</div>


@if (count($result) > 0)

	<table class="table table-striped table-bordered table-hover span8">
		<thead>
			<tr>
        		<th class="span1"></th>
        		<th class="span2">Location</th>
				<th class="span3">Trainer</th>
				<th class="span2">Course Type</th>
        		<th class="span1">Course Date</th>
        		<th class="span1">Course Time</th>
				<th class="span1"></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($result as $key => $roster)
				<tr>
					<td><span>{{{ $roster['type'] }}}</span></td>
					<td><span>{{{ $roster['location'] }}}</span></td>
					<td><span>{{{ $roster['trainer'] }}}</span></td>
					<td><span>{{{ $roster['course_type'] }}}</span></td>
					<td><span>{{{ $roster['course_date'] }}}</span></td>
					<td><span>{{{ $roster['course_time'] }}}</span></td>
                    <td></td>
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no rosters
@endif



</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/financials.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>	
@stop