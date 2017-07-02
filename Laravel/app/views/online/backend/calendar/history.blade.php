@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Roster History ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
	<div class="page-header">
		<h3>Online Roster History for {{$roster->customer->name}}</h3>
	</div>
	<div class="tab-pane active" id="tab-general">
		<div class="control-group well">
		
			<div class="accordion" id="rosters_accordion">
			@foreach ($roster->history->modules as $module_id)
				<div class="accordion-group">
					<div class="accordion-heading">
						<a class="accordion-toggle collapsed alert-info" data-toggle="collapse" data-parent="#rosters_accordion" href="#collapse{{$module_id}}">
							<h3>{{ $roster->history->GetCurrentModule($module_id)->name }}</h3>
						</a>
					</div>
					<div id="collapse{{$module_id}}" class="accordion-body collapse" style="height: 0px;">
						<div class="accordion-inner">

							<div class="accordion" id="answers_accordion">
							@foreach ($roster->history->GetModuleSteps($module_id) as $step)
								@if ($step->answers->count())
								<div class="accordion-group">
									<div class="accordion-heading">
									<a class="accordion-toggle collapsed alert-success" data-toggle="collapse" data-parent="#answers_accordion" href="#collapseAnswer{{$step->step->id}}">
									<h5>{{ $step->step->name }} :: Click for Answers</h5>
									</a></div>
									<div id="collapseAnswer{{$step->step->id}}" class="accordion-body collapse" style="height: 0px;">
										<div class="accordion-inner table-responsive">
											<table class="table table-striped table-brostered table-condensed table-hover">
												<thead>
													<tr>
														<tr>
															<th>Question</th>
															<th>Answer</th>
														</tr>
													</tr>
												</thead>
												<tbody>
													@foreach ($step->answers as $answer)
													<tr>
														<td width="50%" class="td-wrap"><span>{{ $answer->question->title }}</span></td>
														<td width="50%" class="td-wrap"><span>{{ $answer->answer }}</span></td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
								@endif
							@endforeach
							</div>


						</div>
					</div>
				</div>
			@endforeach
			</div>

		</div>
			
	</div>
</div>







@stop
