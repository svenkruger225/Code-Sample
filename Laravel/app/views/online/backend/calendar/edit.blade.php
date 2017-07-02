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
	<h3>
		Order Update

		<div class="pull-right">
			<a href="{{ route('backend.orders.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($order, array('method' => 'PATCH', 'route' => array('backend.orders.update', $order->id), 'class'=>'form-horizontal table table-condensed table-striped')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group">
				<label class="control-label" for="id">Customer: </label>
				<div class="controls">
					{{ Form::label('l_id', $order->id, array('class'=>'input-small')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="customer_id">Customer: </label>
				<div class="controls">
					{{ Form::hidden('customer_id', $order->customer_id) }}
					{{ Form::label('customer', ($order->customer ? $order->customer->fullName : $order->customer_id), array('class'=>'input-xlarge')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="purchase_id">Purchase: </label>
				<div class="controls">
					{{ Form::hidden('purchase_id', $order->purchase_id) }}
					{{ Form::label('purchase', $order->purchase_id, array('class'=>'input-xlarge')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="order_date">Order_date: </label>
				<div class="controls">
					{{ Form::hidden('order_date', $order->order_date) }}
					{{ Form::label('order_date', $order->order_date, array('class'=>'input-small')) }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('backend') ? 'error' : '' }}">
				<label class="control-label" for="backend">Backend: </label>
				<div class="controls">
					<input type="hidden" name="payment" value="0" /><input type="checkbox" name="backend" value="1" {{ $order->backend == '1' ? 'checked' : '' }} />
					{{ $errors->first('backend', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="agent_id">Agent: </label>
				<div class="controls">
					{{ Form::hidden('agent_id', $order->agent_id) }}
					{{ Form::label('agent', ($order->agent ? $order->agent->name : $order->agent_id), array('class'=>'input-xlarge')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="company_id">Company: </label>
				<div class="controls">
					{{ Form::hidden('company_id', $order->company_id) }}
					{{ Form::label('company', ($order->company ? $order->company->name : $order->company_id), array('class'=>'input-xlarge')) }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('comments') ? 'error' : '' }}">
				<label class="control-label" for="comments">Comments: </label>
				<div class="controls">
					{{ Form::textarea('comments', $order->comments, array('rows'=> 3,'class'=>'input-xxlarge')) }}
					{{ $errors->first('comments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="course_id">Items: </label>
				<div class="controls span10">
					<table class="table table-striped table-bordered table-condensed table-parent">
						<thead>
							<tr>
								<th>Name</th>
								<th>Qty</th>
								<th>Price</th>
								<th>Gst</th>
								<th>Total</th>
								<th>Active</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($order->items as $item)
							<tr>
								<td>
								@if ($item->course_instance_id)
									{{ Form::label('course_instance_id[]', $item->instance->course->name, array('class'=>'input-xlarge')) }}
								@elseif ($item->group_booking_id)
									{{ Form::label('group_booking_id[]', $item->groupbooking->name, array('class'=>'input-xlarge')) }}
								@elseif ($item->vouchers_ids != '')
									{{ Form::label('vouchers_ids[]', $item->vouchers_ids, array('class'=>'input-xlarge')) }}
								@elseif ($item->product_id)
									{{ Form::label('product_id[]', $item->product->name . ' (' . $item->comments . ')', array('class'=>'input-xlarge')) }}
								@endif									
								</td>
								<td>{{ Form::label('qty[]', $item->qty, array('class'=>'input-mini')) }}</td>
								<td>{{ Form::label('price[]', $item->price, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('gst[]', $item->gst, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('total[]', $item->total, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('active[]', $item->active, array('class'=>'input-small')) }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="status">Status: </label>
				<div class="controls">
					{{ Form::hidden('status_id', $order->status_id) }}
					{{ Form::label('status', $order->status->name, array('class'=>'input-large')) }}				
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="gst">GST: </label>
				<div class="controls">
					{{ Form::hidden('gst', $order->gst) }}
					{{ Form::label('gst', $order->gst, array('class'=>'input-medium')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="total">Total: </label>
				<div class="controls">
					{{ Form::hidden('total', $order->total) }}
					{{ Form::label('total', $order->total, array('class'=>'input-medium', 'id'=>'order_total')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Payments: </label>
				<div class="controls span10">
					<table class="table table-striped table-bordered table-condensed table-hover courses">
						<thead>
							<tr>
								<th class="span1">Date</th>
								<th class="span2">Method</th>
								<th class="span3">Comments</th>
								<th class="span1">IP</th>
								<th class="span1">Pay #</th>
								<th class="span1">Status</th>
								<th class="span1">Total</th>
							</tr>
						</thead>
						<tbody id="courses_list">
							@if (count($order->payments) > 0 )
							@foreach ($order->payments as $payment)
							<tr>
								<td>{{ Form::label('payment_date[]', $payment->payment_date, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('payment_method_id[]', $payment->method ? $payment->method->name : $payment->payment_method_id, array('class'=>'input-medium')) }}</td>
								<td>{{ Form::label('comments[]', $payment->comments, array('class'=>'input-large')) }}</td>
								<td>{{ Form::label('IP[]', $payment->IP, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('instalment[]', $payment->instalment, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('status[]', $payment->status->name, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('total[]', $payment->total, array('class'=>'input-small')) }}</td>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Add New Transaction </label>
				<div class="controls">
					<a href="{{ route('backend.payments.create') }}" target="_blank" class="btn btn-small btn-info">New Transaction</a>
				</div>
			</div>
		</div>
	</div>
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				{{ Form::submit('Update', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.orders.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

</div>

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/orders.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
