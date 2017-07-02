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
		<h4 class="">Results for {{$data->step->questions->count()}} Questions on Step "{{$data->step->name}}"</h4>
    </div>
    <div class="panel panel-default">
        <div id="collapseOne1" class="panel-collapse collapse in">
            <div id="content" class="panel-body">
                <table class="table table-bordered">
					<tr>
						<td class="col-lg-6">Question</td>
						<td class="col-lg-6">Your Answer</td>
					</tr>
				@foreach ( $data->current_step->answers as $entry )
					<tr class="">
						<td>{{$entry->question->title}}</td>
						<td class="@if($entry->result == 1) bg-success @elseif($entry->result == 0) bg-danger  @else bg-warning @endif">{{$entry->answer}}</td>
					</tr>
				@endforeach
					<tr>
						<td colspan="2">
							<div class="pull-left">
							@if (!$data->step->IsCompleted())
							<a href="/online/step/{{$data->step->id}}?restartstep=1" class="btn btn-warning btn-md pull-left" role="button">Re-Start Step</a>
							@endif
							</div>
							<div class="pull-right">
							@if ($data->step->next_id == 0 && $data->module->next_id)
							<a href="/online/module/{{$data->module->next_id}}" class="btn btn-default btn-md pull-right" role="button">Next Module</a>
							@elseif ($data->step->next_id > 0)
							<a href="/online/step/{{$data->step->next_id}}" class="btn btn-default btn-md pull-right" role="button">Next Step</a>
							@else
							<a href="/online/module/results/{{$data->step->id}}" class="btn btn-default btn-md pull-right" role="button">Course Result</a>
							@endif
							</div>
						</td>
					</tr>
                </table>
            </div>   
		</div>
    </div>
	@endif
	
	
	
@stop
