@extends('online/layouts/defult')

{{-- Page title --}}
@section('title')
Booking ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

	<div class="row-fluid">
	
		<div class="{{$result}}">
			<div class="page-header">
				<h3>Booking cancelled by user</h3>
			</div>
			<div class="row-fluid">
			<div class="span5">{{ $message }}</div>
			<div class="span5">			
				<div class="input-prepend">
					<a target="_blank" href="https://www.facebook.com/CoffeeRSAschool" title="Visit us" style="display: inline-block;vertical-align: top;"><img src="/images/face.png" / ></a>
					<div class="fb-like" data-href="https://www.facebook.com/CoffeeRSAschool" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
				</div>
			</div>
			
			</div>
		
			<p> &nbsp;</p>
			<p> &nbsp;</p>
		</div>

	</div><!--/row-->

	
@stop
