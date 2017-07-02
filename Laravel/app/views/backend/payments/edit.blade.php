@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Payment Update ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Payment Update

		<div class="pull-right">
			<a href="{{ route('backend.payments.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($payment, array('method' => 'PATCH', 'route' => array('backend.payments.update', $payment->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
            
			<div class="control-group">
				<label class="control-label" for="id">Id: </label>
				<div class="controls">
					{{ Form::label('id', $payment->id, array('class'=>'input-medium')) }}
					{{ $errors->first('id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order Id: </label>
				<div class="controls">
					<input type="hidden" name="order_id" value="{{$payment->order_id}}" />
					{{ Form::label('order', $payment->order_id, array('class'=>'input-medium')) }}
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('payment_date') ? 'error' : '' }}">
				<label class="control-label" for="payment_date">Payment_date: </label>
				<div class="controls">
					{{ Form::text('payment_date', $payment->payment_date, array('class'=>'input-medium')) }}
					{{ $errors->first('payment_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('payment_method_id') ? 'error' : '' }}">
				<label class="control-label" for="payment_method_id">Payment_method: </label>
				<div class="controls">
					{{ Form::select('payment_method_id', $methods, $payment->payment_method_id, array('class'=>'input-large')) }}
					{{ $errors->first('payment_method_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('backend') ? 'error' : '' }}">
				<label class="control-label" for="backend">Backend: </label>
				<div class="controls">
					{{ Form::select('backend', array('1'=>'Backend', '0'=>'Frontend'), $payment->backend, array('class'=>'input-medium')) }}
					{{ $errors->first('backend', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('comments') ? 'error' : '' }}">
				<label class="control-label" for="comments">Comments: </label>
				<div class="controls">
					{{ Form::text('comments', $payment->comments, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('comments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('IP') ? 'error' : '' }}">
				<label class="control-label" for="IP">IP address: </label>
				<div class="controls">
					{{ Form::text('IP', $payment->IP, array('class'=>'input-small')) }}
					{{ $errors->first('IP', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('instalment') ? 'error' : '' }}">
				<label class="control-label" for="instalment">Instalment #: </label>
				<div class="controls">
					{{ Form::text('instalment', $payment->instalment, array('class'=>'input-small')) }}
					{{ $errors->first('instalment', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('gateway_id') ? 'error' : '' }}">
				<label class="control-label" for="gateway_id">Gateway Id: </label>
				<div class="controls">
					{{ Form::text('gateway_id', $payment->gateway_id, array('class'=>'input-small')) }}
					{{ $errors->first('gateway_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('gateway_response') ? 'error' : '' }}">
				<label class="control-label" for="gateway_response">Gateway Response: </label>
				<div class="controls">
					{{ var_dump(json_decode($payment->gateway_response)) }}
					{{ $errors->first('gateway_response', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('status_id') ? 'error' : '' }}">
				<label class="control-label" for="status_id">Status: </label>
				<div class="controls">
					{{ Form::select('status_id', $statuses, $payment->status_id, array('class'=>'input-small')) }}
					{{ $errors->first('status_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total') ? 'error' : '' }}">
				<label class="control-label" for="total">Total: </label>
				<div class="controls">
					{{ Form::text('total', $payment->total, array('class'=>'input-medium')) }}
					{{ $errors->first('total', '<span class="help-inline">:message</span>') }}
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
				{{ link_to_route('backend.payments.index', 'Cancel', array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>	

{{ Form::close() }}

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/payments.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
