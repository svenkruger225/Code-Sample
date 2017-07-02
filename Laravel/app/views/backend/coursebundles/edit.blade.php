@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Bundle Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Course Bundle Update

		<div class="pull-right">
			<a href="{{ route('backend.coursebundles.create') }}" class="btn btn-small btn-inverse"><i class="icon-plus icon-white"></i> Create New</a>
			<a href="{{ route('backend.coursebundles.index') }}" class="btn btn-small btn-inverse back"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($coursebundle, array('method' => 'PATCH', 'route' => array('backend.coursebundles.update', $coursebundle->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<input type="hidden" id="edit_old" value="{{{ json_encode($coursebundle) }}}" />

			<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					{{ Form::text('name', $coursebundle->name, array('class'=>'input-xlarge')) }}
					{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::select('location_id', $locations, $coursebundle->location_id, array('class'=>'input-xlarge')) }}				
					{{ $errors->first('location_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('students_min') ? 'error' : '' }}">
				<label class="control-label" for="students_min">Students Min: </label>
				<div class="controls">
					{{ Form::text('students_min', $coursebundle->students_min, array('class'=>'input-mini')) }} - minimum number of students to enable bundle			
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
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@foreach ($coursebundle->bundles as $bundle)
							<tr>
								<td>{{ Form::select('course_id[]', $courses, $bundle->pivot->course_id, array('class'=>'input-xlarge')) }}</td>
								<td><input type="text" name="price_online[]" class="input-small price" value="{{ $bundle->pivot->price_online }}" /></td>
								<td><input type="text" name="price_offline[]" class="input-small price" value="{{ $bundle->pivot->price_offline }}" /></td>
								<th><a href="#" class="btn btn-mini btn-danger remove">Remove</a></th>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_from') ? 'error' : '' }}">
				<label class="control-label" for="date_from">Date_from: </label>
				<div class="controls">
					{{ Form::text('date_from', $coursebundle->date_from, array('class'=>'input-small', 'id'=>'date_from')) }}
					{{ $errors->first('date_from', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_to') ? 'error' : '' }}">
				<label class="control-label" for="date_to">Date_to: </label>
				<div class="controls">
					{{ Form::text('date_to', $coursebundle->date_to, array('class'=>'input-small', 'id'=>'date_to')) }}
					{{ $errors->first('date_to', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total_online') ? 'error' : '' }}">
				<label class="control-label" for="total_online">Total Online: </label>
				<div class="controls">
					{{ Form::text('total_online',  $coursebundle->total_online, array('class'=>'input-medium', 'id'=>'total_online')) }}
					{{ $errors->first('total_online',  '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('total_offline') ? 'error' : '' }}">
				<label class="control-label" for="total_offline">Total Offline: </label>
				<div class="controls">
					{{ Form::text('total_offline', $coursebundle->total_offline, array('class'=>'input-medium', 'id'=>'total_offline')) }}
					{{ $errors->first('total_offline', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="hidden" name="active" value="0" /><input type="checkbox" name="active" value="1" {{ $coursebundle->active == '1' ? 'checked' : '' }} />
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
				{{ Form::submit('Update Bundle', array('class' => 'btn btn-small btn-info')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/bundles.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
</div>
@stop