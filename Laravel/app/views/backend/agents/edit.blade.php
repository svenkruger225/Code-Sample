@extends('backend/layouts/default')

{{-- Web site Title --}}
@section('title')
Agent Update ::
@parent
@stop

{{-- Content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Agent Update
		<div class="pull-right">
			<a href="{{ route('backend.agents.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
	<li><a href="#tab-permissions" data-toggle="tab">Prices</a></li>
</ul>

{{ Form::model($agent, array('method' => 'PATCH', 'route' => array('backend.agents.update', $agent->id),  'class'=>'form-horizontal')) }}

	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('code') ? 'error' : '' }}">
				<label class="control-label" for="code">Code: </label>
				<div class="controls">
					{{ Form::text('code', $agent->code, array('class'=>'input-medium')) }}
					{{ $errors->first('code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $agent->name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
                        <div class="control-group {{ $errors->has('username') ? 'error' : '' }}">
				<label class="control-label" for="username">Username: </label>
				<div class="controls">
					{{ Form::text('username', $agent->contact_name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('username', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('contact_position') ? 'error' : '' }}">
				<label class="control-label" for="contact_position">Contact Position: </label>
				<div class="controls">
					{{ Form::text('contact_position', $agent->contact_position, array('class'=>'input-xlarge')) }}
					{{ $errors->first('contact_position', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
		 
			<div class="control-group {{ $errors->has('address') ? 'error' : '' }}">
				<label class="control-label" for="address">Address: </label>
				<div class="controls">
					{{ Form::text('address', $agent->address, array('class'=>'input-xlarge')) }}
					{{ $errors->first('address', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('city') ? 'error' : '' }}">
				<label class="control-label" for="city">City: </label>
				<div class="controls">
					{{ Form::text('city', $agent->city, array('class'=>'input-large')) }}
					{{ $errors->first('city', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
		 
			<div class="control-group {{ $errors->has('state') ? 'error' : '' }}">
				<label class="control-label" for="state">State: </label>
				<div class="controls">
					{{ Form::text('state', $agent->state, array('class'=>'input-small')) }}
					{{ $errors->first('state', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('post_code') ? 'error' : '' }}">
				<label class="control-label" for="post_code">Post code: </label>
				<div class="controls">
					{{ Form::text('post_code', $agent->post_code, array('class'=>'input-medium')) }}
					{{ $errors->first('post_code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
				<label class="control-label" for="email">Email: </label>
				<div class="controls">
					{{ Form::text('email', $agent->email, array('class'=>'input-xlarge')) }}
					{{ $errors->first('email', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
                        <!-- Password -->
			<div class="control-group {{ $errors->has('password') ? 'error' : '' }}">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					{{ Form::password('password',null, array('class'=>'input-large')) }}
					{{ $errors->first('password', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('phone') ? 'error' : '' }}">
				<label class="control-label" for="phone">Phone: </label>
				<div class="controls">
					{{ Form::text('phone', $agent->phone, array('class'=>'input-large')) }}
					{{ $errors->first('phone', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
		  
			<div class="control-group {{ $errors->has('mobile') ? 'error' : '' }}">
				<label class="control-label" for="mobile">Mobile: </label>
				<div class="controls">
					{{ Form::text('mobile', $agent->mobile, array('class'=>'input-large')) }}
					{{ $errors->first('mobile', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
					 
			<div class="control-group {{ $errors->has('fax') ? 'error' : '' }}">
				<label class="control-label" for="fax">Fax: </label>
				<div class="controls">
					{{ Form::text('fax', $agent->fax, array('class'=>'input-large')) }}
					{{ $errors->first('fax', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('payment_type') ? 'error' : '' }}">
				<label class="control-label" for="payment_type">Payment Type: </label>
				<div class="controls">
                                    {{ Form::radio('payment_type', 'paynow', (Input::old('payment_type') == 'paynow'), array('class'=>'input-large')) }} <span>Pay Now<span><br/>
                                    {{ Form::radio('payment_type', 'paylater', (Input::old('payment_type') == 'paylater'), array('class'=>'input-large')) }} <span>Pay Later<span><br/>
                                    {{ Form::radio('payment_type', 'both',(Input::old('payment_type') == 'both'), array('class'=>'input-large')) }} <span>Both<span><br/>
                                    {{ $errors->first('payment_type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $agent->active == '1' ? 'checked' : '' }} />
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

		</div>

		<!-- Permissions tab -->
		<div class="tab-pane" id="tab-permissions">
			<div class="control-group">
				<div class="controls inline span10">
					<table class="table table-striped table-bordered table-condensed courses">
						<thead>
							<tr>
                                                                <th>Location</th>
								<th>Course Type</th>
								<th>Price Now</th>
								<th>Price Later</th>
								<th><a id="addnewcourse" href="#" class="btn btn-mini btn-success add" data-bind="'click': addNew">Add New</a></th>
							</tr>
						</thead>
						<tbody id="courses_list">
							<tr class="template" style="display:none;">
								<td>
								<input type="hidden" name="c_id[]" value="" />
								<input type="hidden" name="agent_id[]" value="{{$agent->id}}" />
								{{ Form::select('location_id[]', $locations, '', array('class'=>'input-xlarge')) }}
								</td>
								<td>{{ Form::select('course_id[]', $courses, '', array('class'=>'input-xlarge')) }}</td>
								<td><input type="text" name="price_online[]" class="input-small price id" /></td>
								<td><input type="text" name="price_offline[]" class="input-small price" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@foreach ($agent->courses as $course)
							<tr>
								<td>
									<input type="hidden" name="c_id[]" value="{{$course->id}}" />
									<input type="hidden" name="agent_id[]" value="{{$course->agent_id}}" />
									{{ Form::select('location_id[]', $locations, $course->location_id, array('class'=>'input-xlarge')) }}
								</td>
								<td>{{ Form::select('course_id[]', $courses, $course->course_id, array('class'=>'input-xlarge')) }}</td>
								<td><input type="text" name="price_online[]" value="{{$course->price_online}}" class="input-small price id" /></td>
								<td><input type="text" name="price_offline[]" value="{{$course->price_offline}}" class="input-small price" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>


        </div>
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-small btn-success">Update Agent</button>
				<a class="btn btn-small" href="{{ route('backend.agents.index') }}">Cancel</a>
				<button type="reset" class="btn btn-small btn-danger">Reset</button>
			</div>
		</div>
	</div>
{{ Form::close() }}
<script>
$(document).ready(function(){
    var defaultPayment=$('input[name="payment_type"]:checked').val();
    if(defaultPayment === 'paynow'){
        $('th:nth-child(4)').hide();
        $('td:nth-child(4)').hide();
    }
    else if(defaultPayment === 'paylater'){
        $('th:nth-child(3)').hide();
        $('td:nth-child(3)').hide();
    }
    $("input[type='radio']").change(function() {
        var paymentType=$(this).val();
        //alert(paymentType);
        if(paymentType === 'paynow'){
            $('th:nth-child(3)').show();
            $('th:nth-child(4)').hide();
            $('td:nth-child(3)').show();
            $('td:nth-child(4)').hide();
            //$('td:nth-child(4) input').val('0.00');
        }
        else if(paymentType === 'paylater'){
            $('th:nth-child(4)').show();
            $('th:nth-child(3)').hide();
            $('td:nth-child(4)').show();
            $('td:nth-child(3)').hide();
            //$('td:nth-child(3) input').val('0.00');
        }
        else{
            $('th:nth-child(4)').show();
            $('th:nth-child(3)').show();
            $('td:nth-child(4)').show();
            $('td:nth-child(3)').show();
        }
    });
});
</script>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/agents.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
</div>
@stop