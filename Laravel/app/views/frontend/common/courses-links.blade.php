@if (!$booking)
<div class="panel-heading">
	<div class="panel-title"><h3>{{$course_title}}</h3></div>
</div>
@endif
<ul class="list-group">
@foreach ( $courses as $course )
	@if ($booking)
	<li class="list-group-item"><h3>{{$course->name}} Course {{$course->location_name}} ${{$course->price}} 
		<a class="btn btn-small btn-info" href="/bookings/{{$course->location_name}}?course={{$course->id}}" 
		title="{{$course->name}} {{$course->location_name}} ${{$course->price}}">Book Now!</a></h3>
	</li>
	@else
	<li class="list-group-item">
		<a class="list-group-a" href="/content/{{$course->route}}/{{$course->location_name}}" 
		title="{{$course->name}} {{$course->location_name}}"><h3>{{$course->name}} Course {{$course->location_name}}</h3></a>
	</li>
	@endif
@endforeach
</ul>	
<br />		
