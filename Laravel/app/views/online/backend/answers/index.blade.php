@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Answers Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>Online Answers Management
		<div class="pull-right">
		@if ($answers->count())
			<a href="/online/questions/?step_id={{ $answers[0]->question->step->id }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back To Questions</a>
		@endif
		</div>
	</h3>
</div>

@if ($answers->count())
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<td colspan="5"><h4>{{ $answers[0]->question->title }}</h4></td>
                <td>
					<a href="/online/answers/create/?question_id={{ $answers[0]->question->id }}" class="btn btn-mini btn-warning">Create Answer</a>
                </td>
			</tr>
			<tr>
				<th>Id</th>
				<th>Description</th>
				<th>Correct</th>
				<th>Order</th>
				<th>Active</th>
				<th width="10%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($answers as $answer)
				<tr>
					<td>{{{ $answer->id }}}</td>
					<td>{{ $answer->description }}</td>
					<td>{{{ $answer->correct == 1 ? 'x' : '' }}}</td>
					<td>{{{ $answer->order }}}</td>
					<td>{{{ $answer->active == 1 ? 'x' : '' }}}</td>
                    <td>
						{{ link_to_route('online.answers.edit', 'Edit', array($answer->id), array('class' => 'btn btn-mini btn-info editCmd')) }}
						<a href="{{ route('delete/answer', $answer->id) }}" class="btn btn-mini btn-danger deleteCmd">Delete</a>
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>

@else
	There are no Online Answers
@endif
</div>	

@stop