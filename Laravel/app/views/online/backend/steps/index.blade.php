@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Steps Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>Online Steps Management
		<div class="pull-right">
            {{ Form::open(array('method' => 'GET', 'route' => array('online.steps.create'))) }}
			Select Module: {{ Form::select('module_id', $modules, Input::old('module_id'), array('id'=>'module_id', 'class'=>'input-xlarge')) }}
			<button type="submit" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create Step</button>
			@if ($steps->count())
			<a href="/online/modules/?course_id={{ $steps[0]->module->course->id }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back To Modules</a>
            @endif
            {{ Form::close() }}
		</div>
	</h3>
</div>

@if ($steps->count())
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<td colspan="5"><h4>{{ $steps[0]->module->name }}</h4></td>
                <td>
					<a href="/online/steps/create/?module_id={{ $steps[0]->module->id }}" class="btn btn-small btn-warning">Create Step</a>
                </td>
			</tr>
			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Order</th>
				<th>Questions</th>
				<th>Active</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($steps as $step)
				<tr>
					<td>{{{ $step->id }}}</td>
					<td>{{{ $step->name }}}</td>
					<td>{{{ $step->order }}}</td>
					<td>{{{ $step->questions->count() }}}</td>
					<td>{{{ $step->active == 1 ? 'x' : '' }}}</td>
                    <td>
						{{ link_to_route('online.steps.edit', 'Edit', array($step->id), array('class' => 'btn btn-mini btn-info editCmd')) }}
						<a href="{{ route('delete/step', $step->id) }}" class="btn btn-mini btn-danger deleteCmd">Delete</a>
						<a href="/online/questions/create/?step_id={{ $step->id }}" class="btn btn-mini btn-warning">Create Question</a>
						@if ($step->questions->count())
							<a href="/online/questions/?step_id={{ $step->id }}" class="btn btn-mini btn-warning">View Questions</a>
						@endif
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no Online Steps
@endif
</div>	

@stop