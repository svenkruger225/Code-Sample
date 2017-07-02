	<div class="row-fluid">
	<form method="post" action="{{ route('backend.calendar.search2') }}" class="form-inline">
		{{ Form::token() }}
		  From: <input type="text" id="from_date" name="from_date" class="input-small" value="{{ Input::old('from_date') }}" />
		  To: <input type="text" id="to_date" name="to_date" class="input-small" value="{{ Input::old('to_date') }}" />
		  or Single: <input type="text" id="single_date" name="single_date" class="input-small" value="{{ Input::old('single_date') }}" />
			{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-medium')) }}				
			{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-xlarge')) }}				
			{{ Form::select('status_id', $statuses, Input::old('status_id'), array('class'=>'input-medium')) }}		
			<button type="submit" class="btn btn-info">View</button>		
		</form>
	</div>
	<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/calendar.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
