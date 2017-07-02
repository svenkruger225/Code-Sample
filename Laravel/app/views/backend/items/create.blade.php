@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Oreder Item ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="page-header">
	<h3>
		Create Oreder Item

		<div class="pull-right">
			<a href="{{ route('backend.items.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.items.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
            
			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order_id: </label>
				<div class="controls">
					{{ Form::text('order_id', Input::old('order_id'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('product_id') ? 'error' : '' }}">
				<label class="control-label" for="product_id">product_id: </label>
				<div class="controls">
					{{ Form::text('product_id', Input::old('product_id'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('product_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('qty') ? 'error' : '' }}">
				<label class="control-label" for="qty">Qty: </label>
				<div class="controls">
					{{ Form::text('qty', Input::old('qty'), array('class'=>'input-small')) }}
					{{ $errors->first('qty', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('price') ? 'error' : '' }}">
				<label class="control-label" for="price">Item Price: </label>
				<div class="controls">
					{{ Form::text('price', Input::old('price'), array('class'=>'input-medium')) }}
					{{ $errors->first('price', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('gst') ? 'error' : '' }}">
				<label class="control-label" for="gst">Item Price: </label>
				<div class="controls">
					{{ Form::text('gst', Input::old('gst'), array('class'=>'input-medium')) }}
					{{ $errors->first('gst', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total') ? 'error' : '' }}">
				<label class="control-label" for="total">Item Total: </label>
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
				{{ Form::submit('Create Oreder Item', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}


@stop


