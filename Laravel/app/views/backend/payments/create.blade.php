@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Payment ::
@parent
@stop

{{-- Page content --}}
@section('content')


<div class="page-header">
	<h3>
		Create Payment

		<div class="pull-right">
			<a href="{{ route('backend.payments.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.payments.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order Id: </label>
				<div class="controls">
					{{ Form::text('order_id', Input::old('order_id'), array('class'=>'input-medium')) }}
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('payment_date') ? 'error' : '' }}">
				<label class="control-label" for="payment_date">Payment_date: </label>
				<div class="controls">
					{{ Form::text('payment_date', Input::old('payment_date'), array('class'=>'input-medium')) }}
					{{ $errors->first('payment_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('payment_method_id') ? 'error' : '' }}">
				<label class="control-label" for="payment_method_id">Payment_method: </label>
				<div class="controls">
					{{ Form::select('payment_method_id', $methods, Input::old('payment_method_id'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('payment_method_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('backend') ? 'error' : '' }}">
				<label class="control-label" for="backend">Backend: </label>
				<div class="controls">
					{{ Form::select('backend', array('1'=>'Backend', '0'=>'Frontend'),Input::old('backend'), array('class'=>'input-medium')) }}
					{{ $errors->first('backend', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('comments') ? 'error' : '' }}">
				<label class="control-label" for="comments">Comments: </label>
				<div class="controls">
					{{ Form::text('comments', Input::old('comments'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('comments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('instalment') ? 'error' : '' }}">
				<label class="control-label" for="instalment">Instalment #: </label>
				<div class="controls">
					{{ Form::text('instalment', Input::old('instalment'), array('class'=>'input-small')) }}
					{{ $errors->first('instalment', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('status_id') ? 'error' : '' }}">
				<label class="control-label" for="status_id">Status: </label>
				<div class="controls">
					{{ Form::select('status_id', $statuses, Input::old('status_id'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('status_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total') ? 'error' : '' }}">
				<label class="control-label" for="total">Total: </label>
				<div class="controls">
					{{ Form::text('total', Input::old('total'), array('class'=>'input-medium')) }}
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
				{{ Form::submit('Create Payment', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/payments.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


