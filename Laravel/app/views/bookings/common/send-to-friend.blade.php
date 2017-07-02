    <div class="modal-clean hide" id="email-friend-form" data-bind="with: friend">
	    <div class="modal-header alert-info">
			<div class="pull-left"><h3>Share via Email & Sms</h3></div>
		    <a class="close pull-right" data-dismiss="modal">x</a>
	    </div>
	
        <div class="modal-body ">
		<form class="form-horizontal">
			<input type="hidden" id="order_id" value="{{$order->id}}" data-bind="value: order_id" />
			<input type="hidden" id="customer_id" value="{{$order->customer_id}}" data-bind="value: customer_id" />
			<input type="hidden" id="share_link" value="{{$share_link}}" data-bind="value: share_link" />
			@if(empty($order->items->first()->group_booking_id))
			<input type="hidden" id="outlook_message" value="Your friend {{$order->customer->name}} has just booked:%0D%0A @foreach($order->items as $item)-@if ($item->item_type_id == '1') {{$item->instance->course->name}}: {{$item->instance->location->complete_address}} - {{$item->instance->course_date_time}} @else {{$item->vouchers_ids}} : {{$item->description}} @endif %0D%0A@endforeach%0D%0A Click here to join them: {{$share_link}}" data-bind="value: outlook_message" /> 
			@else
			<input type="hidden" id="outlook_message" value="Your friend {{$order->customer->name}} has just booked:%0D%0A @foreach($order->items as $item)-@if ($item->item_type_id == '1') {{$item->groupbooking->course->name}}: {{$item->groupbooking->location->complete_address}} - {{$item->groupbooking->course_date_time}} @else {{$item->vouchers_ids}} : {{$item->description}} @endif %0D%0A@endforeach%0D%0A Click here to join them: {{$share_link}}" data-bind="value: outlook_message" /> 
			@endif
		    <div class="control-group"> 
		        <div class="input-xxlarge">
					<p>Share this booking with your friends so they can join you!.</p>
					<ul>
						@foreach($order->items as $item)
						@if ($item->item_type_id == '1')
							@if(empty($order->items->first()->group_booking_id))
							<li>{{$item->instance->course->name}} at {{$item->instance->location->complete_address}} - {{$item->instance->course_date_time}}</li>
							@else
							<li>{{$item->groupbooking->course->name}} at {{$item->groupbooking->location->complete_address}} - {{$item->groupbooking->course_date_time}}</li>
							@endif
						@else
						<li>{{$item->vouchers_ids}} : {{$item->description}}</li>
						@endif
						@endforeach
					</ul>
				</div>
		    </div>
		    <div class="control-group">&nbsp;</div>
	        <div class="control-group">
				<div class="controls input-append">
					@if(empty($order->items->first()->group_booking_id))
					<a style="color:#fff !important; text-decoration:none !important;" class="btn btn-primary" href="mailto:?subject=Join {{$order->customer->name}} at Coffe School&body=Your friend {{$order->customer->name}} has just booked:%0D%0A @foreach($order->items as $item)-@if ($item->item_type_id == '1'){{$item->instance->course->name}}: {{$item->instance->location->complete_address}} - {{$item->instance->course_date_time}}@else {{$item->vouchers_ids}} : {{$item->description}} @endif %0D%0A@endforeach%0D%0A Click here to join them: {{$share_link}}"><i class="icon-white icon-envelope"></i>&nbsp;&nbsp; Share via email - Outlook</a>
					@else
					<a style="color:#fff !important; text-decoration:none !important;" class="btn btn-primary" href="mailto:?subject=Join {{$order->customer->name}} at Coffe School&body=Your friend {{$order->customer->name}} has just booked:%0D%0A @foreach($order->items as $item)-@if ($item->item_type_id == '1'){{$item->groupbooking->course->name}}: {{$item->groupbooking->location->complete_address}} - {{$item->groupbooking->course_date_time}}@else {{$item->vouchers_ids}} : {{$item->description}} @endif %0D%0A@endforeach%0D%0A Click here to join them: {{$share_link}}"><i class="icon-white icon-envelope"></i>&nbsp;&nbsp; Share via email - Outlook</a>
					@endif
				</div>
	        </div>
		    <div class="control-group">&nbsp;</div>
	        <div class="control-group">
				<div class="controls input-append">
					<input type="text" class="input-large" id="email" data-bind="value: email" placeholder="your friend's email address" /><button type="button" class="btn btn-primary" data-bind="click: $root.emailFriend"><i class="icon-white icon-envelope"></i>&nbsp;&nbsp; Send to email address</button>
				</div>
	        </div>
		    <div class="control-group">&nbsp;</div>
	        <div class="control-group">
				<div class="controls input-append">
					<input type="text" class="input-large" id="mobile" data-bind="value: mobile" placeholder="your friend's mobile number" /><button type="button" class="btn btn-primary" data-bind="click: $root.smsFriend"><i class="icon-white icon-thumbs-up"></i>&nbsp;&nbsp; Send via sms</button>
				</div>
	        </div>
		</form>
	    </div>
    </div>
