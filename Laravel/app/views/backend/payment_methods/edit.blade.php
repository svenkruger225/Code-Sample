@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Payment Method Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Payment Method Update

		<div class="pull-right">
			<a href="{{ route('backend.payment_methods.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($payment_method, array('method' => 'PATCH', 'route' => array('backend.payment_methods.update', $payment_method->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('code') ? 'error' : '' }}">
				<label class="control-label" for="code">Code: </label>
				<div class="controls">
					{{ Form::text('code', $payment_method->code, array('class'=>'input-xlarge')) }}
					{{ $errors->first('code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $payment_method->name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('fee') ? 'error' : '' }}">
				<label class="control-label" for="fee">Fee: </label>
				<div class="controls">
					{{ Form::text('fee', $payment_method->fee, array('class'=>'input-xlarge')) }}
					{{ $errors->first('fee', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('pay_type') ? 'error' : '' }}">
				<label class="control-label" for="pay_type">Pay Type: </label>
				<div class="controls">
					{{ Form::select('pay_type', array('online'=>'OnLine', 'offline'=>'OffLine'), $payment_method->pay_type, array('class'=>'input-medium')) }}
					{{ $errors->first('pay_type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('show_online') ? 'error' : '' }}">
				<label class="control-label" for="show_online">Show Online: </label>
				<div class="controls">
					<input type="hidden" name="show_online" value="0" /><input type="checkbox" name="show_online" value="1" {{ $payment_method->show_online == '1' ? 'checked' : '' }} />
					{{ $errors->first('show_online', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', $payment_method->order, array('class'=>'input-xlarge')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $payment_method->active == '1' ? 'checked' : '' }} />
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
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
				{{ link_to_route('backend.payment_methods.index', 'Cancel', array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}


@stop
