@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Bundle Create ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Course Bundle Create

		<div class="pull-right">
			<a href="{{ route('backend.coursebundles.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::open(array('route' => 'backend.coursebundles.store', 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', Input::old('name'), array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, Input::old('location_id'), array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('students_min') ? 'error' : '' }}">
				<label class="control-label" for="students_min">Students Min: </label>
				<div class="controls">
					{{ Form::text('students_min', Input::old('students_min'), array('class'=>'input-mini')) }} - minimum number of students to enable bundle			
					{{ $errors->first('students_min', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="course_id">Bundles: </label>
				<div class="controls inline span10">
					<table class="table table-striped table-bordered table-condensed courses">
						<thead>
							<tr>
								<th>Course Type</th>
								<th>Price Online</th>
								<th>Price Offline</th>
								<th><a id="addnewcourse" href="#" class="btn btn-mini btn-success add" data-bind="'click': addNew">Add New</a></th>
							</tr>
						</thead>
						<tbody id="courses_list">
							<tr class="template" style="display:none;">
								<td>{{ Form::select('course_id[]', $courses, '', array('class'=>'input-xlarge')) }}</td>
								<td><input type="text" name="price_online[]" class="input-small price id" /></td>
								<td><input type="text" name="price_offline[]" class="input-small price" /></td>
								<td><a href="#" class="btn btn-mini btn-danger remove">Remove</a></td>
							</tr>
							<tr>
								<td>{{ Form::select('course_id[]', $courses, '', array('class'=>'input-xlarge')) }}</td>
								<td><input type="text" name="price_online[]" class="input-small price" value="" /></td>
								<td><input type="text" name="price_offline[]" class="input-small price" value="" /></td>
								<td><a href="#" class="btn btn-mini btn-danger remove">Remove</a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_from') ? 'error' : '' }}">
				<label class="control-label" for="date_from">Date_from: </label>
				<div class="controls">
					{{ Form::text('date_from', Input::old('date_from'), array('class'=>'input-small', 'id'=>'date_from')) }}
					{{ $errors->first('date_from', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_to') ? 'error' : '' }}">
				<label class="control-label" for="date_to">Date_to: </label>
				<div class="controls">
					{{ Form::text('date_to', Input::old('date_to'), array('class'=>'input-small', 'id'=>'date_to')) }}
					{{ $errors->first('date_to', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total_online') ? 'error' : '' }}">
				<label class="control-label" for="total_online">Total Online: </label>
				<div class="controls">
					{{ Form::text('total_online',  Input::old('total_online'), array('class'=>'input-medium', 'id'=>'total_online')) }}
					{{ $errors->first('total_online',  '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total_offline') ? 'error' : '' }}">
				<label class="control-label" for="total_offline">Total Offline: </label>
				<div class="controls">
					{{ Form::text('total_offline', Input::old('total_offline'), array('class'=>'input-medium', 'id'=>'total_offline')) }}
					{{ $errors->first('total_offline', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					{{ Form::checkbox('active', '1', Input::old('active')) }}
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
				{{ Form::submit('Create Bundle', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/bundles.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
</div>
@stop