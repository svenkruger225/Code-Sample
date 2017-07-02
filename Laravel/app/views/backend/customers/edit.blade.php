@extends('backend/layouts/default')

{{-- Web site Title --}}
@section('title')
Customer Update ::
@parent
@stop

{{-- Content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Customer Update
		<div class="pull-right">
			<a href="{{ route('backend.customers.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-personal" data-toggle="tab">Customer Details</a></li>
	<li><a href="#tab-courses" data-toggle="tab">Courses Details</a></li>
</ul>

{{ Form::model($customer, array('method' => 'PATCH', 'route' => array('backend.customers.update', $customer->id), 'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}
	
	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-personal">

			<div class="control-group">
				<label class="control-label" for="id">Customer ID: </label>
				<div class="controls">
					{{ Form::label('id', $customer->id, array('id' =>'customer_id','class'=>'input-small')) }}
				</div>
			</div>
			@if($customer->user_id !== null && $customer->user_id > 0)
			<div class="control-group">
				<label class="control-label" for="user_name">User Name: </label>
				<div class="controls">
					{{ Form::label('user_name', $customer->user->username, array('class'=>'input-medium')) }}
				</div>
			</div>
			@endif
			<div class="control-group localActivityIndicator">
				<label class="control-label" for="unique_student_identifier">USI: </label>
				<div class="controls">
					{{ Form::text('unique_student_identifier', $customer->unique_student_identifier, array('class'=>'input-small')) }}
					<!--<div class="btn-group">
						<button type="button" title="Click here to verify your USI" class='btn btn-small btn-success' data-bind="click: verifyCustomerUsiCmd">Verify</button> 
						<button type="button" title="Click here to Fill the Form to create your USI" class='btn btn-small btn-primary' data-bind="click: createCustomerUsiCmd">Create</button> 
						<button type="button" title="What is USI" class='btn btn-small btn-info' data-bind="click: displayWhatIsUsiModal">?</button>
					</div>-->
					{{ $errors->first('unique_student_identifier', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('first_name') ? 'error' : '' }}">
				<label class="control-label" for="first_name">Given Names: </label>
				<div class="controls">
					{{ Form::text('first_name', $customer->first_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('first_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('middle_name') ? 'error' : '' }}">
				<label class="control-label" for="middle_name">Middle Name: </label>
				<div class="controls">
					{{ Form::text('middle_name', $customer->middle_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('middle_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('last_name') ? 'error' : '' }}">
				<label class="control-label" for="last_name">Family Name: </label>
				<div class="controls">
					{{ Form::text('last_name', $customer->last_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('last_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('dob') ? 'error' : '' }}">
				<label class="control-label" for="dob">Date of Birth: </label>
				<div class="controls">
					{{ Form::text('dob', $customer->dob, array('class'=>'input-medium dob_field')) }}
					{{ $errors->first('dob', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('gender') ? 'error' : '' }}">
				<label class="control-label" for="gender">Gender: </label>
				<div class="controls">
					<label class="radio inline span2"><input type="radio" name="gender" value="M" {{ $customer->gender == 'M' ? 'checked' : '' }} /> Male </label>
					<label class="radio inline"><input type="radio" name="gender" value="F" {{ $customer->gender == 'F' ? 'checked' : '' }} /> Female </label>
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
								{{ Form::text('address_building_name', $customer->address_building_name, array('id'=>'address_building_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_building_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('address_unit_details') ? 'error' : '' }}">
							<label class="control-label" for="address_unit_details">Unit Details: </label>
							<div class="controls">
								{{ Form::text('address_unit_details', $customer->address_unit_details, array('id'=>'address_unit_details', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_unit_details', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('address_street_number') ? 'error' : '' }}">
							<label class="control-label" for="address_street_number">Street Number: </label>
							<div class="controls">
								{{ Form::text('address_street_number', $customer->address_street_number, array('id'=>'address_street_number', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_street_number', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('address_street_name') ? 'error' : '' }}">
							<label class="control-label" for="address_street_name">Street Name: </label>
							<div class="controls">
								{{ Form::text('address_street_name', $customer->address_street_name, array('id'=>'address_street_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('address_street_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('city') ? 'error' : '' }}">
							<label class="control-label" for="city">Suburb: </label>
							<div class="controls">
								{{ Form::text('city', $customer->city, array('id'=>'city', 'class'=>'input-large')) }}
								{{ $errors->first('city', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('state') ? 'error' : '' }}">
							<label class="control-label" for="state">State: </label>
							<div class="controls">
								{{ Form::select('state', $states, $customer->state, array('id'=>'state', 'class'=>'input-medium')) }}
								{{ $errors->first('state', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('post_code') ? 'error' : '' }}">
							<label class="control-label" for="post_code">Postcode: </label>
							<div class="controls">
								{{ Form::text('post_code', $customer->post_code, array('id'=>'post_code', 'class'=>'input-medium')) }}
								{{ $errors->first('post_code', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
					</div>
			
					<div class="span5">
						<div class="span12 offset1"><div class="pull-left"><h5>Postal Address</h5></div><div class="offset3"><label class="checkbox"><input type="checkbox" id="same_as_residential" /> Same as Residential</label></div></div>
						<div class="control-group {{ $errors->has('postal_address_building_name') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_building_name">Building Name: </label>
							<div class="controls">
								{{ Form::text('postal_address_building_name', $customer->postal_address_building_name, array('id'=>'postal_address_building_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_building_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_address_unit_details') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_unit_details">Unit Details: </label>
							<div class="controls">
								{{ Form::text('postal_address_unit_details', $customer->postal_address_unit_details, array('id'=>'postal_address_unit_details', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_unit_details', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_address_street_number') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_street_number">Street Number: </label>
							<div class="controls">
								{{ Form::text('postal_address_street_number', $customer->postal_address_street_number, array('id'=>'postal_address_street_number', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_street_number', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_address_street_name') ? 'error' : '' }}">
							<label class="control-label" for="postal_address_street_name">Street Name: </label>
							<div class="controls">
								{{ Form::text('postal_address_street_name', $customer->postal_address_street_name, array('id'=>'postal_address_street_name', 'class'=>'input-xlarge')) }}
								{{ $errors->first('postal_address_street_name', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_city') ? 'error' : '' }}">
							<label class="control-label" for="postal_city">Suburb: </label>
							<div class="controls">
								{{ Form::text('postal_city', $customer->postal_city, array('id'=>'postal_city', 'class'=>'input-large')) }}
								{{ $errors->first('postal_city', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_state') ? 'error' : '' }}">
							<label class="control-label" for="postal_state">State: </label>
							<div class="controls">
								{{ Form::select('postal_state', $states, $customer->postal_state, array('id'=>'postal_state', 'class'=>'input-medium')) }}
								{{ $errors->first('postal_state', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
						<div class="control-group {{ $errors->has('postal_post_code') ? 'error' : '' }}">
							<label class="control-label" for="postal_post_code">Postcode: </label>
							<div class="controls">
								{{ Form::text('postal_post_code', $customer->postal_post_code, array('id'=>'postal_post_code', 'class'=>'input-medium')) }}
								{{ $errors->first('postal_post_code', '<span class="help-inline">:message</span>') }}
							</div>
						</div>
					</div>
				</div>
			</div>

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
				<label class="control-label" for="mail_out_email">Authorise Mail Out: </label>
				<div class="controls">
					<label class="checkbox inline span2"><input type="hidden" name="mail_out_email" value="0" /><input type="checkbox" name="mail_out_email" value="1" {{ $customer->mail_out_email == '1' ? 'checked' : '' }} /> via email </label>
					<label class="checkbox inline"><input type="hidden" name="mail_out_sms" value="0" /><input type="checkbox" name="mail_out_sms" value="1" {{ $customer->mail_out_sms == '1' ? 'checked' : '' }} /> via SMS </label>
					{{ $errors->first('mail_out_email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('city_of_birth') ? 'error' : '' }}">
				<label class="control-label" for="city_of_birth">City of Birth: </label>
				<div class="controls">
					{{ Form::text('city_of_birth', $customer->city_of_birth, array('class'=>'input-large')) }}
					{{ $errors->first('city_of_birth', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('country_of_birth') ? 'error' : '' }}">
				<label class="control-label" for="country_of_birth">Country of Birth: </label>
				<div class="controls">
					{{ Form::select('country_of_birth', $countries, $customer->country_of_birth, array('class'=>'input-large')) }}
					{{ $errors->first('country_of_birth', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
				
			<div class="control-group {{ $errors->has('country_of_residence') ? 'error' : '' }}">
				<label class="control-label" for="country_of_residence">Country of Residence: </label>
				<div class="controls">
					{{ Form::select('country_of_residence', $countries, $customer->country_of_residence, array('class'=>'input-large')) }}
					{{ $errors->first('country_of_residence', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_other') ? 'error' : '' }}">
				<label class="control-label" for="lang_other">Other lang at home: </label>
				<div class="controls">
					<label class="radio inline span3"><input type="radio" name="lang_eng" value="1" {{ $customer->lang_eng == '1' ? 'checked' : '' }} /> No, English Only, </label> 
					<label class="radio inline"><input type="radio" name="lang_eng" value="0" {{ $customer->lang_eng != '1' ? 'checked' : '' }} /> Yes, If Yes what language: </label> 
					{{ Form::select('lang_other', $languages, $customer->lang_other, array('class'=>'input-large')) }}
					{{ $errors->first('lang_other', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('lang_eng_level') ? 'error' : '' }}">
				<label class="control-label" for="lang_eng_level">English How Well?: </label>
				<div class="controls">
					<label class="radio inline span3"><input type="radio" name="lang_eng_level" value="Very well" {{ $customer->lang_eng_level == 'Very well' ? 'checked' : '' }} /> Very well  </label>
					<label class="radio inline span3"><input type="radio" name="lang_eng_level" value="Well" {{ $customer->lang_eng_level == 'Well' ? 'checked' : '' }} /> Well </label>
					<label class="radio inline span3"><input type="radio" name="lang_eng_level" value="Not Well" {{ $customer->lang_eng_level == 'Not Well' ? 'checked' : '' }} /> Not Well </label>
					<label class="radio inline"><input type="radio" name="lang_eng_level" value="Not at all" {{ $customer->lang_eng_level == 'Not at all' ? 'checked' : '' }} /> Not at all </label>
					{{ $errors->first('lang_eng_level', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('islander_origin') ? 'error' : '' }}">
				<label class="control-label" for="islander_origin">Aboriginal / Islander origin: </label>
				<div class="controls">
					<label class="checkbox inline span3"><input type="checkbox" name="origin" value="Aboriginal" {{ $customer->origin == 'Aboriginal' ? 'checked' : '' }} /> Yes, Aboriginal  </label>
					<label class="radio inline span3"><input type="radio" name="islander_origin" value="1" {{ $customer->islander_origin == '1' ? 'checked' : '' }} /> Yes, Torres Strait Islander  </label>
					<label class="radio inline"><input type="radio" name="islander_origin" value="0" {{ $customer->islander_origin == '0' ? 'checked' : '' }} /> No, Not Aboriginal not Torres Strait Islander</label>
				</div>
				<div class="controls">
					<div class="help-block span10">(For persons of both Aboriginal and Torres Strait Islander origin, mark both `Yes' boxes.)</div>
					{{ $errors->first('islander_origin', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('disability') ? 'error' : '' }}">
				<label class="control-label" for="disability">Disability: </label>
				<div class="controls">
					<label class="radio inline span3"><input type="radio" name="disability" value="1" {{ $customer->disability == '1' ? 'checked' : '' }} /> Yes,  </label>
					<label class="radio inline"><input type="radio" name="disability" value="0" {{ $customer->disability == '0' ? 'checked' : '' }} /> No,  </label>
					{{ $errors->first('disability', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('disabilities') ? 'error' : '' }}">
				<label class="control-label" for="disabilities">If Yes, Disabilities: </label>
				@foreach(array_chunk($disabilities_list, 3) as $group3)
				<div class="controls">
					@foreach($group3 as $key => $disability)
						<label class="checkbox inline {{$key < 2 ? 'span3' : '' }}"><input type="checkbox" name="disabilities[]" value="{{$disability['id']}}" {{ (in_array($disability['id'], $customer->disabilities))  ? 'checked' : '' }} /> {{$disability['name']}} 
						@if($disability['name'] == 'Other')
							{{ Form::text('disabilities_other', $customer->disabilities_other, array('class'=>'input-large')) }}
						@endif
						</label>
					@endforeach				
				</div>
				@endforeach				
			</div>
				
			<div class="control-group {{ $errors->has('school_level') ? 'error' : '' }}">
				<label class="control-label" for="school_level">Highest School Level: </label>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="school_level" value="Year 12 or equivalent" {{ ($customer->school_level == "Year 12 or equivalent") ? 'checked' : '' }} /> Year 12 or equivalent  </label>
					<label class="radio inline span4"><input type="radio" name="school_level" value="Year 11 or equivalent" {{ ($customer->school_level == "Year 11 or equivalent") ? 'checked' : '' }} /> Year 11 or equivalent  </label>
					<label class="radio inline"><input type="radio" name="school_level" value="Year 10 or equivalent" {{ ($customer->school_level == "Year 10 or equivalent") ? 'checked' : '' }} /> Year 10 or equivalent  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="school_level" value="Year 9 or equivalent" {{ ($customer->school_level == "Year 9 or equivalent") ? 'checked' : '' }} /> Year 9 or equivalent  </label>
					<label class="radio inline span4"><input type="radio" name="school_level" value="Year 8 or below" {{ ($customer->school_level == "Year 8 or below") ? 'checked' : '' }} /> Year 8 or below  </label>
					<label class="radio inline"><input type="radio" name="school_level" value="Never attended school" {{ ($customer->school_level == 'Never attended school') ? 'checked' : '' }} /> Never attended school  </label>
					{{ $errors->first('school_level', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('school_year') ? 'error' : '' }}">
				<label class="control-label" for="school_year">Year complete level : </label>
				<div class="controls">
					{{ Form::text('school_year', $customer->school_year, array('class'=>'input-small')) }}
					{{ $errors->first('school_year', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('school_attending') ? 'error' : '' }}">
				<label class="control-label" for="school_attending">School_attending: </label>
				<div class="controls">
					<label class="radio inline span1"><input type="radio" name="school_attending" value="1" {{ $customer->school_attending == '1' ? 'checked' : '' }} /> Yes </label>
					<label class="radio inline"><input type="radio" name="school_attending" value="0" {{ $customer->school_attending == '0' ? 'checked' : '' }}  /> No </label>
					{{ $errors->first('school_attending', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('achievements') ? 'error' : '' }}">
				<label class="control-label" for="achievements">Achievements: </label>
				@foreach(array_chunk($achievements_list, 2) as $group)
				<div class="controls">
					@foreach($group as $key => $achievement)
						<label class="checkbox inline  {{$key == 0 ? 'span4' : '' }}"><input type="checkbox" name="achievements[]" value="{{$achievement['id']}}" {{ (in_array($achievement['id'] ,$customer->achievements)) ? 'checked' : '' }} /> {{$achievement['name']}} </label>
					@endforeach				
				</div>
				@endforeach				
			</div>

			<div class="control-group {{ $errors->has('employment') ? 'error' : '' }}">
				<label class="control-label" for="employment">Employment: </label>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Employer" {{ ($customer->employment == 'Employer') ? 'checked' : '' }} /> Employer  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Unemployed - seeking full-time work" {{ ($customer->employment == 'Unemployed - seeking full-time work') ? 'checked' : '' }} /> Unemployed - seeking full-time work  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Part-time employee" {{ ($customer->employment == 'Part-time employee') ? 'checked' : '' }} /> Part-time employee  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Unemployed - seeking part-time work" {{ ($customer->employment == 'Unemployed - seeking part-time work') ? 'checked' : '' }} /> Unemployed - seeking part-time work  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Full-time employee" {{ ($customer->employment == 'Full-time employee') ? 'checked' : '' }} /> Full-time employee  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Not employed - not seeking employment" {{ ($customer->employment == 'Not employed - not seeking employment') ? 'checked' : '' }} /> Not employed - not seeking employment  </label>
				</div>
				<div class="controls">
					<label class="radio inline span4"><input type="radio" name="employment" value="Self employed - not employing others" {{ ($customer->employment == 'Self employed - not employing others') ? 'checked' : '' }} /> Self employed - not employing others  </label>
					<label class="radio inline"><input type="radio" name="employment" value="Employed - unpaid worker in a family business" {{ ($customer->employment == 'Employed - unpaid worker in a family business') ? 'checked' : '' }} /> Employed - unpaid worker in a family business  </label>
					{{ $errors->first('employment', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('study_reason') ? 'error' : '' }}">
				<label class="control-label" for="study_reason">Study_reason: </label>
				@foreach(array_chunk($study_reasons_list, 2) as $group22)
				<div class="controls">
					@foreach($group22 as $key => $reason)
						<label class="radio inline {{$key == 0 ? 'span4' : '' }}"><input type="radio" name="study_reason" value="{{$reason['id']}}" {{ ($reason['id'] == $customer->study_reason)  ? 'checked' : '' }} /> {{$reason['name']}} </label>
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
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $customer->active == '1' ? 'checked' : '' }} />
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
				</div>
			</div>	
			<div class="control-group {{ $errors->has('usi_verified') ? 'error' : '' }}">
				<label class="control-label" for="usi_verified">USI verified: </label>
				<div class="controls">
					<input type="hidden" name="usi_verified" value="0" /><input type="checkbox" name="usi_verified" value="1" {{ $customer->usi_verified == '1' ? 'checked' : '' }} />
					{{ $errors->first('usi_verified', '<span class="help-inline">:message</span>') }}
				</div>
			</div>	
			<div class="control-group {{ $errors->has('avetmiss_done') ? 'error' : '' }}">
				<label class="control-label" for="avetmiss_done">Avetmiss Done: </label>
				<div class="controls">
					<input type="hidden" name="avetmiss_done" value="0" /><input type="checkbox" name="avetmiss_done" value="1" {{ $customer->avetmiss_done == '1' ? 'checked' : '' }} />
					{{ $errors->first('avetmiss_done', '<span class="help-inline">:message</span>') }}
				</div>
			</div>	
			<div class="control-group">
				<label class="control-label" for="id">Created At: </label>
				<div class="controls">
					{{ Form::label('created_at', $customer->created_at, array('class'=>'input-large')) }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="id">Updated At: </label>
				<div class="controls">
					{{ Form::label('updated_at', $customer->updated_at, array('class'=>'input-large')) }}
				</div>
			</div>
			<!--<div class="control-group">
				<label class="control-label" for=""></label>
				<div class="controls">
					<a id="verify_data" href="#" class="btn btn-warning" data-bind="click: openCloseUsiData">Open USI Data</a>
					
					<button type="button" title="Click here to create USI" class='btn btn-primary' data-bind="click: createCustomerUsiCmd">Create USI</button> 

				</div>
			</div> -->	
			
			<div id="usi_data_info" class="hide">
				<div class="control-group">
					<label class="control-label" for="">Method of Contact: </label>
					<div class="controls">
						<label class="radio inline"><input type="radio" name="preferred_method" value="Mobile" /> Mobile</label>
						<label class="radio inline"><input type="radio" name="preferred_method" value="Email" />Email</label>
						<label class="radio inline"><input type="radio" name="preferred_method" value="Mail" />Mail</label>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="city_of_birth">City of Birth: </label>
					<div class="controls">
						<input type="text" name='city_of_birth' value="" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="country_of_birth">Country of Birth: </label>
					<div class="controls">
						{{ Form::select('country_of_birth', $countries, '', array('id' => 'country_of_birth','class'=>'input-large')) }}
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="country_of_residence">Country of Residence: </label>
					<div class="controls">
						{{ Form::select('country_of_residence', $countries, '', array('id' => 'country_of_residence','class'=>'input-large')) }}
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="active">Select Document Type: <br>Only if creating USI </label>
					<div class="controls">
						<input type="hidden" name="DvsDocumentType" />
						<ul class="nav nav-pills nav-stacked span2">
							<li role="presentation"><a data-toggle="pill" href="#birth_certificate" data-bind="click: setCustomerDocumentType.bind($data, 'BirthCertificate')">Birth Certificate</a></li>
							<li role="presentation"><a data-toggle="pill" href="#passport" data-bind="click: setCustomerDocumentType.bind($data, 'Passport')">Passport</a></li>
							<li role="presentation"><a data-toggle="pill" href="#drivers_licence" data-bind="click: setCustomerDocumentType.bind($data, 'DriversLicence')">Drivers Licenc</a></li>
						</ul>
						<div class="tab-content span8">
							<div id="birth_certificate" class="tab-pane fade">
								<div class="row-fluid">
									<div class="span2">Certificate Number: </div><div class="span10"><input type="text" name="CertificateNumber" class="input-medium" /></div>
								</div>		
								<div class="row-fluid">
									<div class="span2">Date Printed: </div><div class="span10"><input type="text" name="DatePrinted" class="input-medium" /></div>
								</div>
								<div class="row-fluid">
									<div class="span2">Registration Date: </div><div class="span10"><input type="text" name="RegistrationDate" class="input-medium" /></div>
								</div>
								<div class="row-fluid">
									<div class="span2">Registration Number: </div><div class="span10"><input type="text" name="RegistrationNumber" class="input-medium" /></div>
								</div>
								<div class="row-fluid">
									<div class="span2">Registration State: </div><div class="span10"><input type="text" name="RegistrationState" class="input-medium" /></div>
								</div>		
								<div class="row-fluid">
									<div class="span2">Registration Year: </div><div class="span10"><input type="text" name="RegistrationYear" class="input-small" /></div>
								</div>
							</div>
							<div id="passport" class="tab-pane fade">
								<div class="row-fluid">
									<div class="span2">Document Number: </div><div class="span10"><input type="text" name="DocumentNumber" class="input-medium"/></div>
								</div>
							</div>
							<div id="drivers_licence" class="tab-pane fade">
								<div class="row-fluid">
									<div class="span2">Licence Number: </div><div class="span10"><input type="text" name="LicenceNumber" class="input-medium" /></div>
								</div>
								<div class="row-fluid">
									<div class="span2">State: </div><div class="span10"><input type="text" name="LicenceState" class="input-small" /></div>
								</div>
							</div>
						</div><!-- tab content -->
					</div><!-- controls -->
				</div>
			
			</div>
		
			
			
			
					
		</div>
		
		<!-- Courses tab -->
		<div class="tab-pane span12" id="tab-courses">

		@if (count($customer->rosters) > 0)
			<h3>Coffee School Certificates</h3>
			<table class="table table-bordered table-striped table-condensed table-hover">
				<thead>
					<tr>
						<th class="span1">Certificate Id</th>
						<th class="span2">Course Name</th>
						<th class="span1">Course Date/Time</th>
						<th class="span1">Re-Assessed?</th>
						<th class="span1">ReAssessed Date</th>
						<th class="span1">Certificate Date</th>
						<th class="span3">Notes Admin</th>
						<th class="span3">Notes Class</th>
						<th class="span1"></th>
					</tr>
				</thead>

				<tbody>
					@foreach ($customer->rosters as $roster)
						<tr>
							<td>{{ $roster->certificate_id }}</td>
							<td>{{ $roster->course_instance_id ? 
									($roster->instance && $roster->instance->course ? $roster->instance->course->name : 'n/a') : 
									($roster->groupbooking && $roster->groupbooking->course ? $roster->groupbooking->course->name : 'n/a') }}</td>
							<td>{{ $roster->course_instance_id ? 
									($roster->instance && $roster->instance->course ? $roster->instance->course_date_time : 'n/a') : 
									($roster->groupbooking && $roster->groupbooking->course ? $roster->groupbooking->course_date_time : 'n/a')  }}</td>
							<td>{{ $roster->reassessed == '1' ? 'x' : ''  }}</td>
							<td>{{ $roster->reassessed_date }}</td>
							<td>{{ $roster->certificate_id ? $roster->certificate->certificate_date : ''}}</td>
							<td class="td-wrap">{{ $roster->notes_admin }}</td>
							<td class="td-wrap">{{ $roster->notes_class }}</td>
							<td>
								@if ($roster->certificate_id)
								<a class="btn btn-small btn-warning" href="/api/certificates/downloadCertificate/{{$roster->certificate_id}}">Download</a> 
								<a class="btn btn-small btn-primary" href="/api/certificates/viewCertificate/{{$roster->certificate_id}}" target="_blank">View</a>
								@endif
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

		@endif
		@if (count($customer->documents) > 0)
			<hr />
			<h3>External Documents</h3>
			<table class="table table-bordered table-striped table-condensed table-hover">
				<thead>
					<tr>
						<th class="span1">Document Id</th>
						<th class="span1">Document Type</th>
						<th class="span2">Description</th>
						<th class="span3">Course Name</th>
						<th class="span1">Certificate Date</th>
						<th class="span2"></th>
					</tr>
				</thead>

				<tbody>
					@foreach ($customer->documents as $document)
						<tr>
							<td>{{ $document->id }}</td>
							<td>{{ $document->document_type  }}</td>
							<td>{{ $document->description  }}</td>
							<td>{{ $document->course ? $document->course->name : $document->course_id }}</td>
							<td>{{ $document->certificate_date}}</td>
							<td>
								<a class="btn btn-small btn-warning" href="/api/documents/downloadExternalDocument/{{$document->document_file}}">Download</a> 
								<a class="btn btn-small btn-primary" href="/api/documents/viewExternalDocument/{{$document->document_file}}" target="_blank">View</a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>

		@endif
		<a href="#" class="btn btn-small btn-warning" data-bind="click: openUploadDocumentForm.bind($data, '{{$customer->id}}')" title="Upload External Document"><i class="icon-upload icon-white"></i> Upload External Document</a>
		@if (count($customer->rosters) == 0 && count($customer->documents) == 0)
			There are no Certificates
		@endif

		
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
@include('backend/calendar/external-document')
@include('bookings/common/whatis-usi')
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/customers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop