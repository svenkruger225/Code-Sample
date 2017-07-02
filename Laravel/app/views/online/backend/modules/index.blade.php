@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Modules Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>Online Modules Management
		<div class="pull-right">
            {{ Form::open(array('method' => 'GET', 'route' => array('online.modules.create'))) }}
			Select Module: {{ Form::select('course_id', $courses, Input::old('course_id'), array('id'=>'course_id', 'class'=>'input-xlarge')) }}
			<button type="submit" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create Module</button>
            {{ Form::close() }}
		</div>
	</h3>
</div>

@if ($modules->count())
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<td colspan="5"><h4>{{ $modules[0]->course->name }}</h4></td>
                <td>
					<a href="/online/modules/create/?course_id={{ $modules[0]->course->id }}" class="btn btn-small btn-warning">Create Module</a>
                </td>
			</tr>
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Order</th>
				<th>Steps</th>
				<th>Active</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($modules as $module)
				<tr>
					<td>{{{ $module->id }}}</td>
					<td>{{{ $module->name }}}</td>
					<td>{{{ $module->order }}}</td>
					<td>{{{ $module->steps->count() }}}</td>
					<td>{{{ $module->active == 1 ? 'x' : '' }}}</td>
                    <td>
						{{ link_to_route('online.modules.edit', 'Edit', array($module->id), array('class' => 'btn btn-mini btn-info editCmd')) }}
						<a href="{{ route('delete/module', $module->id) }}" class="btn btn-mini btn-danger deleteCmd">Delete</a>
						<a href="/online/steps/create/?module_id={{ $module->id }}" class="btn btn-mini btn-warning">Create Step</a>
						@if ($module->steps->count())
							<a href="/online/steps/?module_id={{ $module->id }}" class="btn btn-mini btn-warning">View Steps</a>
						@endif
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no Online Modules
@endif
</div>	

@stop