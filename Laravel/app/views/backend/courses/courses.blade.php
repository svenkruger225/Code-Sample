@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Course Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>Course Management</h3>
</div>
<div class="alert-info form-horizontal table-condensed">
	<div class="control-group">
		<label class="control-label" for="CourseId">Select Course</label>
		<div class="controls">
            <select id="courseList" data-bind="value: mySelection, 'event': { 'change': selectItem }">
				<option value="">Select Course</option>
				@foreach ($courses as $course)<option value="{{ $course->id }}">{{ $course->name }}</option>@endforeach
            </select> or 
			<div class="pull-right">
				<button class="btn btn-inverse" data-bind="click: newItemCmd"><i class="icon-white icon-plus"></i>&nbsp;Add New</button>
			</div>
		</div>
	</div>
</div>

  <form action="" id="courseForm" class="form-horizontal">
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content" data-bind="with: itemForEditing">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<!-- Course Name -->
			<div class="control-group">
				<label class="control-label" for="name">Name</label>
				<div class="controls">
					<input type="hidden" name="id" id="id" data-bind="value:id" />
					<input type="text" name="name" id="name" data-bind="value:name" class="input-xlarge" />
				</div>
			</div>

			<!-- First Name -->
			<div class="control-group">
				<label class="control-label" for="short_name">Short Name</label>
				<div class="controls">
					<input type="text" name="short_name" id="short_name" data-bind="value:short_name" class="input-medium" />
				</div>
			</div>

			<!-- Last Name -->
			<div class="control-group">
				<label class="control-label" for="description">Description</label>
				<div class="controls">
					<textarea name="description" id="description" rows="5" data-bind="value:description" class="input-xxlarge" ></textarea>
				</div>
			</div>

			<!-- Email -->
			<div class="control-group">
				<label class="control-label" for="myob_code">MYOB code</label>
				<div class="controls">
					<input type="text" name="myob_code" id="myob_code" data-bind="value:myob_code" class="input-small" />
				</div>
			</div>

			<!-- Activation Status -->
			<div class="control-group">
				<label class="control-label" for="active">Active</label>
				<div class="controls">
					<input type="checkbox" name="active" id="active" data-bind="checked:active" />
				</div>
			</div>
		</div>

	</div>

	<!-- Form Actions -->
	<div class="control-group">
		<div class="controls">
			<button class="btn btn-success" data-bind="command: addCourseCmd, activity: addCourseCmd.isExecuting"><i class="icon-white icon-ok"></i>&nbsp;Add Course</button>
			<button class="btn btn-success" data-bind="command: updateCourseCmd, activity: updateCourseCmd.isExecuting"><i class="icon-white icon-ok"></i>&nbsp;Update Course</button>
			<button class="btn btn-danger" data-bind="click: deleteCourseCmd"><i class="icon-white icon-trash"></i>&nbsp;Delete Course</button>
			<button type="reset" class="btn btn-danger" data-bind="click: clearCmd"><i class="icon-white icon-trash"></i>&nbsp;Clear</button>
		</div>
	</div>

</form>
<script data-main="/app/bootstrapers/courses" src="/assets/js/require/require.js"></script>
</div>

@stop
