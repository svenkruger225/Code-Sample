@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Voucher Create ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>Create Voucher
		<div class="pull-right">
			<a href="{{ route('backend.vouchers.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::open(array('route' => 'backend.vouchers.store', 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('customer_id') ? 'error' : '' }}">
				<label class="control-label" for="customer_id">Customer Id: </label>
				<div class="controls">
					{{ Form::text('customer_id', Input::old('customer_id'), array('class'=>'input-small')) }}
					{{ $errors->first('customer_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('course_id') ? 'error' : '' }}">
				<label class="control-label" for="course_id">Course: </label>
				<div class="controls">
					{{ Form::select('course_id', $courses, Input::old('course_id'), array('class'=>'input-large')) }}
					{{ $errors->first('course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-large')) }}
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('expiry_date') ? 'error' : '' }}">
				<label class="control-label" for="expiry_date">Expiry Date: </label>
				<div class="controls">
					{{ Form::text('expiry_date', Input::old('expiry_date'), array('id'=> 'expiry_date', 'class'=>'input-medium')) }}
					{{ $errors->first('expiry_date', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('status_id') ? 'error' : '' }}">
				<label class="control-label" for="status_id">Status: </label>
				<div class="controls">
					{{ Form::select('status_id', $statuses, Input::old('status_id'), array('class'=>'input-large')) }}
					{{ $errors->first('status_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ Input::old('active') == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Create', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.vouchers.index', 'Cancel', array(),  array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>
{{ Form::close() }}
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/vouchers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop


