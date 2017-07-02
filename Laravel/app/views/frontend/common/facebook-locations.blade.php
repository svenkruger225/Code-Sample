<div class="span12 text-center">
@foreach ( $courses as $index => $course )
	@if ($index == 0)
	<div class="row-fluid">
	@endif
		<div class="span33 voucher-button">
			<voucher class="icon">
				<a href="/content/{{$course->route}}/{{$course->location_name}}" title="{{$course->name}} in {{$course->location_name}}">
					<img class="icon1" src="/images/icons/{{$course->short_name}}3.png" />
					<img class="icon2" src="/images/icons/{{$course->short_name}}4.png" style="display:none;" />
				</a>
				<voucher_caption>{{$course->name}}</voucher_caption>
			</voucher>		
		</div>
	@if (($index + 1) % 3 == 0)
	</div>
	<div class="row-fluid">
	@endif
	@if (($index + 1) >= count($courses))
	</div>
	@endif
@endforeach
</div>