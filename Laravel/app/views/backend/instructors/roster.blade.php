@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Instructors Roster Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Instructors Roster Management</h4></div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.instructors.roster', 'class'=>'form-inline')) }}
				Month: {{ Form::select('roster_month', $months, Input::old('roster_month'), array('class'=>'input-large')) }} &nbsp;&nbsp;	
				Year: {{ Form::select('roster_year', $years, Input::old('roster_year'), array('class'=>'input-medium')) }} &nbsp;&nbsp;	
				{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-medium')) }}				
				{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-xlarge')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>
<div class="well container-fluid">
	<div id="calanderDay">
		@foreach ($result as $date =>$parents)
		<h4 class="titleH1">{{{ date("l d F Y", strtotime($date)) }}}</h4>
			@foreach ($parents as $parent =>$locations)
			<h4 class="titleH3">{{{ $parent }}}</h4>
				@foreach ($locations as $name =>$location)
				<h5 class="titleH4">{{{ $name }}}</h5>
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="span1">Type</th>
							<th class="span4">Course</th>
							<th class="span2">Start</th>
							<th class="span2">end</th>
							<th class="span3">Instructors</th>
							<th class="span3">Select</th>
							<th class="span1"></th>
						</tr>
					</thead>

					<tbody>
						@foreach ($location as $id => $class)
							<tr class="{{$class['class']}}" id="row{{$id}}">
								<td>{{{ $class['type'] }}}</td>
								<td>{{{ $class['course'] }}}</td>
								<td>{{{ $class['time_start'] }}}</td>
								<td>{{{ $class['time_end'] }}}</td>
								<td><span id="instructors{{$id}}">{{ $class['instructors'] }}</span></td>
								<td>
									{{ Form::select('instructor[]', $class['trainerslist'], $class['trainers'], array('class'=>'instructors' . $id, 'multiple', 'cols'=>'20','rows'=>'5')) }}</td>
								<td>
									<a href="#" class="btn btn-small btn-primary" data-bind="click: updateClassInstructors.bind($data, '{{$id}}', '{{$class['type']}}')" title="Update Class"><i class="icon-edit icon-white"></i> Update Class</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				@endforeach
			@endforeach
		@endforeach
	</div>
</div>
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/instructors.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop