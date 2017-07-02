<style>

#cbCourseDates {
	font-size:10px;
}

.courseDate {
	font-size:12px;
}

.course-started {
	background-color:#f0f0f0;
}

.course-completed {
	background-color:#ccc;
}

.course-full {
	background-color:#FFB3B5;
}

.input-tiny {
	width: 52px !important;
}
.input-100 {
	width: 95% !important;
}

</style>


    <div id="coursesDetails">
		<div id="cbCourseDates" title="">
			@foreach ( $courses as $course )
			<div class="cbCourseDate well well-small" id="cbCourseDate{{$course->id}}" title="{{$course->id}}" >
				<h6>{{$course->name}} 
				<!--<span class="pull-right">$<input type="text" class="discount input-tiny" placeholder="discount" size="5" id="discount{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateDiscount }"></span>&nbsp;
				<span class="pull-right">$<input type="text" class="feeRebook input-tiny" placeholder="fee" size="4" id="feeRebook{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateFeeRebook }"></span>-->
				</h6>
				<div>
					<select class="courseDate span7" id="courseDate{{$course->id}}" data-bind="'event': { 'change': updateSelectedInstance }" >
						<option value="">Please Select a Course Date</option>
						<option value="gv{{$course->id}}">Gift Voucher (Face to Face)</option>
						@foreach ( $course->instances as $instance )
							<option class="course-date-option @if ( $instance->isCourseCompleted() ) course-completed @elseif ( $instance->isCourseStarted() ) course-started @elseif ( $instance->full ) course-full @endif" value="{{$instance->id}}" >{{$instance->courseDateDescription}}</option>
						@endforeach
					</select>                                          
					<select class="input-mini" id="courseDateQty{{$course->id}}" title="courseDate{{$course->id}}" data-bind="options: qtyStudents, 'event': { 'change': updateSelectedQty.bind($data, '{{$course->id}}') }" ></select>
					<div class="notes">
						<input type="text" class="input-100" id="notesAdmin{{$course->id}}" placeholder="notes office" name="notesAdmin{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateNotesAdmin }"><br />
						<input type="text" class="input-100" id="notesClass{{$course->id}}" placeholder="notes class list" name="notesClass{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateNotesClass }">
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>

