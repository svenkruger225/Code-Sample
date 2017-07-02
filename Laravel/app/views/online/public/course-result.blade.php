@extends('online/public/course')

{{-- Page title --}}
@section('title')
@stop

{{-- Page content --}}
@section('course-content')
	
	@if ( $data->course->questions->count() )
    <div class="panel-heading bg-primary">
		<h4 class="">Results for {{$data->course->questions->count()}} Questions on Course</h4>
    </div>
    <div class="panel panel-default">
        <div id="collapseOne1" class="panel-collapse collapse in">
            <div id="content" class="panel-body">
                <table class="table table-bordered">
				@foreach ( $data->student->current_online_roster->current_course_answers as $history )
					@if ($history->step->questions->count())
					<tr>
						<td class="col-lg-6">
						<div class="pull-left"><b>{{$history->step->name}}</b><br>
						{{$history->answers->count()}} out {{$history->step->questions->count()}} Questions answered
						</div>
						<div class="pull-right">
							@if (!$history->step->IsCompleted())
							<a href="/online/step/{{$history->step->id}}?restartstep=1" class="btn btn-warning btn-md pull-right" role="button">Re-Start Step</a>
							@endif
						</div>
						</td>
						<td class="col-lg-6">
							<div class="progress" style="margin-bottom:0px;">
								@if($history->correct_answers->count())
								<div class="progress-bar progress-bar-success progress-bar-striped" style="width: {{ Utils::GetPercentage($history->correct_answers->count(), $history->step->questions->count()) }}">
									<span>{{$history->correct_answers->count()}} Correct</span>
								</div>
								@endif
								@if($history->wrong_answers->count())
								<div class="progress-bar progress-bar-danger progress-bar-striped" style="width: {{ Utils::GetPercentage($history->wrong_answers->count(), $history->step->questions->count()) }}">
									<span>{{$history->wrong_answers->count()}} Wrong</span>
								</div>
								@endif
								@if($history->step->questions->count() > $history->answers->count())
								<div class="progress-bar progress-bar-default progress-bar-striped" style="width: {{ Utils::GetPercentage($history->step->questions->count() - $history->answers->count(), $history->step->questions->count()) }}">
									<span>{{$history->step->questions->count() - $history->answers->count()}} Not Answered</span>
								</div>
								@endif
								@if($history->to_be_marked_answers->count())
								<div class="progress-bar progress-bar-warning progress-bar-striped" style="width: {{ Utils::GetPercentage($history->to_be_marked_answers->count(), $history->step->questions->count()) }}">
									<span>{{$history->to_be_marked_answers->count()}} To be marked</span>
								</div>
								@endif
							</div>
							<small>
								@if($history->correct_answers->count())
								<span class="text-success">{{$history->correct_answers->count()}} Correct, </span> 
								@endif
								@if($history->wrong_answers->count())
								<span class="text-danger">{{$history->wrong_answers->count()}} Wrong, </span>
								@endif
								@if($history->step->questions->count() > $history->answers->count())
								<span class="text-muted">{{$history->step->questions->count() - $history->answers->count()}} Not Answered, </span>
								@endif
								@if($history->to_be_marked_answers->count())
								<span class="text-warning">{{$history->to_be_marked_answers->count()}} To be marked</span></td>
								@endif
							</small>
						</td>
					</tr>
					@endif
					@if($history->correct_answers->count() < $history->step->questions->count())
					<tr>
						<td class="col-lg-6">Question</td>
						<td class="col-lg-6">Your Answer</td>
					</tr>
					@foreach ( $history->answers as $entry )
						<tr>
							<td>{{$entry->question->title}}</td>
							<td class="@if($entry->result == 1) bg-success @elseif($entry->result == 0) bg-danger  @else bg-warning @endif">{{$entry->answer}}</td>
						</tr>
					@endforeach
					@endif
				@endforeach
					<tr>
						<td colspan="2">
							@if (!$data->course->IsCompleted())
							<a href="/online/course/{{$data->course->id}}?restartcourse=1" class="btn btn-warning btn-md pull-right" role="button">Re-Start course</a>
							@endif
							@if ($data->course->IsCompleted())
							<a href="/online/assessment/{{$data->course->id}}/{{$data->student->current_online_roster->order_id}}" class="btn btn-success btn-md pull-left" role="button">Book Your Assessment</a>
							@endif
						</td>
					</tr>
                </table>
            </div>   
		</div>
    </div>
	@endif
	
	
	
@stop
