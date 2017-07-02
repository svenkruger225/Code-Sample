@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Questions Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>Online Questions Management
		<div class="pull-right">
            {{ Form::open(array('method' => 'GET', 'route' => array('online.questions.create'))) }}
			Select Step: {{ Form::select('step_id', $steps, Input::old('step_id'), array('id'=>'step_id', 'class'=>'input-xlarge')) }}
			<button type="submit" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create Question</button>
			@if ($questions->count())
			<a href="/online/steps/?module_id={{ $questions[0]->step->module->id }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back To Steps</a>
            @endif
			{{ Form::close() }}
		</div>
	</h3>
</div>

@if ($questions->count())
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<td colspan="7"><h4>{{ $questions[0]->step->name }}</h4></td>
                <td>
					<a href="/online/questions/create/?step_id={{ $questions[0]->step->id }}" class="btn btn-small btn-warning">Create Question</a>
                </td>
			</tr>
			<tr>
				<th>Id</th>
				<th>Title</th>
				<th>Type</th>
				<th>Weight</th>
				<th>Order</th>
				<th>Answers</th>
				<th>Active</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($questions as $question)
				<tr>
					<td>{{{ $question->id }}}</td>
					<td>{{ $question->title }}</td>
					<td>{{{ $question->type }}}</td>
					<td>{{{ $question->weight }}}</td>
					<td>{{{ $question->order }}}</td>
					<td>{{{ $question->answers->count() }}}</td>
					<td>{{{ $question->active == 1 ? 'x' : '' }}}</td>
                    <td>
						{{ link_to_route('online.questions.edit', 'Edit', array($question->id), array('class' => 'btn btn-mini btn-info editCmd')) }}
						<a href="{{ route('delete/question', $question->id) }}" class="btn btn-mini btn-danger deleteCmd">Delete</a>
						<a href="/online/answers/create/?question_id={{ $question->id }}" class="btn btn-mini btn-warning">Create Answer</a>
						@if ($question->answers->count())
							<a href="/online/answers/?question_id={{ $question->id }}" class="btn btn-mini btn-warning">View Answers</a>
						@endif
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no Online Questions
@endif
</div>	

@stop