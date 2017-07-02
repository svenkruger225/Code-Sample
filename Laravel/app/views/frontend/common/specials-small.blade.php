
@if(count($specials) > 0)
<div class="panel panel-danger panel-specials">
	<div class="panel-heading"><a class="title_link" href="/content/specials/{{$location_name}}"><h5 class="panel-title">SPECIALS {{$location_name}}</h5></a></div>
	<div class="panel-body {{$body_css}}">
		<table class="table table-responsive table-condensed table-striped table-hover">
			@foreach( $specials as $special)
			@if( $display_header && $special->first )
			<tr>
				<td align="left" valign="top"colspan="3" style="background-color: #f4f4f4;">
				<a class="title_link" href="/content/specials/{{$special->location_name}}"><h3>{{$special->location_name}}</h3></a>
				</td>
			</tr>
			@endif
			<tr>
				<td align="left" valign="top"><b>{{$special->course_name}}</b> on {{date('d/m/Y', strtotime($special->course_date))}} at {{$special->time_start}}</td>
				<td align="right" valign="top"><b>${{$special->price_online}}</b></td>
				<td align="right" valign="top"><p><a class="btn" href="{{$special->booking}}"><span style="font-size:18px; font-weight:bold; font-family: 'Economica', sans-serif;">&gt;</span></a></p></td>
			</tr>
			@endforeach
		</table>

	</div>
</div>
<style>
.title_link {
	text-decoration: none !important;
	color: #a94442;
}
</style>
@else
<a href="{{$specials_link}}" title="Special Offers"><img alt="Book Now" src="/images/special-offer.png" style="display: block; margin-left: auto; margin-right: auto; width:200px" /></a>
<script>
    $(".specials-list").addClass("dark_purp");     
	$(".specials-list").removeClass("specials-list");  
</script>
@endif


