@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Payment Method ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Create Payment Method

		<div class="pull-right">
			<a href="{{ route('backend.payment_methods.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.payment_methods.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
            
			<div class="control-group {{ $errors->has('code') ? 'error' : '' }}">
				<label class="control-label" for="code">Code: </label>
				<div class="controls">
					{{ Form::text('code', Input::old('code'), array('class'=>'input-small')) }}
					{{ $errors->first('code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', Input::old('name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('fee') ? 'error' : '' }}">
				<label class="control-label" for="fee">Fee: </label>
				<div class="controls">
					{{ Form::text('fee', Input::old('fee'), array('class'=>'input-medium')) }}
					{{ $errors->first('fee', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('pay_type') ? 'error' : '' }}">
				<label class="control-label" for="pay_type">Pay Type: </label>
				<div class="controls">
					{{ Form::select('pay_type', array('online'=>'OnLine', 'offline'=>'OffLine'), Input::old('pay_type'), array('class'=>'input-medium')) }}
					{{ $errors->first('pay_type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('show_online') ? 'error' : '' }}">
				<label class="control-label" for="show_online">Show Online: </label>
				<div class="controls">
					{{ Form::checkbox('show_online') }}
					{{ $errors->first('show_online', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', Input::old('order'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					{{ Form::checkbox('active') }}
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
				{{ Form::submit('Create Payment Method', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

@stop


