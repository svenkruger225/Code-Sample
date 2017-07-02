@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Update Attachment ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Update Attachment
		<div class="pull-right">
			<a href="{{ route('backend.attachments.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::model($attachment, array('method' => 'PATCH', 'route' => array('backend.attachments.update', $attachment->id), 'class'=>'form-horizontal', 'files'=>true)) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
		
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $attachment->name, array('class'=>'input-xxlarge', 'id' =>'name')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{  $errors->has('path') ? 'error' : '' }}">
				<label class="control-label" for="attachment">Current Path: </label>
				<div class="controls">
					{{ Form::label('path', $attachment->path, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('path', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{  $errors->has('attachment') ||  $errors->has('path') ? 'error' : '' }}">
				<label class="control-label" for="attachment">Attachment: </label>
				<div class="controls">
					{{ Form::file('attachment', '', array('class'=>'input-xlarge')) }}
					{{ $errors->first('attachment', '<span class="help-inline">:message</span>') }}
					{{ $errors->first('path', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('type') ? 'error' : '' }}">
				<label class="control-label" for="type">Type: </label>
				<div class="controls">
					{{ Form::select('type', $types, $attachment->type, array('class'=>'input-large')) }}				
					{{ $errors->first('type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="title">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $attachment->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update Attachment', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

</div>

@stop