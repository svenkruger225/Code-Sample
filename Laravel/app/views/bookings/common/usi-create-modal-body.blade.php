        <div class="modal-body container-fluid" data-bind="css: { hide: usicreate().is_course_accredited() == false }">	

			<div class="row-fluid localActivityIndicator" data-bind="with: usicreate">
				<ul id="usi-create-tabs" class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#contact_details" data-bind="click: $root.navigateToTab.bind($data, 0), clickBubble: false">1. Contact Details</a></li>
					<li><a data-toggle="tab" href="#address_details" data-bind="click: $root.navigateToTab.bind($data, 1), clickBubble: false">2. Address Details</a></li>
					<li><a data-toggle="tab" href="#lang_details" data-bind="click: $root.navigateToTab.bind($data, 2), clickBubble: false">3. Language Disabilities</a></li>
					<li><a data-toggle="tab" href="#education_details" data-bind="click: $root.navigateToTab.bind($data, 3), clickBubble: false">4. Education</a></li>
					<li><a data-toggle="tab" href="#employment_details" data-bind="click: $root.navigateToTab.bind($data, 4), clickBubble: false">5. Employment</a></li>
					<!--
					<li data-bind="css: { hide: verify_usi() }"><a data-toggle="tab" href="#document_details" data-bind="click: $root.navigateToTab.bind($data, 5), clickBubble: false">6. Document</a></li>
					-->
					<div class="pull-right"><button type="button" class="btn btn-mini btn-warning" data-bind="click: $root.openHelper" class="pull-right" title="Click here for help"><i class="icon-white icon-question-sign"></i> Help</button></div>
				</ul>
				<div class="tab-content">
					<div id="contact_details" class="tab-pane fade in active">			
						<div class="row-fluid form-line">
							<div class="span2">USI (Optional): </div>
							<div class="span10">
							<input type="text" id="usi" class='input-medium' name="usi" data-bind="value: usi"/> 
							Enter your USI here if you already have one or LEAVE IT BLANK.
							</div>
						</div>
						<div class="row-fluid form-line">
							<div class="span2">Title: </div>
							<div class="span10">{{Form::select('title', $titles, '', array('id'=>'title', 'class'=>'input-medium', 'data-bind'=>"value: title"))}}</div>
						</div>
						
						<div class="row-fluid form-line">
							<div class="span2">First Name: </div>
							<div class="span10"><input type="text" id="first_name" name="first_name" class="input-xlarge" data-bind="value: first_name" /></div>
						</div>
				
						<div class="row-fluid form-line">
							<div class="span2">Middle Name: </div>
							<div class="span10"><input type="text" id="middle_name" name="middle_name" class="input-xlarge" data-bind="value: middle_name" /></div>
						</div>

						<div class="row-fluid form-line">
							<div class="span2">Family Name: </div>
							<div class="span10"><input type="text" id="last_name" name="last_name" class="input-xlarge" data-bind="value: last_name" /></div>
						</div>
				
						<div class="row-fluid form-line">
							<div class="span2">Date of Birth: </div>
							<div class="span10"><input type="text" id="dob" name="dob" class="input-medium" data-bind="datepicker: dob, datepickerOptions: $root.datepickerOptions" required="true" /></div>
						</div>

						<div class="row-fluid form-line">
							<div class="span2">Gender: </div>
							<div class="span10">
								<label class="radio inline span2"><input type="radio" name="gender" data-bind="checked: gender" value="M"/> Male </label>
								<label class="radio inline span2"><input type="radio" name="gender" data-bind="checked: gender" value="F"/> Female </label>
								<span class="validationMessage" data-bind='validationMessage: gender'></span>
							</div>
						</div>
						<div class="row-fluid form-line"><div class="span2">Email: </div><div class="span10"><input type="text" id="email" name="email" class="input-xlarge" data-bind="value: email" /></div></div>
						<div class="row-fluid form-line"><div class="span2">Phone: </div><div class="span10"><input type="text" id="phone" name="phone" class="input-large" data-bind="value: phone" /></div></div>
						<div class="row-fluid form-line"><div class="span2">Mobile: </div><div class="span10"><input type="text" id="mobile" name="mobile" class="input-large" data-bind="value: mobile" /></div></div>
						<div class="row-fluid form-line">
							<div class="span2">Authorise Mail Out: </div>
							<div class="span10">
								<label class="checkbox inline span2"><input type="checkbox" name="mail_out_email" data-bind="checked: mail_out_email" value="Aboriginal" value="1" /> via email </label>
								<label class="checkbox inline"><input type="checkbox" name="mail_out_sms" data-bind="checked: mail_out_sms" value="Aboriginal" value="1" /> via SMS </label>
							</div>
						</div>
						<div class="row-fluid form-line">
							<div class="span2">Preferred Method of Contact: </div>
							<div class="span10">
								<label class="radio inline span2"><input type="radio" name="preferred_method" data-bind="checked: preferred_method" value="Mobile" /> Mobile</label>
								<label class="radio inline span2"><input type="radio" name="preferred_method" data-bind="checked: preferred_method" value="Email" />Email</label>
								<label class="radio inline span2"><input type="radio" name="preferred_method" data-bind="checked: preferred_method" value="Mail" />Mail</label>
								<span class="validationMessage" data-bind='validationMessage: preferred_method'></span>
							</div>
						</div>

					</div>
					<div id="address_details" class="tab-pane fade">

						<div class="row-fluid form-line">
						<div class="span2">Country of Residence: </div><div class="span10">{{ Form::select('country_of_residence', $countries, '', array('class'=>'input-large', 'data-bind'=>"value: country_of_residence")) }} <span class="alert-danger">* You must enter an Australian address when attending a RSA/RCG course in NSW</span></div>						
						</div>
						<div class="row-fluid form-line">	
							<div class="span6">
								<div class="row-fluid"><div class="span12"><h5>Residential Address</h5></div></div>
								<div class="row-fluid"><div class="span3">Building Name: </div><div class="span8"><input type="text" id="address_building_name" name="address_building_name" class="input-large" data-bind="textInput: address_building_name, event:{'keyup': $root.setPostalAddress.bind($data, true)}" /></div></div>
								<div class="row-fluid"><div class="span3">Unit Number: </div><div class="span8"><input type="text" id="address_unit_details" name="address_unit_details" class="input-large" data-bind="textInput: address_unit_details, event:{'keyup': $root.setPostalAddress.bind($data, true)}" /></div></div>
								<div class="row-fluid"><div class="span3">Street Number: </div><div class="span8"><input type="text" id="address_street_number" name="address_street_number" class="input-large" data-bind="textInput: address_street_number, event:{'keyup': $root.setPostalAddress.bind($data, true)}" /></div></div>
								<div class="row-fluid"><div class="span3">Street Name: </div><div class="span8"><input type="text" id="address_street_name" name="address_street_name" class="input-large" data-bind="textInput: address_street_name, event:{'keyup': $root.setPostalAddress.bind($data, true)}" /></div></div>
								<div class="row-fluid"><div class="span3">Suburb: </div><div class="span8"><input type="text" id="city" name="city" class="input-medium" data-bind="textInput: city, event:{'keyup': $root.setPostalAddress.bind($data, true)}" /></div></div>
								<div class="row-fluid"><div class="span3">State: </div><div class="span8">{{ Form::select('state', $states, '', array('id'=>'state', 'class'=>'input-medium', 'data-bind'=>"textInput: state")) }}</div></div>
								<div class="row-fluid localActivityIndicator"><div class="span3">Postcode: </div><div class="span8"><input type="text" id="post_code" name="post_code" class="input-medium" data-bind="textInput: post_code, event:{'keyup': $root.setPostalAddress.bind($data, true), 'keyup': $root.getSuburbs.bind($data, post_code())}" /></div>
									@include('bookings/common/usi-create-search-suburb')	
								</div>			
							</div>
							<div class="span6">
								<div class="row-fluid">
									<div class="span6"><h5>Postal Address</h5></div>
									<div class="offset3">
										<label class="checkbox"><input type="checkbox" id="same_as_residential" data-bind="checked: $root.postal_same_as_residential, click: $root.setPostalAddress.bind($data, false)" /> Same as Residential</label>
									</div>
								</div>
								<div class="row-fluid"><div class="span3">Building Name: </div><div class="span8"><input type="text" id="postal_address_building_name" name="postal_address_building_name" class="input-large" data-bind="value: postal_address_building_name" /></div></div>
								<div class="row-fluid"><div class="span3">Unit Details: </div><div class="span8"><input type="text" id="postal_address_unit_details" name="postal_address_unit_details" class="input-large" data-bind="value: postal_address_unit_details" /></div></div>
								<div class="row-fluid"><div class="span3">Street Number: </div><div class="span8"><input type="text" id="postal_address_street_number" name="postal_address_street_number" class="input-large" data-bind="value: postal_address_street_number" /></div></div>
								<div class="row-fluid"><div class="span3">Street Name: </div><div class="span8"><input type="text" id="postal_address_street_name" name="postal_address_street_name" class="input-large" data-bind="value: postal_address_street_name" /></div></div>
								<div class="row-fluid"><div class="span3">Suburb: </div><div class="span8"><input type="text" id="postal_city" name="postal_city" class="medium" data-bind="value: postal_city" /></div></div>
								<div class="row-fluid"><div class="span3">State: </div><div class="span8">{{ Form::select('postal_state', $states, '', array('id'=>'postal_state', 'class'=>'input-medium', 'data-bind'=>"value: postal_state")) }}</div></div>
								<div class="row-fluid"><div class="span3">Postcode: </div><div class="span8"><input type="text" id="postal_post_code" name="postal_post_code" class="input-medium" data-bind="value: postal_post_code" /></div></div>
							</div>
						</div>
				
						<div class="row-fluid form-line"><div class="span2">City of Birth: </div><div class="span10"><input type="text" id="city_of_birth" name="city_of_birth" class="input-large" data-bind="value: city_of_birth" /></div></div>
						<div class="row-fluid form-line"><div class="span2">Country of Birth: </div><div class="span10">{{ Form::select('country_of_birth', $countries, '', array('class'=>'input-large', 'data-bind'=>"value: country_of_birth")) }}</div></div>

					</div>
					<div id="lang_details" class="tab-pane fade">
				

						<div class="row-fluid form-line">
							<div class="span2">Other languages at home: </div>
							<div class="span10">
								<label class="radio inline span3"><input type="radio" name="lang_eng" data-bind="checkedValue: 1, checked: lang_eng" value="1"/> No, English Only </label> 
								<label class="radio inline"><input type="radio" name="lang_eng" data-bind="checkedValue: 0, checked: lang_eng" value="0" /> Yes, If Yes what language: </label> 
								{{ Form::select('lang_other', $languages, '', array('class'=>'input-large', 'data-bind'=>"value: lang_other")) }}
							</div>
						</div>

						<div class="row-fluid form-line">
							<div class="span2">How well do you speak English? </div>
							<div class="span10">
								<label class="radio inline span3"><input type="radio" name="lang_eng_level" data-bind="checked: lang_eng_level" value="Very well" /> Very well  </label>
								<label class="radio inline span3"><input type="radio" name="lang_eng_level" data-bind="checked: lang_eng_level" value="Well" /> Well </label>
								<label class="radio inline span3"><input type="radio" name="lang_eng_level" data-bind="checked: lang_eng_level" value="Not Well" /> Not Well </label>
								<label class="radio inline"><input type="radio" name="lang_eng_level" data-bind="checked: lang_eng_level" value="Not at all" /> Not at all </label>
							</div>
						</div>
				
						<div class="row-fluid form-line">
							<div class="span2">Aboriginal / Islander origin: </div>
							<div class="span10">
								{{ Form::select('origin_selection', 
								array(''=>'Select Origin', 
								'Aboriginal'=>'Yes, Aboriginal',
								'Islander'=>'Yes, Torres Strait Islander',
								'AboriginalIslander'=>'Yes, Aboriginal and Yes, Torres Strait Islander',
								'None'=>'No, Not Aboriginal, not Torres Strait Islander'), '', array('class'=>'input-xlarge', 'data-bind'=>"value: origin_selection")) }}
							</div>
						</div>

						<div class="row-fluid form-line">
							<div class="span2">Disability: </div>
							<div class="span10">
								<label class="radio inline span2"><input type="radio" name="disability" data-bind="checkedValue: 1, checked: disability" value="1" /> Yes  </label>
								<label class="radio inline span2"><input type="radio" name="disability" data-bind="checkedValue: 0, checked: disability" value="0" /> No  </label>
								<span class="validationMessage" data-bind='validationMessage: disability'></span>
							</div>
						</div>

						<div class="row-fluid form-line">
						<div class="span2">If Yes, Disabilities: </div>
							<div class="span10">	
								@foreach(array_chunk($disabilities_list, 3) as $group)
								<div class="row-fluid">
									@foreach($group as $key => $disability)
										<label class="checkbox inline @if($key < 2) span3 @endif"><input type="checkbox" name="disabilities[]" data-bind="checked: disabilities" value="{{$disability['id']}}"/> {{$disability['name']}} 
										@if($disability['name'] == 'Other')
											<input type="text" id="disabilities_other" name="disabilities_other" class="input-medium" data-bind="value: disabilities_other" />
										@endif
										</label>
									@endforeach				
								</div>
								@endforeach				
							</div>	
						</div>	
				

					</div>
					<div id="education_details" class="tab-pane fade">

						<div class="row-fluid form-line">
							<div class="span2">Highest School Level: </div>
							<div class="span10">
								<div class="row-fluid">
									<label class="radio inline span3"><input type="radio" name="school_level" data-bind="checked: school_level" value="Year 12 or equivalent" /> Year 12 or equivalent  </label>
									<label class="radio inline span3"><input type="radio" name="school_level" data-bind="checked: school_level" value="Year 11 or equivalent" /> Year 11 or equivalent  </label>
									<label class="radio inline"><input type="radio" name="school_level" data-bind="checked: school_level" value="Year 10 or equivalent"  /> Year 10 or equivalent  </label>
								</div>
								<div class="row-fluid">
									<label class="radio inline span3"><input type="radio" name="school_level" data-bind="checked: school_level" value="Year 9 or equivalent" /> Year 9 or equivalent  </label>
									<label class="radio inline span3"><input type="radio" name="school_level" data-bind="checked: school_level" value="Year 8 or below" /> Year 8 or below  </label>
									<label class="radio inline"><input type="radio" name="school_level" data-bind="checked: school_level" value="Never attended school" /> Never attended school  </label>
									<span class="validationMessage" data-bind='validationMessage: school_level'></span>
								</div>
							</div>
						</div>
						<div class="row-fluid form-line"><div class="span2">Year complete level : </div><div class="span10"><input type="text" id="school_year" name="school_year" class="input-small" data-bind="value: school_year" /></div></div>

						<div class="row-fluid form-line">
							<div class="span2">Are you still attending secondary school: </div>
							<div class="span10">
								<label class="radio inline span3"><input type="radio" data-bind="checked: school_attending" name="c" value="1" /> Yes </label>
								<label class="radio inline"><input type="radio" data-bind="checked: school_attending" name="school_attending" value="0"  /> No </label>
							</div>
						</div>

						<div class="row-fluid form-line">
							<div class="span2">Achievements: </div>
							<div class="span10">	
								@foreach(array_chunk($achievements_list, 2) as $group)
								<div class="row-fluid">
									@foreach($group as $key => $achievement)
										<label class="checkbox inline span5"><input type="checkbox" name="achievements[]" data-bind="checked: achievements" value="{{$achievement['id']}}" /> {{$achievement['name']}} </label>
									@endforeach				
								</div>
								@endforeach				
							</div>
						</div>

					</div>
					<div id="employment_details" class="tab-pane fade">
						<div class="row-fluid form-line">
							<div class="span2">Employment: </div>
							<div class="span10">
								<div class="row-fluid">
								<label class="radio inline span5"><input type="radio" name="employment" data-bind="checked: employment" value="Employer" /> Employer  </label>
								<label class="radio inline"><input type="radio" name="employment" data-bind="checked: employment" value="Unemployed - seeking full-time work" /> Unemployed - seeking full-time work  </label>
								</div>
								<div class="row-fluid">
									<label class="radio inline span5"><input type="radio" name="employment data-bind="checked: employment"" value="Part-time employee" /> Part-time employee  </label>
									<label class="radio inline"><input type="radio" name="employment" data-bind="checked: employment" value="Unemployed - seeking part-time work"/> Unemployed - seeking part-time work  </label>
								</div>
								<div class="row-fluid">
									<label class="radio inline span5"><input type="radio" name="employment" data-bind="checked: employment" value="Full-time employee" /> Full-time employee  </label>
									<label class="radio inline"><input type="radio" name="employment" data-bind="checked: employment" value="Not employed - not seeking employment" /> Not employed - not seeking employment  </label>
								</div>
								<div class="row-fluid">
									<label class="radio inline span5"><input type="radio" name="employment" data-bind="checked: employment" value="Self employed - not employing others" /> Self employed - not employing others  </label>
									<label class="radio inline"><input type="radio" name="employment" data-bind="checked: employment" value="Employed - unpaid worker in a family business" /> Employed - unpaid worker in a family business  </label>
								</div>
							</div>
						</div>
						<div class="row-fluid form-line">
							<div class="span2">Reason for taking course: </div>
							<div class="span10">
								@foreach(array_chunk($study_reasons_list, 2) as $group22)
								<div class="row-fluid">
									@foreach($group22 as $key => $reason)
										<label class="radio inline span5"><input type="radio" name="study_reason" data-bind="checked: study_reason" value="{{$reason['id']}}" /> {{$reason['name']}}</label>
									@endforeach				
								</div>
								@endforeach				
							</div>
						</div>

					</div>
				
					<!--
					<div id="document_details" class="tab-pane fade">
						@--include('bookings/common/usi-create-documents')						
					</div><!-- tab-pane -->
					
				
				</div><!-- tab content -->
			</div>
		</div>

        <div class="modal-body container-fluid" data-bind="css: { hide: usicreate().is_course_accredited() == true }">	
			<div class="tab-content" data-bind="with: usicreate">
				<div class="row-fluid form-line">
					<div class="span2">Family Name: </div>
					<div class="span10"><input type="text" name="last_name" class="input-xlarge" data-bind="value: last_name" /></div>
				</div>
				
				<div class="row-fluid form-line">
					<div class="span2">Given Names: </div>
					<div class="span10"><input type="text" name="first_name" class="input-xlarge" data-bind="value: first_name" /></div>
				</div>

				<div class="row-fluid form-line">
					<div class="span2">Date of Birth: </div>
					<div class="span10"><input type="text" name="dob" class="input-medium" data-bind="datepicker: dob, datepickerOptions: $root.datepickerOptions" required="true" /></div>
				</div>

				<div class="row-fluid form-line"><div class="span2">Email: </div><div class="span10"><input type="text" name="email" class="input-xlarge" data-bind="value: email" /></div></div>
				<div class="row-fluid form-line"><div class="span2">Phone: </div><div class="span10"><input type="text" name="phone" class="input-large" data-bind="value: phone" /></div></div>
				<div class="row-fluid form-line"><div class="span2">Mobile: </div><div class="span10"><input type="text" name="mobile" class="input-large" data-bind="value: mobile" /></div></div>
			</div><!-- tab content -->
		</div>

@include('bookings/common/usi-create-helper')						
