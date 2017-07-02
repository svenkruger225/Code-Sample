
@foreach(array_chunk($data->courses->toArray(), 4) as $coursesRow)
    <div id="content" class="row">
        @foreach($coursesRow as $course)
            <div class="col-md-3">
                <div class="course-block">
					<div class="row-fluid">
						<h2>{{$course['name']}}</h2>
					</div>
					<div class="row-fluid">
						<div class="course-info">{{$course['description']}}</div>
					</div>
					<div class="row-fluid">
					<div class="course-buttons">
						<div class="row">
							<div class="col-sm-6">
								<p>&nbsp;</p>
							</div>
							<div class="col-sm-6">
								<button class="btn btn-warning pull-right" role="button" data-toggle="modal" data-target="#classDatesModal{{$course['id']}}">Class dates &raquo;</button>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-sm-6">
								@if ($data->student && $data->student->IsEnrolled($course['id']))
									<a class="btn btn-success" href="/online/course/{{$course['short_name']}}" role="button">&nbsp;Resume &raquo;&nbsp;</a>
								@else
									<a class="btn btn-primary" href="/online/register" role="button">&nbsp;Register &raquo; &nbsp;</a>
								@endif
							</div>
							<div class="col-sm-6">
								<button class="btn btn-info pull-right" role="button" data-toggle="modal" data-target="#moreInfoModal{{$course['id']}}">&nbsp;More Info &raquo; &nbsp;</a>
							</div>
						</div>
					</div>
					</div>
                </div><!-- /.course-block -->
            </div><!-- /.col-lg-3 -->
        @endforeach
    </div>
@endforeach

@foreach($data->courses as $course)

<div class="modal fade" id="classDatesModal{{$course->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">{{$course->name}} Class dates</h4>
		<p>Please note this is for reference only, you will select an available class after completing the online portion of the course</p>
      </div>
      <div class="modal-body">
			<div class="online-panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-condensed">
						@foreach ( $course->locations as $location )
						<tr class="row cbCourseDate" id="cbCourseDate{{$location->id}}" title="{{$location->id}}">
							<td class="col-md-4">
								<address>
									<b>{{$location->name}}</b><br>
									<small>
										<span>{{$location->address}}</span><br>
										<span>{{$location->city}} {{$location->state}}</span>
									</small>
								</address>  
							</td>
							<td class="col-md-8">
								<select class="form-control">
									<option value="">Click to browse available dates</option>
									@foreach ( $location->instances as $instance )
									<option value="{{$instance->id}}">{{$instance->courseDateDescription}}</option>
									@endforeach
								</select>  
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="moreInfoModal{{$course->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">{{$course->name}} More Information</h4>
      </div>
      <div class="modal-body">
			{{$course->more_info}}
      </div>
    </div>
  </div>
</div>


@endforeach

