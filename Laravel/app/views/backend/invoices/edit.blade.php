@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Invoice Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Invoice Update
		<div class="pull-right">
			<a href="{{ route('backend.invoices.index', array('from'=>Input::old('from'),'to'=>Input::old('to'))) }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($invoice, array('method' => 'PATCH', 'route' => array('backend.invoices.update', $invoice->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group">
				<label class="control-label" for="id">Invoice_id: </label>
				<div class="controls">
					{{ Form::label('id', $invoice->id, array('class'=>'input-medium')) }}				
				</div>
			</div>
			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order_id: </label>
				<div class="controls">
					{{ Form::text('order_id', $invoice->order_id, array('class'=>'input-medium')) }}				
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('invoice_date') ? 'error' : '' }}">
				<label class="control-label" for="invoice_date">Invoice_date: </label>
				<div class="controls">
					{{ Form::text('invoice_date', $invoice->invoice_date, array('class'=>'input-medium', 'id'=>'invoice_date')) }}
					{{ $errors->first('invoice_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('comments') ? 'error' : '' }}">
				<label class="control-label" for="comments">Comments: </label>
				<div class="controls">
					{{ Form::text('comments', $invoice->comments, array('class'=>'input-xlarge')) }}
					{{ $errors->first('comments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('status') ? 'error' : '' }}">
				<label class="control-label" for="status">Status: </label>
				<div class="controls">
					{{ Form::select('status', $statuses, $invoice->status_id, array('class'=>'input-large')) }}				
					{{ $errors->first('status', '<span class="help-inline">:message</span>') }}
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
								<th class="span1"><a id="addnewcourse" href="#" class="btn btn-mini btn-success add" data-bind="'click': addNew">Add New</a></th>
							</tr>
						</thead>
						<tbody id="courses_list">
							<tr class="template" style="display:none;">
								<td>
									{{ Form::hidden('payment_id[]', '') }}
									{{ Form::text('payment_date[]', '', array('class'=>'input-small')) }}
								</td>
								<td>{{ Form::select('payment_method_id[]', $methods, '', array('class'=>'input-medium')) }}</td>
								<td><input type="text" name="comments[]" class="input-large" /></td>
								<td><input type="text" name="IP[]" class="input-small" value="{{$_SERVER['REMOTE_ADDR']}}" /></td>
								<td><input type="text" name="instalment[]" class="input-mini" /></td>
								<td>{{ Form::select('pay_status_id[]', $pay_statuses, '', array('class'=>'input-small')) }}</td>
								<td><input type="text" name="total[]" class="input-small price id" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@if (count($invoice->payments) > 0 )
							@foreach ($invoice->payments as $payment)
							<tr>
								<td>
									{{ Form::hidden('payment_id[]', $payment->id) }}
									{{ Form::text('payment_date[]', $payment->payment_date, array('class'=>'input-small')) }}
								</td>
								<td>{{ Form::select('payment_method_id[]', $methods, $payment->payment_method_id, array('class'=>'input-medium')) }}</td>
								<td><input type="text" name="comments[]" class="input-large" value="{{$payment->comments}}" /></td>
								<td><input type="text" name="IP[]" class="input-small" value="{{$payment->IP}}" /></td>
								<td><input type="text" name="instalment[]" class="input-mini" value="{{$payment->instalment}}" /></td>
								<td>{{ Form::select('pay_status_id[]', $pay_statuses, $payment->status_id, array('class'=>'input-small')) }}</td>
								<td><input type="text" name="total[]" class="input-small price id" value="{{$payment->total}}" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
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
				{{ link_to_route('backend.invoices.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/invoices.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
