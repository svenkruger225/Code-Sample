<div class="online-panel-body">
	<div class="table-responsive">
		<table class="table table-striped table-condensed">
			@foreach ( $locations as $location )
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
					<select class="courseDate pull-left" id="courseDate{{$location->id}}" data-bind="event: { 'change': updateOnlineFaceToFaceSelectedInstance }" >
						<option value="">Please Select a Course Date</option>
						@foreach ( $location->course->instances as $instance )
						<option value="{{$instance->id}}">{{$instance->courseDateDescription}}</option>
						@endforeach
					</select>  
				</td>
			</tr>
			@endforeach
		</table>
	</div>
</div>
