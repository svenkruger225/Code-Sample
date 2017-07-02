@extends('frontend/layouts/default')

@section('content')


<div class="row-fluid">
	<div class="span9">
		<div class="bs-docs-example">
			<div class="carousel slide" id="myCarousel">
				<ol class="carousel-indicators"><li class="active" data-slide-to="0" data-target="#myCarousel">&nbsp;</li></ol>
				<div class="carousel-inner">
					<div class="item active"><img alt="" src="/images/headers/coffee.jpg" width="100%" />
						<div class="carousel-caption"><h4>Master the art of coffee with this hands-on, Accredited Barista Course and work in any caf&eacute; in Australia! Sydney&rsquo;s first and best barista training course.</h4></div>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="halfpage-right">
				{{$result->body}}
			</div>
		</div>
	</div>
	<div class="span4 specials-list">
		{{$specials}}
	</div>
	<br />
</div>

@stop
