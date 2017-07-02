@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Create Customer ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Create Customer
		<div class="pull-right">
			<a href="{{ route('backend.customers.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

{{ Form::open(array('route' => 'backend.customers.store', 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-personal">

			<div class="control-group">
				<label class="control-label" for="unique_student_identifier">USI: </label>
				<div class="controls">
					{{ Form::text('unique_student_identifier', Input::old('unique_student_identifier'), array('class'=>'input-small')) }}
					<div class="btn-group">
						<button type="button" title="Click here to Fill the Form to create your USI" class='btn btn-medium btn-primary' data-bind="click: displayUsiRegistrationForm">Create</button> 
						<button type="button" title="What is USI" class='btn btn-medium btn-info' data-bind="click: displayWhatIsUsiModal">?</button>
					</div>
					{{ $errors->first('unique_student_identifier', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('first_name') ? 'error' : '' }}">
				<label class="control-label" for="first_name">Given Names: </label>
				<div class="controls">
					{{ Form::text('first_name', Input::old('first_name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('first_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('middle_name') ? 'error' : '' }}">
				<label class="control-label" for="middle_name">Middle Name: </label>
				<div class="controls">
					{{ Form::text('middle_name', Input::old('middle_name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('middle_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('last_name') ? 'error' : '' }}">
				<label class="control-label" for="last_name">Family Name: </label>
				<div class="controls">
					{{ Form::text('last_name', Input::old('last_name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('last_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('dob') ? 'error' : '' }}">
				<label class="control-label" for="dob">Date of Birth (dd/mm/yyyy):: </label>
				<div class="controls">
					{{ Form::text('dob', Input::old('dob'), array('class'=>'input-medium')) }}
					{{ $errors->first('dob', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('gender') ? 'error' : '' }}">
				<label class="control-label" for="gender">Gender: </label>
				<div class="controls">
					<label class="radio inline span1"><input type="radio" name="gender" value="M" {{ Input::old('gender') == 'M' ? 'checked' : '' }} /> Male </label>
					<label class="radio inline"><input type="radio" name="gender" value="F" {{ Input::old('gender') == 'F' ? 'checked' : '' }} /> Female </label>
					{{ $errors->first('gender', '<span class="help-inline">:message</span>') }}
				</div>
			</div>


			<div class="row-fluid">	
				<div class="span12">	
					<div class="span5">
						<div class="span12 offset1"><h5>Residential Address</h5></div>
						<div class="control-group {{ $errors->has('address_building_name') ? 'error' : '' }}">
							<label class="control-label" for="address_building_name">Building Name: </label>
							<div class="controls">
								{{ Form::text('address_building_name', Input::old('address_building_name'), array('id'=>'address_building_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_building_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('address_unit_details') ? 'error' : '' }}">
							<label class="control-label" for="address_unit_details">Unit Details: </label>
							<div class="controls">
								{{ Form::text('address_unit_details', Input::old('address_unit_details'), array('id'=>'address_unit_details', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_unit_details', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('address_street_number') ? 'error' : '' }}">
							<label class="control-label" for="address_street_number">Street Number: </label>
							<div class="controls">
								{{ Form::text('address_street_number', Input::old('address_street_number'), array('id'=>'address_street_number', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_street_number', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('address_street_name') ? 'error' : '' }}">
							<label class="control-label" for="address_street_name">Street Name: </label>
							<div class="controls">
								{{ Form::text('address_street_name', Input::old('address_street_name'), array('id'=>'address_street_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_street_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('city') ? 'error' : '' }}">
							<label class="control-label" for="city">Suburb: </label>
							<div class="controls">
								{{ Form::text('city', Input::old('city'), array('id'=>'city', 'class'=>'input-large')) }}
								{{ $errors->first('city', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('state') ? 'error' : '' }}">
							<label class="control-label" for="state">State: </label>
							<div class="controls">
								{{ Form::select('state', $states, Input::old('state'), array('id'=>'state', 'class'=>'input-medium')) }}
								{{ $errors->first('state', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('post_code') ? 'error' : '' }}">
							<label class="control-label" for="post_code">Postcode: </label>
							<div class="controls">
								{{ Form::text('post_code', Input::old('post_code'), array('id'=>'post_code', 'class'=>'input-medium')) }}
								{{ $errors->first('post_code', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
					</div>
			
					<div class="span5">
						<div class="span12 offset1"><div class="pull-left"><h5>Postal Address</h5></div><div class="offset3"><label class="checkbox"><input type="checkbox" id="same_as_residential" /> Same as Residential</label></div></div>
						<div class="control-group {{ $errors->has('postal_address_building_name') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_building_name">Building Name: </label>
							<div class="controls">
								{{ Form::text('postal_address_building_name', Input::old('postal_address_building_name'), array('id'=>'postal_address_building_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_building_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_address_unit_details') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_unit_details">Unit Details: </label>
							<div class="controls">
								{{ Form::text('postal_address_unit_details', Input::old('postal_address_unit_details'), array('id'=>'postal_address_unit_details', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_unit_details', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_address_street_number') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_street_number">Street Number: </label>
							<div class="controls">
								{{ Form::text('postal_address_street_number', Input::old('postal_address_street_number'), array('id'=>'postal_address_street_number', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_street_number', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_address_street_name') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_street_name">Street Name: </label>
							<div class="controls">
								{{ Form::text('postal_address_street_name', Input::old('postal_address_street_name'), array('id'=>'postal_address_street_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_street_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_city') ? 'error' : '' }}">
							<label class="control-label" for="postal_city">Suburb: </label>
							<div class="controls">
								{{ Form::text('postal_city', Input::old('postal_city'), array('id'=>'postal_city', 'class'=>'input-large')) }}
								{{ $errors->first('postal_city', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_state') ? 'error' : '' }}">
							<label class="control-label" for="postal_state">State: </label>
							<div class="controls">
								{{ Form::select('postal_state', $states, Input::old('postal_state'), array('id'=>'postal_state', 'class'=>'input-medium')) }}
								{{ $errors->first('postal_state', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_post_code') ? 'error' : '' }}">
							<label class="control-label" for="postal_post_code">Postcode: </label>
							<div class="controls">
								{{ Form::text('postal_post_code', Input::old('postal_post_code'), array('id'=>'postal_post_code', 'class'=>'input-medium')) }}
								{{ $errors->first('postal_post_code', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
					</div>
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
				<label class="control-label" for="email">Email: </label>
				<div class="controls">
					{{ Form::text('email', Input::old('email'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('phone') ? 'error' : '' }}">
				<label class="control-label" for="phone">Phone: </label>
				<div class="controls">
					{{ Form::text('phone', Input::old('phone'), array('class'=>'input-large')) }}
					{{ $errors->first('phone', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('mobile') ? 'error' : '' }}">
				<label class="control-label" for="mobile">Mobile: </label>
				<div class="controls">
					{{ Form::text('mobile', Input::old('mobile'), array('class'=>'input-large')) }}
					{{ $errors->first('mobile', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('fax') ? 'error' : '' }}">
				<label class="control-label" for="fax">Fax: </label>
				<div class="controls">
					{{ Form::text('fax', Input::old('fax'), array('class'=>'input-large')) }}
					{{ $errors->first('fax', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('mail_out_email') ? 'error' : '' }}">
				<label class="control-label" for="mail_out_email">Authorise Mail Out: </label>
				<div class="controls">
					<label class="checkbox inline span1"><input type="hidden" name="mail_out_email" value="0" /><input type="checkbox" name="mail_out_email" value="1" {{ Input::old('mail_out_email') == '1' ? 'checked' : '' }} /> via email </label>
					<label class="checkbox inline"><input type="hidden" name="mail_out_sms" value="0" /><input type="checkbox" name="mail_out_sms" value="1" {{ Input::old('mail_out_sms') == '1' ? 'checked' : '' }} /> via SMS </label>
					{{ $errors->first('mail_out_email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('country_of_birth') ? 'error' : '' }}">
				<label class="control-label" for="country_of_birth">Country of Birth: </label>
				<div class="controls">
					{{ Form::select('country_of_birth', $countries, Input::old('country_of_birth'), array('class'=>'input-large')) }}
					{{ $errors->first('country_of_birth', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_other') ? 'error' : '' }}">
				<label class="control-label" for="lang_other">Other lang at home: </label>
				<div class="controls">
					<label class="radio inline span2"><input type="radio" name="lang_eng" value="1" {{ Input::old('lang_eng') == '1' ? 'checked' : '' }} /> No, English Only, </label> 
					<label class="radio inline"><input type="radio" name="lang_eng" value="0" {{ Input::old('lang_eng') != '1' ? 'checked' : '' }} /> Yes, If Yes what language: </label> 
					{{ Form::select('lang_other', $languages, Input::old('lang_other'), array('class'=>'input-large')) }}
					{{ $errors->first('lang_other', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_eng_level') ? 'error' : '' }}">
				<label class="control-label" for="lang_eng_level">English How Well?: </label>
				<div class="controls">
					<label class="radio inline span1"><input type="radio" name="lang_eng_level" value="Very well" {{ Input::old('lang_eng_level') == 'Very well' ? 'checked' : '' }} /> Very well  </label>
					<label class="radio inline span1"><input type="radio" name="lang_eng_level" value="Well" {{ Input::old('lang_eng_level') == 'Well' ? 'checked' : '' }} /> Well </label>
					<label class="radio inline span1"><input type="radio" name="lang_eng_level" value="Not Well" {{ Input::old('lang_eng_level') == 'Not Well' ? 'checked' : '' }} /> Not Well </label>
					<label class="radio inline"><input type="radio" name="lang_eng_level" value="Not at all" {{ Input::old('lang_eng_level') == 'Not at all' ? 'checked' : '' }} /> Not at all </label>
					{{ $errors->first('lang_eng_level', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('islander_origin') ? 'error' : '' }}">
				<label class="control-label" for="islander_origin">Aboriginal / Islander origin: </label>
				<div class="controls">
					<label class="checkbox inline span2"><input type="checkbox" name="origin" value="Aboriginal" {{ Input::old('origin') == 'Aboriginal' ? 'checked' : '' }} /> Yes, Aboriginal  </label>
					<label class="radio inline span2"><input type="radio" name="islander_origin" value="1" {{ Input::old('islander_origin') == '1' ? 'checked' : '' }} /> Yes, Torres Strait Islander  </label>
					<label class="radio inline span4"><input type="radio" name="islander_origin" value="0" {{ Input::old('islander_origin') == '0' ? 'checked' : '' }} /> No, Not Aboriginal, not Torres Strait Islander</label>
					{{ $errors->first('islander_origin', '<span class="help-inline">:message</span>') }}
					<span class="help-block">(For persons of both Aboriginal and Torres Strait Islander origin, mark both `Yes' boxes.)</span>
				</div>
			</div>

			<div class="control-group {{ $errors->has('disability') ? 'error' : '' }}">
				<label class="control-label" for="disability">Disability: </label>
				<div class="controls">
					<label class="radio inline span1"><input type="radio" name="disability" value="1" {{ Input::old('disability') == '1' ? 'checked' : '' }} /> Yes,  </label>
					<label class="radio inline"><input type="radio" name="disability" value="0" {{ Input::old('disability') == '0' ? 'checked' : '' }} /> No,  </label>
					{{ $errors->first('disability', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('disabilities') ? 'error' : '' }}">
				<label class="control-label" for="disabilities">If Yes, Disabilities: </label>
				@foreach(array_chunk($disabilities_list, 3) as $group)
				<div class="controls">
					@foreach($group as $key => $disability)
						<label class="checkbox inline span2"><input type="checkbox" name="disabilities[]" value="{{$disability['id']}}" {{ (in_array($disability['id'], Input::old('disabilities', array())))  ? 'checked' : '' }} /> {{$disability['name']}} 
						@if($disability['name'] == 'Other')
							{{ Form::text('disabilities_other', Input::old('disabilities_other'), array('class'=>'input-large')) }}
						@endif
						</label>
					@endforeach				
				</div>
				@endforeach				
			</div>
				
			<div class="control-group {{ $errors->has('school_level') ? 'error' : '' }}">
				<label class="control-label" for="school_level">Highest School Level: </label>
				<div class="controls">
					<label class="radio inline span2"><input type="radio" name="school_level" value="Year 12 or equivalent" {{ (Input::old('school_level') == "Year 12 or equivalent") ? 'checked' : '' }} /> Year 12 or equivalent  </label>
					<label class="radio inline span2"><input type="radio" name="school_level" value="Year 11 or equivalent" {{ (Input::old('school_level') == "Year 11 or equivalent") ? 'checked' : '' }} /> Year 11 or equivalent  </label>
					<label class="radio inline"><input type="radio" name="school_level" value="Year 10 or equivalent" {{ (Input::old('school_level') == "Year 10 or equivalent") ? 'checked' : '' }} /> Year 10 or equivalent  </label>
				</div>
				<div class="controls">
					<label class="radio inline span2"><input type="radio" name="school_level" value="Year 9 or equivalent" {{ (Input::old('school_level') == "Year 9 or equivalent") ? 'checked' : '' }} /> Year 9 or equivalent  </label>
					<label class="radio inline span2"><input type="radio" name="school_level" value="Year 8 or below" {{ (Input::old('school_level') == "Year 8 or below") ? 'checked' : '' }} /> Year 8 or below  </label>
					<label class="radio inline"><input type="radio" name="school_level" value="Never attended school" {{ (Input::old('school_level') == 'Never attended school') ? 'checked' : '' }} /> Never attended school  </label>
					{{ $errors->first('school_level', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('school_year') ? 'error' : '' }}">
				<label class="control-label" for="school_year">Year complete level : </label>
				<div class="controls">
					{{ Form::text('school_year', Input::old('school_year'), array('class'=>'input-small')) }}
					{{ $errors->first('school_year', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('school_attending') ? 'error' : '' }}">
				<label class="control-label" for="school_attending">School_attending: </label>
				<div class="controls">
					<label class="radio inline span1"><input type="radio" name="school_attending" value="1" {{ Input::old('school_attending') == '1' ? 'checked' : '' }} /> Yes </label>
					<label class="radio inline"><input type="radio" name="school_attending" value="0" {{ Input::old('school_attending') == '0' ? 'checked' : '' }}  /> No </label>
					{{ $errors->first('school_attending', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('achievements') ? 'error' : '' }}">
				<label class="control-label" for="achievements">Achievements: </label>
				@foreach(array_chunk($achievements_list, 2) as $group)
				<div class="controls">
					@foreach($group as $key => $achievement)
						<label class="checkbox inline span4"><input type="checkbox" name="achievements[]" value="{{$achievement['id']}}" {{ (in_array($achievement['id'] ,Input::old('achievements', array()))) ? 'checked' : '' }} /> {{$achievement['name']}} </label>
					@endforeach				
				</div>
				@endforeach				
			</div>

			<div class="control-group {{ $errors->has('employment') ? 'error' : '' }}">
				<label class="control-label" for="employment">Employment: </label>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Employer" {{ (Input::old('employment') == 'Employer') ? 'checked' : '' }} /> Employer  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Unemployed - seeking full-time work" {{ (Input::old('employment') == 'Unemployed - seeking full-time work') ? 'checked' : '' }} /> Unemployed - seeking full-time work  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Part-time employee" {{ (Input::old('employment') == 'Part-time employee') ? 'checked' : '' }} /> Part-time employee  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Unemployed - seeking part-time work" {{ (Input::old('employment') == 'Unemployed - seeking part-time work') ? 'checked' : '' }} /> Unemployed - seeking part-time work  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Full-time employee" {{ (Input::old('employment') == 'Full-time employee') ? 'checked' : '' }} /> Full-time employee  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Not employed - not seeking employment" {{ (Input::old('employment') == 'Not employed - not seeking employment') ? 'checked' : '' }} /> Not employed - not seeking employment  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Self employed - not employing others" {{ (Input::old('employment') == 'Self employed - not employing others') ? 'checked' : '' }} /> Self employed - not employing others  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Employed - unpaid worker in a family business" {{ (Input::old('employment') == 'Employed - unpaid worker in a family business') ? 'checked' : '' }} /> Employed - unpaid worker in a family business  </label>
					{{ $errors->first('employment', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('study_reason') ? 'error' : '' }}">
				<label class="control-label" for="study_reason">Study_reason: </label>
				@foreach(array_chunk($study_reasons_list, 2) as $group22)
				<div class="controls">
					@foreach($group22 as $key => $reason)
						<label class="radio inline span4"><input type="radio" name="study_reason" value="{{$reason['id']}}" {{ ($reason['id'] == Input::old('study_reason'))  ? 'checked' : '' }} /> {{$reason['name']}} 
						</label>
					@endforeach				
				</div>
				@endforeach				
				<div class="controls">
					{{ $errors->first('study_reason', '<span class="help-inline">:message</span>') }}
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
				<a class="btn btn-small" href="{{ route('backend.customers.index') }}">Cancel</a>

				<button type="reset" class="btn btn-small">Reset</button>

				<button type="submit" class="btn btn-small btn-success">Create Customer</button>
			</div>
		</div>
	</div>


{{ Form::close() }}
@include('bookings/common/whatis-usi')
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/customers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
