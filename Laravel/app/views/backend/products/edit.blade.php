@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Product Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Course Product

		<div class="pull-right">
			<a href="{{ route('backend.products.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a> | 
			<a href="{{ route('backend.products.index') }}" class="btn btn-small btn-info"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($product, array('method' => 'PATCH', 'route' => array('backend.products.update', $product->id), 'class'=>'form-horizontal', 'files'=>true)) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $product->name, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::textarea('description', $product->description, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('price') ? 'error' : '' }}">
				<label class="control-label" for="price">Price: </label>
				<div class="controls">
					{{ Form::text('price', $product->price, array('class'=>'input-small')) }}
					{{ $errors->first('price', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('gst') ? 'error' : '' }}">
				<label class="control-label" for="gst">Apply Gst: </label>
				<div class="controls">
					<input type="hidden" name="gst" value="0" /><input type="checkbox" name="gst" value="1" {{ $product->gst == '1' ? 'checked' : '' }} />
					{{ $errors->first('gst', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $product->active == '1' ? 'checked' : '' }} />
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
				{{ link_to_route('backend.products.index', 'Cancel', array(), array('class' => 'btn btn-default btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/products.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
