@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Question Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div id="content">
<div class="page-header">
	<h3>
		Online Question Update

		<div class="pull-right">
			<a href="{{ route('online.questions.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($question, array('method' => 'PATCH', 'route' => array('online.questions.update', $question->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('step_id') ? 'error' : '' }}">
				<label class="control-label" for="step_id">Step: </label>
				<div class="controls">
					{{ Form::select('step_id', $steps, $question->step_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('step_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('title') ? 'error' : '' }}">
				<label class="control-label" for="title">Title: </label>
				<div class="controls">
					{{ Form::textarea('title', $question->title, array('id'=>'title')) }}
					{{ $errors->first('title', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', $question->order, array('class'=>'input-mini')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('type') ? 'error' : '' }}">
				<label class="control-label" for="type">Type: </label>
				<div class="controls">
					{{ Form::select('type', $types, $question->type, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('weight') ? 'error' : '' }}">
				<label class="control-label" for="weight">Weight: </label>
				<div class="controls">
					{{ Form::select('weight', $weights, $question->weight, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('weight', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $question->active == '1' ? 'checked' : '' }} />
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
				{{ link_to_route('online.questions.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/questions.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop