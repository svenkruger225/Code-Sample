@extends('backend/layouts/default')

{{-- Web site Title --}}
@section('title')
Customer Update ::
@parent
@stop

{{-- Content --}}
@section('content')
<div class="page-header">
	<h3>
		Customer Update
		<div class="pull-right">
			<a href="{{ route('backend.customers.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

<!-- Tabs
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-personal" data-toggle="tab">Personal Details</a></li>
	<li><a href="#tab-address" data-toggle="tab">Address</a></li>
	<li><a href="#tab-contact" data-toggle="tab">Contact Details</a></li>
	<li><a href="#tab-avetmiss1" data-toggle="tab">AvetMiss Part 1</a></li>
	<li><a href="#tab-avetmiss2" data-toggle="tab">AvetMiss Part 2</a></li>
</ul>
 -->
{{ Form::model($customer, array('method' => 'PATCH', 'route' => array('backend.customers.update', $customer->id), 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-personal">

			<div class="control-group {{ $errors->has('last_name') ? 'error' : '' }}">
				<label class="control-label" for="last_name">Family Name: </label>
				<div class="controls">
					{{ Form::text('last_name', $customer->last_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('last_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('first_name') ? 'error' : '' }}">
				<label class="control-label" for="first_name">Given Names: </label>
				<div class="controls">
					{{ Form::text('first_name', $customer->first_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('first_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('dob') ? 'error' : '' }}">
				<label class="control-label" for="dob">Date of Birth (dd/mm/yyyy):: </label>
				<div class="controls">
					{{ Form::text('dob', $customer->dob, array('class'=>'input-medium')) }}
					{{ $errors->first('dob', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('gender') ? 'error' : '' }}">
				<label class="control-label" for="gender">Gender: </label>
				<div class="controls">
					<input type="checkbox" name="gender[]" value="M" {{ $customer->gender == 'M' ? 'checked' : '' }} /> Male | 
					<input type="checkbox" name="gender[]" value="F" {{ $customer->gender == 'F' ? 'checked' : '' }} /> Female 
					{{ $errors->first('gender', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $customer->active == '1' ? 'checked' : '' }} />
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			
		</div>
		<!-- Permissions tab -->
		<div class="tab-pane" id="tab-address">
			
				
			<div class="control-group {{ $errors->has('address') ? 'error' : '' }}">
				<label class="control-label" for="address">Address: </label>
				<div class="controls">
					{{ Form::text('address', $customer->address, array('class'=>'input-xlarge')) }}
					{{ $errors->first('address', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('city') ? 'error' : '' }}">
				<label class="control-label" for="city">City: </label>
				<div class="controls">
					{{ Form::text('city', $customer->city, array('class'=>'input-large')) }}
					{{ $errors->first('city', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('state') ? 'error' : '' }}">
				<label class="control-label" for="state">State: </label>
				<div class="controls">
					{{ Form::text('state', $customer->state, array('class'=>'input-small')) }}
					{{ $errors->first('state', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('post_code') ? 'error' : '' }}">
				<label class="control-label" for="post_code">Post_code: </label>
				<div class="controls">
					{{ Form::text('post_code', $customer->post_code, array('class'=>'input-medium')) }}
					{{ $errors->first('post_code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

	
		</div>

		<!-- Permissions tab -->
		<div class="tab-pane" id="tab-contact">
			
				
			<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
				<label class="control-label" for="email">Email: </label>
				<div class="controls">
					{{ Form::text('email', $customer->email, array('class'=>'input-xlarge')) }}
					{{ $errors->first('email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('phone') ? 'error' : '' }}">
				<label class="control-label" for="phone">Phone: </label>
				<div class="controls">
					{{ Form::text('phone', $customer->phone, array('class'=>'input-large')) }}
					{{ $errors->first('phone', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('mobile') ? 'error' : '' }}">
				<label class="control-label" for="mobile">Mobile: </label>
				<div class="controls">
					{{ Form::text('mobile', $customer->mobile, array('class'=>'input-large')) }}
					{{ $errors->first('mobile', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('fax') ? 'error' : '' }}">
				<label class="control-label" for="fax">Fax: </label>
				<div class="controls">
					{{ Form::text('fax', $customer->fax, array('class'=>'input-large')) }}
					{{ $errors->first('fax', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('mail_out_email') ? 'error' : '' }}">
				<label class="control-label" for="mail_out_email">Mail_out_email: </label>
				<div class="controls">
					<input type="hidden" name="mail_out_email" value="0" /><input type="checkbox" name="mail_out_email" value="1" {{ $customer->mail_out_email == '1' ? 'checked' : '' }} />
					{{ $errors->first('mail_out_email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('mail_out_sms') ? 'error' : '' }}">
				<label class="control-label" for="mail_out_sms">Mail_out_sms: </label>
				<div class="controls">
					<input type="hidden" name="mail_out_sms" value="0" /><input type="checkbox" name="mail_out_sms" value="1" {{ $customer->mail_out_sms == '1' ? 'checked' : '' }} />
					{{ $errors->first('mail_out_sms', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			
		</div>


		<!-- Permissions tab -->
		<div class="tab-pane" id="tab-avetmiss1">
			
				
			<div class="control-group {{ $errors->has('country_of_birth') ? 'error' : '' }}">
				<label class="control-label" for="country_of_birth">Country_of_birth: </label>
				<div class="controls">
					{{ Form::text('country_of_birth', $customer->country_of_birth, array('class'=>'input-large')) }}
					{{ $errors->first('country_of_birth', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('islander_origin') ? 'error' : '' }}">
				<label class="control-label" for="islander_origin">Islander_origin: </label>
				<div class="controls">
					<input type="hidden" name="islander_origin" value="0" /><input type="checkbox" name="islander_origin" value="1" {{ $customer->islander_origin == '1' ? 'checked' : '' }} />
					{{ $errors->first('islander_origin', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('origin') ? 'error' : '' }}">
				<label class="control-label" for="origin">Origin: </label>
				<div class="controls">
					{{ Form::text('origin', $customer->origin, array('class'=>'input-large')) }}
					{{ $errors->first('origin', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_eng') ? 'error' : '' }}">
				<label class="control-label" for="lang_eng">Lang_eng: </label>
				<div class="controls">
					{{ Form::text('lang_eng', $customer->lang_eng, array('class'=>'input-large')) }}
					{{ $errors->first('lang_eng', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_other') ? 'error' : '' }}">
				<label class="control-label" for="lang_other">Lang_other: </label>
				<div class="controls">
					{{ Form::text('lang_other', $customer->lang_other, array('class'=>'input-large')) }}
					{{ $errors->first('lang_other', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_eng_level') ? 'error' : '' }}">
				<label class="control-label" for="lang_eng_level">Lang_eng_level: </label>
				<div class="controls">
					{{ Form::text('lang_eng_level', $customer->lang_eng_level, array('class'=>'input-large')) }}
					{{ $errors->first('lang_eng_level', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('disability') ? 'error' : '' }}">
				<label class="control-label" for="disability">Disability: </label>
				<div class="controls">
					<input type="hidden" name="disability" value="0" /><input type="checkbox" name="disability" value="1" {{ $customer->disability == '1' ? 'checked' : '' }} />
					{{ $errors->first('disability', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('disabilities') ? 'error' : '' }}">
				<label class="control-label" for="disabilities">Disabilities: </label>
				<div class="controls">
					{{ Form::textarea('disabilities', $customer->disabilities, array('class'=>'input-xlarge')) }}
					{{ $errors->first('disabilities', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

		</div>


		<!-- Permissions tab -->
		<div class="tab-pane" id="tab-avetmiss2">

			
				
			<div class="control-group {{ $errors->has('school_level') ? 'error' : '' }}">
				<label class="control-label" for="school_level">School_level: </label>
				<div class="controls">
					{{ Form::text('school_level', $customer->school_level, array('class'=>'input-large')) }}
					{{ $errors->first('school_level', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('school_year') ? 'error' : '' }}">
				<label class="control-label" for="school_year">School_year: </label>
				<div class="controls">
					{{ Form::text('school_year', $customer->school_year, array('class'=>'input-small')) }}
					{{ $errors->first('school_year', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('school_attending') ? 'error' : '' }}">
				<label class="control-label" for="school_attending">School_attending: </label>
				<div class="controls">
					<input type="hidden" name="school_attending" value="0" /><input type="checkbox" name="school_attending" value="1" {{ $customer->school_attending == '1' ? 'checked' : '' }} />
					{{ $errors->first('school_attending', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('qualifications') ? 'error' : '' }}">
				<label class="control-label" for="qualifications">Qualifications: </label>
				<div class="controls">
					{{ Form::textarea('qualifications', $customer->qualifications, array('class'=>'input-xlarge')) }}
					{{ $errors->first('qualifications', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('employment') ? 'error' : '' }}">
				<label class="control-label" for="employment">Employment: </label>
				<div class="controls">
					{{ Form::text('employment', $customer->employment, array('class'=>'input-large')) }}
					{{ $errors->first('employment', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('study_reason') ? 'error' : '' }}">
				<label class="control-label" for="study_reason">Study_reason: </label>
				<div class="controls">
					{{ Form::textarea('study_reason', $customer->study_reason, array('class'=>'input-xlarge')) }}
					{{ $errors->first('study_reason', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

		</div>

	</div>

	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				<a class="btn btn-small" href="{{ route('backend.customers.index') }}">Cancel</a>

				<button type="reset" class="btn btn-small">Reset</button>

				<button type="submit" class="btn btn-small btn-success">Update Customer</button>
			</div>
		</div>
	</div>

{{ Form::close() }}

@stop