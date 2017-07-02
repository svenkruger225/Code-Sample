@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Bundles Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Course Bundles Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.coursebundles.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.coursebundles.index', 'class'=>'form-inline')) }}
				{{ Form::select('l_id', $locations, Input::old('l_id'), array('id'=>'locationList','class'=>'input-medium')) }}
				{{ Form::select('c_id', $courses, Input::old('c_id'), array('class'=>'input-xlarge')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if ($coursebundles->count())
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th class="span1">Id</th>
				<th class="span2">Location</th>
				<th class="span4">Bundles</th>
				<th class="span2">Date_from</th>
				<th class="span2">Date_to</th>
				<th class="span1">Students Min</th>
				<th class="span2">Total Online</th>
				<th class="span2">Total Offline</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($coursebundles as $courseBundle)
				<tr data-bind="'click': EditBundle.bind($data, 'Edit{{$courseBundle->id}}')">
					<td>{{{ $courseBundle->id }}}</td>
					<td>{{{ $courseBundle->location ? $courseBundle->location->name : $courseBundle->location_id }}}</td>
					<td>
						<table class="table table-striped table-bordered table-condensed">
						@foreach ($courseBundle->bundles as $bundle)
							<tr><td width="50%">{{{ $bundle->name }}}</td><td width="25%">${{{ $bundle->pivot->price_online }}}</td><td width="25%">${{{ $bundle->pivot->price_offline }}}</td></tr>
						@endforeach
						</table>
					</td>
					<td>{{{ $courseBundle->date_from != '0000-00-00' ? $courseBundle->date_from  : ''}}}</td>
					<td>{{{ $courseBundle->date_to!= '0000-00-00' ? $courseBundle->date_to  : '' }}}</td>
					<td>{{{ $courseBundle->students_min }}}</td>
					<td>${{{ $courseBundle->total_online }}}</td>
					<td>${{{ $courseBundle->total_offline }}}</td>
					<td>{{{ $courseBundle->active == '1' ? 'x' : '' }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.coursebundles.destroy', $courseBundle->id))) }}
                    <td>
						{{ link_to_route('backend.coursebundles.edit', 'Edit', array($courseBundle->id), array('class' => 'btn btn-mini btn-info', 'id'=>"Edit$courseBundle->id")) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd', 'data-bind'=>'click: DeleteBundle, clickBubble: false')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>

@include('backend.common.confirm')

@else
	There are no coursebundles
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/bundles.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop