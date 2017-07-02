@extends('online/public/course')

{{-- Page title --}}
@section('title')
@stop

{{-- Page content --}}
@section('course-content')
	@if ( !empty($data->step->description) )
	<div>
		<div>{{$data->step->description}}</div>
		@if ($data->step->previous_id == 0 && $data->module->previous_id)
		<a href="/online/module/{{$data->module->previous_id}}" class="btn btn-default btn-md pull-left" role="button">Previous Module</a>
		@elseif ($data->step->previous_id > 0)
		<a href="/online/step/{{$data->step->previous_id}}" class="btn btn-default btn-md pull-left" role="button">Previous Step</a>
		@endif
		@if ($data->step->next_id == 0 && $data->module->next_id)
		<a href="/online/module/{{$data->module->next_id}}" class="btn btn-default btn-md pull-right" role="button">Next Module</a>
		@elseif ($data->step->next_id > 0)
		<a href="/online/step/{{$data->step->next_id}}" class="btn btn-default btn-md pull-right" role="button">Next Step</a>
		@endif
	</div>
	@endif
	
	@if ( $data->step->questions->count() )
    <div class="panel-heading bg-primary">
		<h4 class="">Question {{$data->question->index}} of {{$data->step->questions->count()}}</h4>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">{{$data->question->title}}</h4>
        </div>
        <div id="collapseOne1" class="panel-collapse collapse in">
            <div id="content" class="panel-body">
                <table class="table table-bordered">
				<form id="qaForm" method="post" action="/online/answer"  >
				<!-- CSRF Token -->
				{{ Form::token() }}
				<input type="hidden" name="customer_id" value="{{$student->id}}">
				<input type="hidden" name="question_id" value="{{$data->question->id}}">
				<tr><td colspan="2">
					<h4 class="panel-title">Answers: </h4>
					<p><b>
					@if ($data->question->type == 'multiple')
						You may select more than one.
					@elseif ($data->question->type == 'single')
						Please select one.
					@elseif ($data->question->type == 'text')
						Please enter your answer.
					@elseif ($data->question->type == 'upload')
						Please Upload your answer  as an audio file.
					@endif
					</b></p>
				</td></tr>
				@foreach ( $data->question->answers as $answer )
					@if ($data->question->type == 'multiple')
					<tr>
						<td class="col-md-1 @if($data->goto_next_question && $answer->correct) bg-success @endif">
						<input type="checkbox" name="answer" value="{{$answer->id}}" data-bind="initCheckbox: answer, checked: answer" @if($answer->id == $data->current_answer) checked @endif>
						</td>
					@elseif ($data->question->type == 'single')
					<tr>
						<td class="col-md-1 @if($data->goto_next_question && $answer->correct) bg-success @endif">
						<input type="radio" name="answer" value="{{$answer->id}}" data-bind="initRadio: answer, checked: answer" @if($answer->id == $data->current_answer) checked @endif>
						</td>
					@elseif ($data->question->type == 'text')
					<tr>
						<td class="col-md-1 @if($data->goto_next_question && $answer->correct) bg-success @endif">
						<input type="text" name="answer" value="" data-bind="value: answer">
						</td>
					@elseif ($data->question->type == 'upload')
					<tr>
						<td class="col-md-1 @if($data->goto_next_question && $answer->correct) bg-success @endif">
						<input type="file" name="answer" data-bind="value: answer">
						</td>
					@endif
						<td @if($data->goto_next_question && $answer->correct) class='bg-success' @endif>{{strip_tags($answer->description)}}</td>
					</tr>
				@endforeach
					<tr>
						<td colspan="2" @if($data->goto_next_question && $data->correct) class='bg-success' @elseif($data->goto_next_question && !$data->correct) class='bg-danger' @endif>
							@if($data->goto_next_question) 
							<h4 class="pull-left">{{$data->message}}</h4>
							@endif

							@if($data->still_questions && $data->goto_next_question)
								<a href="/online/step/{{$data->step->id}}?nq={{$data->question->next->id}}" 
								class="btn btn-default btn-md pull-right" 
								role="button">Next Question</a>
							@elseif(!$data->goto_next_question)
								<button data-bind="click: checkAnswer"
								class="btn btn-default btn-md pull-right" 
								role="button">Submit Answer</button>
							@else
								<a href="/online/step/results/{{$data->step->id}}" class="btn btn-default btn-md pull-right" role="button">View Step Results</a>

								@if ($data->step->next_id == 0 && $data->module->next_id)
								<a href="/online/module/{{$data->module->next_id}}" class="btn btn-default btn-md pull-right" role="button">Next Module</a>
								<a href="/online/module/results/{{$data->step->id}}" class="btn btn-default btn-md pull-right" role="button">View Module Results</a>
								@elseif ($data->step->next_id > 0)
								<a href="/online/step/{{$data->step->next_id}}" class="btn btn-default btn-md pull-right" role="button">Next Step</a>
								@endif
							@endif
						</td>
					</tr>
					</form>
                </table>
            </div>   
		</div>
    </div>
	@endif
	
	
	
@stop
