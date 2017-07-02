@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Invoice ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Create Invoice

		<div class="pull-right">
			<a href="{{ route('backend.invoices.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.invoices.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order_id: </label>
				<div class="controls">
					{{ Form::text('order_id', Input::old('order_id'), array('class'=>'input-small')) }}				
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('invoice_date') ? 'error' : '' }}">
				<label class="control-label" for="invoice_date">Invoice_date: </label>
				<div class="controls">
					{{ Form::text('invoice_date', Input::old('invoice_date'), array('class'=>'input-medium', 'id'=>'invoice_date')) }}
					{{ $errors->first('invoice_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('comments') ? 'error' : '' }}">
				<label class="control-label" for="comments">Comments: </label>
				<div class="controls">
					{{ Form::textarea('comments', Input::old('comments'), array('rows'=>'3','class'=>'input-xxlarge')) }}
					{{ $errors->first('comments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('status_id') ? 'error' : '' }}">
				<label class="control-label" for="status">Status: </label>
				<div class="controls">
					{{ Form::select('status_id', $statuses, Input::old('status'), array('class'=>'input-large')) }}				
					{{ $errors->first('status_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Payments: </label>
				<div class="controls span10">
					<table class="table table-striped table-bordered table-condensed table-hover courses">
						<thead>
							<tr>
								<th class="span2">Date</th>
								<th class="span2">Method</th>
								<th class="span3">Comments</th>
								<th class="span1">IP</th>
								<th class="span1">Pay #</th>
								<th class="span2">Status</th>
								<th class="span2">Total</th>
								<th class="span2"><a id="addnewcourse" href="#" class="btn btn-mini btn-success add" data-bind="'click': addNew">Add New</a></th>
							</tr>
						</thead>
						<tbody id="courses_list">
							<tr class="template" style="display:none;">
								<td>{{ Form::text('payment_date[]', '', array('class'=>'input-small')) }}</td>
								<td>{{ Form::select('payment_method_id[]', $methods, '', array('class'=>'input-medium')) }}</td>
								<td><input type="text" name="comments[]" class="input-large" /></td>
								<td><input type="text" name="IP[]" class="input-small" value="{{$_SERVER['REMOTE_ADDR']}}" /></td>
								<td><input type="text" name="instalment[]" class="input-mini" /></td>
								<td>{{ Form::select('pay_status_id[]', $pay_statuses, '', array('class'=>'input-small')) }}</td>
								<td><input type="text" name="total[]" class="input-small price id" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							<tr>
								<td>{{ Form::text('payment_date[]', '', array('class'=>'input-small')) }}</td>
								<td>{{ Form::select('payment_method_id[]', $methods, '', array('class'=>'input-medium')) }}</td>
								<td><input type="text" name="comments[]" class="input-large" /></td>
								<td><input type="text" name="IP[]" class="input-small" value="{{$_SERVER['REMOTE_ADDR']}}" /></td>
								<td><input type="text" name="instalment[]" class="input-mini" /></td>
								<td>{{ Form::select('pay_status_id[]', $pay_statuses, '', array('class'=>'input-small')) }}</td>
								<td><input type="text" name="total[]" class="input-small price id" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
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
				{{ Form::submit('Create Course', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/invoices.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


