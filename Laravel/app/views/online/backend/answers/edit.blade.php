@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Online Answer Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div id="content">
<div class="page-header">
	<h3>
		Online Answer Update

		<div class="pull-right">
			<a href="{{ route('online.answers.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($answer, array('method' => 'PATCH', 'route' => array('online.answers.update', $answer->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('question_id') ? 'error' : '' }}">
				<label class="control-label" for="question_id">Question: </label>
				<div class="controls">
					{{ Form::select('question_id', $questions, $answer->question_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('question_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::textarea('description', $answer->description, array('id'=>'description')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('correct') ? 'error' : '' }}">
				<label class="control-label" for="correct">Correct Answer: </label>
				<div class="controls">
					<input type="hidden" name="correct" value="0" /><input type="checkbox" name="correct" value="1" {{ $answer->correct == '1' ? 'checked' : '' }} />
					{{ $errors->first('correct', '<span class="help-inline">:message</span>') }}
				</div>
			</div>			
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', $answer->order, array('class'=>'input-mini')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $answer->active == '1' ? 'checked' : '' }} />
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
				{{ link_to_route('online.answers.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/answers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop