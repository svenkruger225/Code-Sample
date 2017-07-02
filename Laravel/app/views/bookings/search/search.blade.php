	<div class="row-fluid">
		<h3>Find Order - Edit</h3>
		<form method="post" action="{{ route('backend.booking.search') }}" class="form-inline">
			{{ Form::token() }}
			<input type="text" name="search_text" value="{{ Input::old('search_text') }}" class="input-medium" placeholder="Search Text"/>
			{{ Form::select('search_type', $search_types, Input::old('search_type'), array('class'=>'input-medium')) }}				
			{{ Form::select('status_id', $statuses, Input::old('status_id'), array('class'=>'input-medium')) }}		
			<input type="text" id="search_date" name="search_date" value="{{ Input::old('search_date') }}" class="input-medium" placeholder="Order Date"/>
			<button type="submit" name="search" class="btn btn-small btn-info"><i class="icon-search"> Search Bookings</i></button>
		</form>
	</div>
