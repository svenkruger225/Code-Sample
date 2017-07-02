<br />
<div>
@foreach ( $courses as $course )
@if( $course->instances->count() )
	<label>
		<input type="checkbox" id="courseToggle{{$course->id}}" class="courseToggle{{$course->id}}" value="{{$course->id}}" data-bind="'click': displayCoursesInstances" />
		<span> Show Dates: {{$course->name}} - ${{$page->location_id ? $course->priceForLocation($page->location_id)->price_online : $course->priceForLocation(1)->price_online}}</span>
	</label>
@endif
@endforeach
</div>



@foreach ( $courses as $course )
@if( $course->instances->count() )
	<div class="cbCourseDate hide" id="cbCourseDate{{$course->id}}" title="{{$course->id}}">
		<h3>{{$course->name}}</h3>
		<!--<div class="row-fluid">
			<span>{{$course->description}}</span>
		</div> -->
		<div class="row-fluid">
			<a class="btn btn-small pull-left" name="purchaseItemType{{$course->id}}" title="{{$course->id}}" data-bind="'click': selectGiftVoucher"><i class="icon-large icon-gift"></i> <span>Gift Voucher - {{$course->name}}</span></a>
		</div>
		<div class="row-fluid">
			<select class="courseDate pull-left" id="courseDate{{$course->id}}" data-bind="event: { 'change': updateSelectedInstance }" >
				<option value="">Please Select a Course Date</option>
				<option value="gv{{$course->id}}">Gift Voucher (Face to Face)</option>
				@foreach ( $course->instances as $instance )
				<option value="{{$instance->id}}">{{$instance->courseDateDescription}}</option>
				@endforeach
			</select>                       
			<span> &nbsp;&nbsp;&nbsp; QTY: <select class="ipt-mini" id="courseDateQty{{$course->id}}" title="courseDate{{$course->id}}" data-bind="options: qtyStudents, 'event': { 'change': updateSelectedQty.bind($data, '{{$course->id}}') }" ></select></span>
		</div>
	</div>
@endif
@endforeach
