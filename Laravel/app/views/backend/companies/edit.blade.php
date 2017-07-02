@extends('backend/layouts/default')

{{-- Web site Title --}}
@section('title')
Company Update ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		Company Update
		<div class="pull-right">
			<a href="{{ route('backend.companies.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::model($company, array('method' => 'PATCH', 'route' => array('backend.companies.update', $company->id),  'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $company->name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('contact_name') ? 'error' : '' }}">
				<label class="control-label" for="contact_name">Contact Name: </label>
				<div class="controls">
					{{ Form::text('contact_name', $company->contact_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('contact_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('contact_position') ? 'error' : '' }}">
				<label class="control-label" for="contact_position">Contact Position: </label>
				<div class="controls">
					{{ Form::text('contact_position', $company->contact_position, array('class'=>'input-xlarge')) }}
					{{ $errors->first('contact_position', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
				<label class="control-label" for="email">Email: </label>
				<div class="controls">
					{{ Form::text('email', $company->email, array('class'=>'input-xlarge')) }}
					{{ $errors->first('email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('phone') ? 'error' : '' }}">
				<label class="control-label" for="phone">Phone: </label>
				<div class="controls">
					{{ Form::text('phone', $company->phone, array('class'=>'input-large')) }}
					{{ $errors->first('phone', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
		  
			<div class="control-group {{ $errors->has('mobile') ? 'error' : '' }}">
				<label class="control-label" for="mobile">Mobile: </label>
				<div class="controls">
					{{ Form::text('mobile', $company->mobile, array('class'=>'input-large')) }}
					{{ $errors->first('mobile', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
					 
			<div class="control-group {{ $errors->has('fax') ? 'error' : '' }}">
				<label class="control-label" for="fax">Fax: </label>
				<div class="controls">
					{{ Form::text('fax', $company->fax, array('class'=>'input-large')) }}
					{{ $errors->first('fax', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $company->active == '1' ? 'checked' : '' }} />
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

		</div>
	</div
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-small btn-success">Update Company</button>
				<a class="btn btn-small" href="{{ route('backend.companies.index') }}">Cancel</a>
				<button type="reset" class="btn btn-small btn-danger">Reset</button>
			</div>
		</div>
	</div>
{{ Form::close() }}

@stop