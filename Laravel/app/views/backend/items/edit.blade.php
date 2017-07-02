@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Order Item Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Order Item Update

		<div class="pull-right">
			<a href="{{ route('backend.items.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($item, array('method' => 'PATCH', 'route' => array('backend.items.update', $item->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

        <li>
            
			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order_id: </label>
				<div class="controls">
					{{ Form::label('order_id', $item->order_id, array('class'=>'input-xlarge')) }}
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">description: </label>
				<div class="controls">
					{{ Form::textarea('description', $item->description, array('class'=>'input-xxlarge','size' => '100x4')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('comments') ? 'error' : '' }}">
				<label class="control-label" for="comments">comments: </label>
				<div class="controls">
					{{ Form::textarea('comments', $item->comments, array('class'=>'input-xxlarge','size' => '100x4')) }}
					{{ $errors->first('comments', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('qty') ? 'error' : '' }}">
				<label class="control-label" for="qty">Qty: </label>
				<div class="controls">
					{{ Form::label('qty', $item->qty, array('class'=>'input-small')) }}
					{{ $errors->first('qty', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('price') ? 'error' : '' }}">
				<label class="control-label" for="price">Item Price: </label>
				<div class="controls">
					{{ Form::text('price', $item->price, array('class'=>'input-medium')) }}
					{{ $errors->first('price', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('gst') ? 'error' : '' }}">
				<label class="control-label" for="gst">Item Price: </label>
				<div class="controls">
					{{ Form::label('gst', $item->gst, array('class'=>'input-medium')) }}
					{{ $errors->first('gst', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total') ? 'error' : '' }}">
				<label class="control-label" for="total">Item Total: </label>
				<div class="controls">
					{{ Form::label('total', $item->total, array('class'=>'input-medium')) }}
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
				{{ link_to_route('backend.items.index', 'Cancel', array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

@stop
