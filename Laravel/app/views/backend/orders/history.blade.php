@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Order Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
	<div class="page-header">
		<h3>Order History</h3>
	</div>
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group">
				<label>Order: </label>
			</div>
			<div class="control-group well">
					<table class="table table-striped table-bordered table-condensed">
						<thead>
							<tr>
								<th class="span1">Order Id</th>
								<th class="span2">Customer</th>
								<th class="span1">Date</th>
								<th class="span1">End</th>
								<th class="span2">Agent</th>
								<th class="span2">Company</th>
								<th class="span1">Purchase</th>
								@if ($order->group_booking )
								<th class="span2">Group Name</th>
								<th class="span2">Group Notes</th>
								@else
								<th class="span3">Comments</th>
								@endif
								<th class="span1">Status</th>
								<th class="span1">Gst</th>
								<th class="span1">Total</th>
								<th class="span1">Paid</th>
								<th class="span1">Owing</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><span id="order_id">{{ $order->id }}</span></td>
								<td><span>{{ $order->customer ? $order->customer->full_name : $order->customer_id }}</span></td>
								<td><span>{{ $order->order_date  }}</span></td>
								<td><span>{{ $order->backend == '1' ? 'Backend' : 'Frontend' }}</span></td>
								<td class="td-wrap"><span>{{ $order->agent ? $order->agent->name : $order->agent_id }}</span></td>
								<td class="td-wrap"><span>{{ $order->company ? $order->company->name : $order->company_id }}</span></td>
								<td><span>{{ $order->purchase_id }}</span></td>
								@if ($order->group_booking )
								<td class="td-wrap"><span>{{ $order->group_booking->group_name }}</span></td>
								<td class="td-wrap"><span>{{ $order->group_booking->notes }}</span></td>
								@else
								<td class="td-wrap"><span>{{ $order->comments  ? $order->comments : '' }}</span></td>
								@endif
								<td><span>{{ $order->status ? $order->status->name : $order->status_id }}</span></td>
								<td><span>{{ $order->gst }}</span></td>
								<td><span id="order_total">{{ $order->total }}</span></td>
								<td><span id="order_paid">{{ $order->paid }}</span></td>
								<td><span id="order_owing">{{ $order->owing }}</span></td>
							</tr>
						</tbody>
					</table>
			</div>
			<div class="control-group">
				<label>Items: </label>
			</div>
			<div class="control-group well">
				<table class="table table-striped table-bordered table-condensed order_items">
					<thead>
						<tr>
							<th class="span3">Description</th>
							<th class="span3">Comments</th>
							<th class="span1">Qty</th>
							<th class="span1">Price</th>
							<th class="span1">Gst</th>
							<th class="span1">Total</th>
							<th class="span2">User</th>
							<th class="span1">Created</th>
							<th class="span1">Active</th>
							<th class="span1"></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($order->items as $item)
						<tr>
                            <?php
                            if(empty($item->course_instance_id))
                            {
                                $des = explode(',',$item->description);
                                unset($des[1]);
                                $description = implode(',',$des);
                                $description = $description.' - Voucher No '.substr($item->vouchers_ids, 1, -1 );
                            }
                            elseif(!empty($item->vouchers_ids))
                            {
                                $description = $item->description.' - Voucher No '.substr($item->vouchers_ids, 1, -1 );
                            }
                            else
                            {
                                $description = $item->description;
                            }
                            ?>
							<td class="td-wrap"><span>{{$description }}</span></td>
							<td class="td-wrap"><span>{{ $item->comments }}</span></td>
							<td><span>{{ $item->qty }}</span></td>
							<td><span>{{ $item->price }}</span></td>
							<td><span>{{ $item->gst }}</span></td>
							<td><span>{{ $item->total }}</span></td>
							<td><span>{{ $item->user ? $item->user->name : '' }}</span></td>
							<td><span>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</span></td>
							<td><span>{{ $item->active == '1' ? 'x' : '' }}</span></td>
							<td><span><a href="#" class="btn" data-bind="click: openItemDetails.bind($data, '{{$item->id}}')">Edit</a></span></td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="control-group">
				<label>Rosters: </label>
			</div>
			<div class="control-group well">
				<table class="table table-striped table-bordered table-condensed order_rosters">
					<thead>
						<tr>
							<th class="span2">Description</th>
							<th class="span2">Date / Time</th>
							<th class="span2">Student</th>
							<th class="span1">Certificate Id</th>
							<th class="span2">Comments</th>
							<th class="span1">Attendance</th>
							<th class="span3">Notes Admin</th>
							<th class="span3">Notes Class</th>
							<th class="span1"></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($order->rosters as $roster)
						<tr id="{{ $roster->id }}">
							<td>
							@if ($roster->course_instance_id)
								<span>{{ $roster->instance->course->name }}</span>
								<span id="instance_id{{ $roster->id }}" style="display:none;">{{ $roster->course_instance_id }}</span>
							@elseif ($roster->group_booking_id)
								<span>{{ $roster->groupbooking->course->name }}</span>
								<span id="instance_id{{ $roster->id }}" style="display:none;">{{ $roster->group_booking_id }}</span>
							@endif									
							</td>
							<td>
							@if ($roster->course_instance_id)
								<span>{{ $roster->instance->course_date }}<BR>{{ $roster->instance->start_time }} : {{ $roster->instance->end_time }}</span>
							@elseif ($roster->group_booking_id)
								<span>{{ $roster->groupbooking->course_date }}<BR>{{ $roster->groupbooking->start_time }} : {{ $roster->groupbooking->end_time }}</span>
							@endif									
							</td>
							<td>
							<span id="customer{{ $roster->id }}">{{ $roster->customer ? $roster->customer->full_name : $roster->customer_id }}</span>
							<span id="id_customer{{ $roster->id }}" style="display:none;">{{ $roster->customer_id }}</span>
							<span id="email{{ $roster->id }}" style="display:none;">{{ $roster->customer ? $roster->customer->email : 'no email found' }}</span>
							<span id="mobile{{ $roster->id }}" style="display:none;">{{ $roster->customer ? $roster->customer->mobile : 'no mobile found' }}</span>
							</td>
							<td><span id="certificate_id{{ $roster->id }}">{{ $roster->certificate_id }}</span></td>
							<td class="td-wrap"><span>{{ $roster->comments }}</span></td>
							<td><span id="attendance{{ $roster->id }}">{{ $roster->attendance }}</span></td>
							<td class="td-wrap"><span id="notes_admin{{ $roster->id }}">{{ $roster->notes_admin }}</span></td>
							<td class="td-wrap"><span id="notes_class{{ $roster->id }}">{{ $roster->notes_class }}</span></td>
							<td><span><a href="#" class="btn" data-bind="click: openRosterDetails.bind($data, '{{$roster->id}}')">Edit</a></span></td>
						</tr>
						@endforeach
						@foreach ($order->onlinerosters as $roster)
						<tr id="{{ $roster->id }}">
							<td>
							@if ($roster->course_id)
								<span>{{ $roster->course->name }}</span>
								<span id="instance_id{{ $roster->id }}" style="display:none;">{{ $roster->course_instance_id }}</span>
							@endif									
							</td>
							<td>Online</td>
							<td>
							<span id="customer{{ $roster->id }}">{{ $roster->customer ? $roster->customer->full_name : $roster->customer_id }}</span>
							<span id="id_customer{{ $roster->id }}" style="display:none;">{{ $roster->customer_id }}</span>
							<span id="email{{ $roster->id }}" style="display:none;">{{ $roster->customer ? $roster->customer->email : 'no email found' }}</span>
							<span id="mobile{{ $roster->id }}" style="display:none;">{{ $roster->customer ? $roster->customer->mobile : 'no mobile found' }}</span>
							</td>
							<td><span id="certificate_id{{ $roster->id }}">{{ $roster->certificate_id }}</span></td>
							<td class="td-wrap"><span>{{ $roster->comments }}</span></td>
							<td><span id="attendance{{ $roster->id }}">{{ $roster->attendance }}</span></td>
							<td class="td-wrap"><span id="notes_admin{{ $roster->id }}">{{ $roster->notes_admin }}</span></td>
							<td class="td-wrap"><span id="notes_class{{ $roster->id }}">{{ $roster->notes_class }}</span></td>
							<td><span><a href="#" class="btn" data-bind="click: openRosterDetails.bind($data, '{{$roster->id}}')">Edit</a></span></td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			
			<div class="control-group">
				<label>Payments: </label>
			</div>
			<div class="control-group well">
				<table class="table table-striped table-bordered table-condensed table-hover order_payments">
					<thead>
						<tr>
							<th class="span1">Date</th>
							<th class="span1">Method</th>
							<th class="span3">Comments</th>
							<th class="span1">IP</th>
							<th class="span1">Status</th>
							<th class="span1">Gateway Response</th>
							<th class="span1">Total</th>
							<th class="span1">End</th>
							<th class="span1">User</th>
							<th class="span1"></th>
						</tr>
					</thead>
					<tbody >
						@if (count($order->payments) > 0 )
						@foreach ($order->payments as $payment)
						<tr id="{{$payment->id}}">
							<td><span id="pay_date{{ $payment->id }}">{{ $payment->payment_date }}</span></td>
							<td><span id="method{{ $payment->id }}">{{ $payment->method ? $payment->method->name : $payment->payment_method_id }}</span></td>
							<td class="td-wrap"><span id="comments{{ $payment->id }}">{{ $payment->comments }}</span></td>
							<td><span>{{ $payment->IP }}</span></td>
							<td><span id="status{{ $payment->id }}">{{ $payment->status->name }}</span></td>
							<td class="td-wrap"><span>{{ $payment->response }}</span></td>
							<td><span id="pay_total{{ $payment->id }}">{{ $payment->total }}</span></td>
							<td><span id="backend{{ $payment->id }}">{{ $payment->backend == '1' ? 'Backend' : 'Frontend' }}</span></td>
							<td><span>{{ $payment->user ? $payment->user->name : '' }}</span></td>
							<td><span><a href="#" class="btn" data-bind="click: openPaymentDetails.bind($data, '{{$payment->id}}')">Edit</a></span></td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
			
			@if (count($order->gateway_responses) > 0 )
			<div class="control-group">
				<label>GateWays Responses: </label>
			</div>
			<div class="control-group well">
				<table class="table table-striped table-bordered table-condensed table-hover">
					<thead>
						<tr>
							<th class="span1">Id</th>
							<th class="span1">Session Id</th>
							<th class="span3">Session Content</th>
							<th class="span3">Gateway Response</th>
							<th class="span1">Status</th>
							<th class="span1">Created At</th>
							<th class="span1">Updated At</th>
						</tr>
					</thead>
					<tbody >
						@foreach ($order->gateway_responses as $response)
						<tr>
							<td>{{ $response->id }}</td>
							<td class="td-wrap">{{ $response->session_id }}</td>
							<td class="td-wrap">{{ $response->session_content }}</td>
							<td class="td-wrap">{{ print_r($response->gateway_response,true) }}</td>
							<td>{{ $response->returned_page }}</td>
							<td>{{ date('d/m/Y H:i', strtotime($response->created_at)) }}</td>
							<td>{{ date('d/m/Y H:i', strtotime($response->updated_at)) }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			@endif
			
			
		</div>
	</div>

@include('backend/calendar/roster-details')
@include('backend/calendar/transaction')

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/history.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
