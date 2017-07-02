    <div id="coursesDetails">
		<div id="cbCourseDates" title="">
			@foreach ( $courses as $course )
			<div class="cbCourseDate well well-small" id="cbCourseDate{{$course->id}}" title="{{$course->id}}" >
				<div class="row offset1">
					<h6>{{$course->name}}</h6>
					<div class="row">
						<div class="span2">Date:</div>
						<div class="span4">
						<input type="hidden" id="courseName{{$course->id}}" value="{{$course->name}}" />
						<input class="course_date input-small" type="text" id="courseDate{{$course->id}}" placeholder="course date" title="{{$course->id}}" data-bind="'event': { 'change': updateSelectedInstance }" />
						</div>
						<div class="span2">Students:</div>
						<div class="span4"><select class="input-small" id="courseDateQty{{$course->id}}" title="{{$course->id}}" data-bind="options: qtyStudents, 'event': { 'change': updateSelectedInstance }" ></select></div>
					</div>
					<div class="row">
						<div class="span2">Start:</div>
						<div class="span4">
						{{ Form::select('course_start[]', $course_times, $course->time_start, array('class'=>'input-small', 'id'=>"time_start$course->id",'title'=>"$course->id", 'data-bind'=>"'event': { 'change': updateSelectedInstance }")) }}				
						</div>
						<div class="span2">End:</div>
						<div class="span4">
						{{ Form::select('course_end[]', $course_times, $course->time_end, array('class'=>'input-small', 'id'=>"time_end$course->id",'title'=>"$course->id", 'data-bind'=>"'event': { 'change': updateSelectedInstance }")) }}				
						</div>
					</div>
					<div class="row">
						<div class="span2">Fee:</div>
						<div class="span4">
							<input type="text" class="feeRebook input-small" placeholder="fee" size="4" id="feeRebook{{$course->id}}" title="cbCourseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateFeeRebook }">
						</div>
						<div class="span2">Discount:</div>
						<div class="span4">
							<input type="text" class="discount input-small" placeholder="discount" size="5" id="discount{{$course->id}}" title="cbCourseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateDiscount }">
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>

