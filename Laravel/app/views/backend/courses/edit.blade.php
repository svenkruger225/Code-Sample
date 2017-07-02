@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Course Update

		<div class="pull-right">
			<a href="{{ route('backend.courses.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($course, array('method' => 'PATCH', 'route' => array('backend.courses.update', $course->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">

			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="title">Name: </label>
				<div class="controls">
					{{ Form::text('name', $course->name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('short_name') ? 'error' : '' }}">
				<label class="control-label" for="title">Short Name: </label>
				<div class="controls">
					{{ Form::text('short_name', $course->short_name, array('class'=>'input-medium')) }}
					{{ $errors->first('short_name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('type') ? 'error' : '' }}">
				<label class="control-label" for="type">Course Type: </label>
				<div class="controls">
					{{ Form::select('type', array('FaceToFace'=> 'Face to Face', 'Online'=> 'Online'), $course->type, array('class'=>'input-medium')) }}
					{{ $errors->first('type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			
			<div class="control-group {{ $errors->has('assessment_type') ? 'error' : '' }}">
				<label class="control-label" for="type">Assessment Type: </label>
				<div class="controls">
					{{ Form::select('assessment_type', array('FaceToFace'=> 'Face to Face', 'Review'=> 'Review', 'Online'=> 'Online'), $course->assessment_type, array('class'=>'input-medium')) }}
					{{ $errors->first('assessment_type', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="title">Description: </label>
				<div class="controls">
					{{ Form::textarea('description', $course->description, array('class'=>'span8', 'rows'=>'3')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('rto_code') ? 'error' : '' }}">
				<label class="control-label" for="title">RTO code: </label>
				<div class="controls">
					{{ Form::text('rto_code', $course->rto_code, array('class'=>'input-medium')) }}
					{{ $errors->first('rto_code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('certificate_code') ? 'error' : '' }}">
				<label class="control-label" for="title">Certificate Code/Name: </label>
				<div class="controls">
					{{ Form::text('certificate_code', $course->certificate_code, array('class'=>'input-xxlarge')) }}
					{{ $errors->first('certificate_code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Prices: </label>
				<div class="controls span10">
					<table class="table table-striped table-bordered table-condensed table-hover courses">
						<thead>
							<tr>
								<th class="span1">Location</th>
								<th class="span1">Price Online</th>
								<th class="span1">Price Offline</th>
								<th class="span1">Discount</th>
								<th class="span2">Discount Type</th>
								<th class="span1">Students Min</th>
								<th class="span1">Active</th>
								<th class="span1"><a id="addnewcourse" href="#" class="btn btn-mini btn-success add">Add New</a></th>
							</tr>
						</thead>
						<tbody id="courses_list">
							<tr class="template" style="display:none;">
								<td>
								<input type="hidden" name="price_id[]" value="" />
								<input type="hidden" name="course_id[]" value="" />
								{{ Form::select('location_id[]', $locations, '', array('class'=>'input-medium')) }}
								</td>
								<td><input type="text" name="price_online[]" class="input-mini price id" /></td>
								<td><input type="text" name="price_offline[]" class="input-mini price" /></td>
								<td><input type="text" name="discount[]" class="input-mini percentage" /></td>
								<td>{{ Form::select('discount_type[]', array('0'=> 'Percentage', '1'=> 'Amount'), '0', array('class'=>'input-small')) }}</td>
								<td><input type="text" name="students_min[]" value="1" class="input-mini" /></td>
								<td>{{ Form::select('act[]', array('1'=> 'active', '0'=> 'inactive'), '1', array('class'=>'input-mini')) }}</td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@if ($course->prices->count())
							@foreach ($course->prices as $price)
							<tr>
								<td>
								<input type="hidden" name="price_id[]" value="{{$price->id}}" />
								<input type="hidden" name="course_id[]" value="{{$price->course_id}}" />
								{{ Form::select('location_id[]', $locations, $price->location_id, array('class'=>'input-medium')) }}
								</td>
								<td><input type="text" name="price_online[]" value="{{$price->price_online}}" class="input-mini price id" /></td>
								<td><input type="text" name="price_offline[]" value="{{$price->price_offline}}" class="input-mini price" /></td>
								<td><input type="text" name="discount[]" value="{{$price->discount}}" class="input-mini percentage" /></td>
								<td>{{ Form::select('discount_type[]', array('0'=> 'Percentage', '1'=> 'Amount'), $price->discount_type, array('class'=>'input-small')) }}</td>
								<td><input type="text" name="students_min[]" value="{{$price->students_min}}" class="input-mini" /></td>
								<td>{{ Form::select('act[]', array('1'=> 'active', '0'=> 'inactive'), $price->active, array('class'=>'input-mini')) }}</td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
			<div class="control-group {{ $errors->has('order') ? 'error' : '' }}">
				<label class="control-label" for="order">Order: </label>
				<div class="controls">
					{{ Form::text('order', $course->order, array('class'=>'input-mini')) }}
					{{ $errors->first('order', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('gst') ? 'error' : '' }}">
				<label class="control-label" for="gst">Apply Gst: </label>
				<div class="controls">
					<input type="hidden" name="gst" value="0" /><input type="checkbox" name="gst" value="1" {{ $course->gst == '1' ? 'checked' : '' }} />
					{{ $errors->first('gst', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $course->active == '1' ? 'checked' : '' }} />
					{{ $errors->first('active', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			
			<div class="control-group {{ $errors->has('myob_job_code') ? 'error' : '' }}">
				<label class="control-label" for="title">Myob Job Code: </label>
				<div class="controls">
					{{ Form::text('myob_job_code', $course->myob_job_code, array('class'=>'input-large')) }}
					{{ $errors->first('myob_job_code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('myob_code') ? 'error' : '' }}">
				<label class="control-label" for="title">MYOB Code: </label>
				<div class="controls">
					{{ Form::text('myob_code', $course->myob_code, array('class'=>'input-small')) }}
					{{ $errors->first('myob_code', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('pair_course_id') ? 'error' : '' }}">
				<label class="control-label" for="pair_course_id">Pair Course: </label>
				<div class="controls">
					{{ Form::select('pair_course_id', $courses, $course->pair_course_id, array('class'=>'input-large')) }}				
					{{ $errors->first('pair_course_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>

			<div class="control-group {{ $errors->has('pair_course_id_to_add') ? 'error' : '' }}">
				<label class="control-label" for="pair_course_id_to_add">Pair To Add: </label>
				<div class="controls">
					{{ Form::select('pair_course_id_to_add', $courses, $course->pair_to_add, array('class'=>'input-large')) }}				
					{{ $errors->first('pair_course_id_to_add', '<span class="help-inline">:message</span>') }}
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
				{{ link_to_route('backend.courses.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/courses.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop