<div id="busyindicator"></div>
<div id="content">

<!-- Search HQ AdWords -->
<script type='text/javascript'>

var _fxcmd = _fxcmd || [];
_fxcmd.sid = 'e56e8eea20a504ce71a03ee4318d8067';
(function () {
	var _pzfx = document['createElement']('script');
	_pzfx.type = 'text/javascript';
	_pzfx.async = true;
	_pzfx.src = 'http://static.w3t.cn/fx/1/1/fx.js';
	var sc = document.getElementsByTagName('script')[0];
	sc.parentNode.insertBefore(_pzfx, sc);
})();

</script>


<!-- Facebook Conversion Code for Facebook tracking pixel -->
<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6017669334803', {'value':'0.01','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6017669334803&amp;cd[value]=0.01&amp;cd[currency]=USD&amp;noscript=1" /></noscript>



	<!-- INSTAGRAM CODE -->
	<style>
		.ig-b- { display: inline-block; }
		.ig-b- img { visibility: hidden; }
		.ig-b-:hover { background-position: 0 -60px; } .ig-b-:active { background-position: 0 -120px; }
		.ig-b-v-24 { width: 137px; height: 24px; background: url(//badges.instagram.com/static/images/ig-badge-view-sprite-24.png) no-repeat 0 0; }
		@media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
		.ig-b-v-24 { background-image: url(//badges.instagram.com/static/images/ig-badge-view-sprite-24@2x.png); background-size: 160px 178px; } }
	</style>

	<div class="row-fluid">
	
		{{$page->content_top }}

		<!-- GOOGLE ADS CODE -->
		<script type="text/javascript">
		<!-- only does the google ads code if we have just received the booking -->
		@if ($first_visit) 
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-9324019-1']);
			_gaq.push(['_trackPageview']);
			_gaq.push(['_addTrans',
			'{{$order->id}}',           // transaction ID - required
			'{{$order->current_payment_method}}',  // affiliation or store name
			'{{$order->total}}',          // total - required
			'',           // tax
			'',              // shipping
			'',       // city
			'',     // state or province
			'AU'             // country
			]);

			// add item might be called for every item in the shopping cart
			// where your ecommerce engine loops through each item in the cart and
			// prints out _addItem for each
			@foreach($order->items as $item)
				_gaq.push(['_addItem',
				'{{$order->id}}',           // transaction ID - required
	
				@if ($item->item_type_id == '1')
					'{{$item->course_instance_id}}',           // SKU/code - required
					'{{$item->instance->course->name}}',        // product name
					'{{$item->instance->location->name}}',   // category or variation
				@else
					'{{$item->vouchers_ids}}',           // SKU/code - required
					'{{$item->description}}',        // product name
					'',   // category or variation
				@endif
	
				'{{$item->price}}',          // unit price - required
				'{{$item->qty}}'               // quantity - required
				]);
			@endforeach

			_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();
		@endif
		</script>

	
		<div class="{{$result}}">
			<div class="row-fluid">
				<div class="span6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="panel-title"><h3>ENROLMENT DATA</h3></div>
						</div>
						<div class="panel-body" style="color:#333;">
							@if ($first_visit) 
							<div class="row-fluid">
								<div class="page-header"><h4>Thank You for the booking</h4></div>
								<div class="span12">
									{{ $message }}
									<hr />
								</div>
							</div>
							@endif	
							@if (count($order->rosters) > 0)
							<div class="row-fluid">
								<div class="page-header">
								<h4>Students must complete their Enrolment Data before attending the course. 
								Once you have completed this it will be indicated below next to your name</h4></div>
								<div class="span12">
									<input 
									type="hidden" 
									value='[@foreach($order->rosters as $key => $roster){"student_id": "{{$student_id}}", "roster_id": "{{$roster->id}}", "customer_id": "{{$roster->customer->id}}", "name": "{{{$roster->customer->name}}}", "usi_verified": "{{$roster->customer->usi_verified}}", "avetmiss_done": "{{$roster->customer->avetmiss_done}}"} @if (($key + 1) < count($order->rosters)),@endif @endforeach]' 
									data-bind="initObservableArray: student_list, propNames: '[&quot;student_id&quot;,&quot;roster_id&quot;,&quot;customer_id&quot;,&quot;name&quot;,&quot;usi_verified&quot;,&quot;avetmiss_done&quot;]'" />							
									<div class="pull-left" data-bind="foreach: student_list()">
										<!-- ko if: typeof($data.student_id) == 'undefined' || $data.student_id() == '' || $data.student_id() == $data.customer_id() -->
											<!-- ko if: $data.usi_verified() == '1' || $data.avetmiss_done() == '1' -->
											<p class="btn btn-small btn-warning pull-left" disabled><span data-bind="html: $data.name"></span></p>&nbsp;&nbsp; <span class="alert-success"> Enrolment data completed!</span><br><br>
											<!-- /ko -->
											<!-- ko if: $data.usi_verified() == '0' && $data.avetmiss_done() == '0' -->
											<p><button class="btn btn-small btn-warning pull-left" data-bind="click: $root.loadUsiRegistrationForm.bind($data, $data.customer_id, $data.roster_id)" ><span data-bind="html: $data.name"></span> <i class="icon-arrow-right icon-white"></i> CLICK to Update</button>&nbsp;&nbsp; <br></p>
											<!-- /ko -->
										<!-- /ko -->
									</div>
								</div>
							</div>	
							@elseif (count($order->onlinerosters) > 0)
							<div class="row-fluid">
								<div class="page-header">
								<h4>Students must complete their Enrolment Data before attending the course. 
								Once you have completed this it will be indicated below next to your name</h4></div>
								<div class="span12">
									<input 
									type="hidden" 
									value='[@foreach($order->onlinerosters as $key => $roster){"student_id": "{{$student_id}}", "roster_id": "{{$roster->id}}", "customer_id": "{{$roster->customer->id}}", "name": "{{{$roster->customer->name}}}", "usi_verified": "{{$roster->customer->usi_verified}}", "avetmiss_done": "{{$roster->customer->avetmiss_done}}"} @if (($key + 1) < count($order->onlinerosters)),@endif @endforeach]' 
									data-bind="initObservableArray: student_list, propNames: '[&quot;student_id&quot;,&quot;roster_id&quot;,&quot;customer_id&quot;,&quot;name&quot;,&quot;usi_verified&quot;,&quot;avetmiss_done&quot;]'" />							
									<div class="pull-left" data-bind="foreach: student_list()">
										<!-- ko if: typeof($data.student_id) == 'undefined' || $data.student_id() == '' || $data.student_id() == $data.customer_id() -->
											<!-- ko if: $data.usi_verified() == '1' || $data.avetmiss_done() == '1' -->
											<p class="btn btn-small btn-warning pull-left" disabled><span data-bind="html: $data.name"></span></p>&nbsp;&nbsp; <span class="alert-success"> USI verified and Enrolment data completed!</span><br><br>
											<!-- /ko -->
											<!-- ko if: $data.usi_verified() == '0' && $data.avetmiss_done() == '0' -->
											<p><button class="btn btn-small btn-warning pull-left" data-bind="click: $root.loadUsiRegistrationForm.bind($data, $data.customer_id, $data.roster_id)" ><span data-bind="html: $data.name"></span> <i class="icon-arrow-right icon-white"></i> CLICK to Update</button>&nbsp;&nbsp; <br></p>
											<!-- /ko -->
										<!-- /ko -->
									</div>
								</div>
							</div>	
							@endif
						</div>
					</div>
				</div>
				
				
				<div class="span6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="panel-title"><h3>SHARE</h3></div>
						</div>
						<div class="panel-body" style="color:#333;">
							<div class="row-fluid">
								<div class="page-header"><h4>DON'T COME ALONE!</h4></div>
								<div class="span12">									
									<p>Tell your friends about your booking.</p>
									<p>Click the <b>Share</b> button below.</p>
									<hr />
								</div>
							</div>	
							@if ($data['layout'] != 'perth')
							<div class="row-fluid">
								<div class="input-prepend">
									<a target="_blank" href="https://www.facebook.com/CoffeeRSAschool" title="Visit us" style="display: inline-block;vertical-align: top;"><img src="/images/face_46.png" / ></a>
									<iframe 
									src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FCoffeeRSAschool&amp;width&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=80" 
									scrolling="no" frameborder="0" 
									style="border:none; overflow:hidden; height:80px;" 
									allowTransparency="true"></iframe>
									<hr />
								</div>
							</div>								
					
							<div class="row-fluid">
								<div class="span6">
									<button class="btn btn-small btn-primary pull-left" data-bind="click: $root.showSendToFriendForm"><i class="icon-white icon-thumbs-up"></i> Share via Email</button> <button class="btn btn-small btn-primary" data-bind="click: $root.showSendToFriendForm"><i class="icon-white icon-thumbs-up"></i> Share via Sms</button><br />
									<hr />
								</div>
							</div>
							@endif
							
							@if ($data['layout'] == 'perth')
							<div class="row-fluid">
								<div class="input-prepend">
									<a target="_blank" href="http://www.facebook.com/rsaperth" title="Visit us" style="display: inline-block;vertical-align: top;"><img src="/images/face_46.png" / ></a>									
									<hr />
								</div>
							</div>	
							@endif
							
							<div class="row-fluid">
								<div class="span12">
									<a href="http://instagram.com/coffeersaschool?ref=badge" class="ig-b- ig-b-v-24"><img src="//badges.instagram.com/static/images/ig-badge-view-24.png" alt="Instagram" /></a>						
									<hr />
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		
			<p> &nbsp;</p>
			<p> &nbsp;</p>
		</div>
		<style>
			/* code to enlarge the facebook buttons */
			iframe
			{
				transform: scale(1.2);
				-ms-transform: scale(1.2); 
				-webkit-transform: scale(1.2); 
				-o-transform: scale(1.2); 
				-moz-transform: scale(1.2); 
				transform-origin: top left;
				-ms-transform-origin: top left;
				-webkit-transform-origin: top left;
				-moz-transform-origin: top left;
				-webkit-transform-origin: top left;
			}
		</style>
	
		{{ $page->content_bottom }}

	</div><!--/row-->

	@include('bookings/common/send-to-friend')
	@include('bookings/common/usi-create-form')

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/booking.share.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

