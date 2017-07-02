<style>

#cbCourseDates {
	font-size:10px;
}

.courseDate {
	font-size:12px;
}

.course-completed {
	background-color:#ccc;
}

.course-full {
	background-color:#FFB3B5;
}

</style>


    <div id="cbCourseDates" title="">
		@foreach ( $courses as $course )
		<div class="cbCourseDate well" id="cbCourseDate{{$course->id}}" title="{{$course->id}}" >
            <h5>{{$course->name}}</h5>
			<div>
			
				<select class="courseDate" id="courseDate{{$course->id}}" data-bind="'event': { 'change': updateSelectedInstance }" >
					<option value="">Please Select a Course Date</option>
					@foreach ( $course->instances as $instance )
						<option class="course-date-option @if ( $instance->isCourseCompleted() ) course-completed @elseif ( $instance->full ) course-full @endif" 
						value="{{$instance->id}}" 
						@if ( $instance->isNextCourse() ) selected="selected" @endif >{{$instance->courseDateDescription}}</option>
					@endforeach
				</select>                                          
                <select class="input-mini" id="courseDateQty{{$course->id}}" title="courseDate{{$course->id}}" data-bind="options: qtyStudents, 'event': { 'change': updateSelectedQty }" ></select>
			    <div class="fees">
				$<input type="text" class="priceOffline input-mini" size="4" id="pOffLine{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updatePriceOffLine }">&nbsp;
                $<input type="text" class="feeRebook input-mini" size="4" id="feeRebook{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateFeeRebook }">
			    </div>
				<div class="notes">
                    <input type="text" class="input-medium' id="notesAdmin{{$course->id}}" name="notesAdmin{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateNotesAdmin }"> Notes (Office)<br />
                    <input type="text" class="input-medium' id="notesClass{{$course->id}}" name="notesClass{{$course->id}}" title="courseDate{{$course->id}}" data-bind="valueUpdate: 'afterkeydown', event: {keyup: updateNotesClass }"> Notes (Class List)
			    </div>
				
            </div>
        </div>
		@endforeach
    </div>


