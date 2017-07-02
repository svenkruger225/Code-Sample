@extends('backend/layouts/default')

{{-- Web site Title --}}
@section('title')
Referrer Update ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		Referrer Update
		<div class="pull-right">
			<a href="{{ route('backend.referrers.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.referrers.store',  'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">

			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', Input::old('name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('url') ? 'error' : '' }}">
				<label class="control-label" for="url">Referrer Url: </label>
				<div class="controls">
					{{ Form::text('url', Input::old('url'), array('class'=>'input-xxlarge')) }}
					{{ $errors->first('url', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('ad_id') ? 'error' : '' }}">
				<label class="control-label" for="ad_id">Ad Id: </label>
				<div class="controls">
					{{ Form::text('ad_id', Input::old('ad_id'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('ad_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', Input::old('order'), array('class'=>'input-mini')) }}
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

	</div
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-small btn-success">Create Referrer</button>
				<a class="btn btn-small" href="{{ route('backend.referrers.index') }}">Cancel</a>
				<button type="reset" class="btn btn-small btn-danger">Reset</button>
			</div>
		</div>
	</div>
{{ Form::close() }}

@stop