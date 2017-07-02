@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Repeats Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Course Repeats Management</h4></div>
		<div class="span4 pull-right">
			<a href="#" class="btn btn-small btn-primary" data-bind="click: UpdateNoShowsManually"><i class="icon-thumbs-up icon-white"></i> Update NoShows Manually</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="{{ route('backend.courserepeats.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.courserepeats.index', 'class'=>'form-inline')) }}
				{{ Form::select('l_id', $locations, Input::old('l_id'), array('class'=>'input-medium')) }}				
				{{ Form::select('c_id', $courses, Input::old('c_id'), array('class'=>'input-xlarge')) }}				
				{{ Form::select('a_id', array(''=>'All', '1'=>'Active','0'=>'Inactive'), Input::old('a_id'), array('class'=>'input-medium')) }}		
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if (count($courseRepeats) > 0)
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th class="span2">Location</th>
				<th class="span2">Course</th>
				<th class="span1">Mon</th>
				<th class="span1">Tue</th>
				<th class="span1">Wed</th>
				<th class="span1">Thu</th>
				<th class="span1">Fri</th>
				<th class="span1">Sat</th>
				<th class="span1">Sun</th>
				<th class="span1">Monthly</th>
				<th class="span2">start</th>
				<th class="span2">end</th>
				<th class="span1">Max</th>
				<th class="span1">alert</th>
				<th class="span1">auto</th>
				<th class="span2">Start_date</th>
				<th class="span2">End_date</th>
				<th class="span2">Last date</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($courseRepeats as $courseRepeat)
				<tr>
					<td>{{{ $courseRepeat->location->short_name }}}</td>
					<td>{{{ $courseRepeat->course ? $courseRepeat->course->short_name : $courseRepeat->course_id }}}</td>
					<td>{{{ $courseRepeat->monday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->tuesday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->wednesday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->thursday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->friday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->saturday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->sunday == 1 ? 'x' : '' }}}</td>
					<td>{{{ $courseRepeat->monthly == 1 ? 'x' : '' }}}</td>
					<td>{{{ date('h:i A', strtotime($courseRepeat->time_start)) }}}</td>
					<td>{{{ date('h:i A', strtotime($courseRepeat->time_end)) }}}</td>
					<td>{{{ $courseRepeat->maximum_students }}}</td>
					<td>{{{ $courseRepeat->maximum_alert }}}</td>
					<td>{{{ $courseRepeat->maximum_auto == 1 ? 'x' : '' }}}</td>
					<td>{{{ date('d/m/Y', strtotime($courseRepeat->start_date)) }}}</td>
					<td>{{{ $courseRepeat->end_date ? date('d/m/Y', strtotime($courseRepeat->end_date))  : '' }}}</td>
					<td>{{{ date('d/m/Y', strtotime($courseRepeat->last_instance_date)) }}}</td>
					<td>{{{ $courseRepeat->active == 1 ? 'x' : '' }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.courserepeats.destroy', $courseRepeat->id))) }}
                    <td>
						<a href="#" class="btn btn-small btn-primary" data-bind="click: RunManually.bind($data,'{{$courseRepeat->id}}')"><i class="icon-plus-sign icon-white"></i> Run Manually</a>
						{{ link_to_route('backend.courserepeats.edit', 'Edit', array($courseRepeat->id), array('class' => 'btn btn-mini btn-info', 'id'=>"Edit$courseRepeat->id")) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no courseRepeats
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/repeats.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop