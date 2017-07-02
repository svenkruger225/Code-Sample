@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
User Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>User Management</h3>
</div>
<div class="alert-info form-horizontal table-condensed">
	<div class="control-group">
		<label class="control-label" for="UserId">Select User</label>
		<div class="controls">
            <select data-bind="value: mySelection, 'event': { 'change': selectItem }">
				<option value="">Select User</option>
				@foreach ($users as $user)<option value="{{$user->id}}">{{$user->fullName()}}</option>@endforeach
            </select> or 
			<div class="pull-right">
				<button class="btn btn-inverse" data-bind="click: newItemCmd"><i class="icon-white icon-plus"></i>&nbsp;Add New</button>
			</div>
		</div>
	</div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab-general" data-toggle="tab">General</a></li>
	<li><a href="#tab-permissions" data-toggle="tab">Permissions</a></li>
</ul>

  <form action="" id="userForm" class="form-horizontal">
	
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content" data-bind="with: itemForEditing">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<!-- User Name -->
			<div class="control-group">
				<label class="control-label" for="username">User Name</label>
				<div class="controls">
					<input type="text" name="username" id="username" data-bind="value:username" class="input-small" />
				</div>
			</div>

			<!-- First Name -->
			<div class="control-group">
				<label class="control-label" for="first_name">First Name</label>
				<div class="controls">
					<input type="text" name="first_name" id="first_name" data-bind="value:first_name" class="input-medium" />
				</div>
			</div>

			<!-- Last Name -->
			<div class="control-group">
				<label class="control-label" for="last_name">Last Name</label>
				<div class="controls">
					<input type="text" name="last_name" id="last_name" data-bind="value:last_name" class="input-medium" />
				</div>
			</div>

			<!-- Email -->
			<div class="control-group">
				<label class="control-label" for="email">Email</label>
				<div class="controls">
					<input type="text" name="email" id="email" data-bind="value:email" class="input-large" />
				</div>
			</div>

			<!-- Password -->
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="password" name="password" id="password" data-bind="value:password" class="input-medium" />
				</div>
			</div>

			<!-- Password Confirm -->
			<div class="control-group">
				<label class="control-label" for="password_confirm">Confirm Password</label>
				<div class="controls">
					<input type="password" name="password_confirm" id="password_confirm" data-bind="value:password_confirm" class="input-medium" />
				</div>
			</div>

			<!-- DOB Status -->
			<div class="control-group">
				<label class="control-label" for="dob">Date of Birth</label>
				<div class="controls">
					<input type="text" name="dob" id="dob" data-bind="datepicker: dob, datepickerOptions: $parent.datepickerOptions" class="input-medium" />
				</div>
			</div>

			<!-- Activation Status -->
			<div class="control-group">
				<label class="control-label" for="activated">Activated</label>
				<div class="controls">
					<input type="checkbox" name="activated" id="activated" data-bind="checked:activated" />
				</div>
			</div>

			<!-- Groups -->
			<div class="control-group">
				<label class="control-label" for="groups">Groups</label>
				<div class="controls">
					<select name="groups[]" id="groups[]" multiple="true" data-bind="
					options: $parent.roles,
					optionsText: 'name',
					optionsValue: 'id',
					selectedOptions: rolesIds, 
					optionsCaption: 'Select Group(s)...'">
					</select>
					<span class="help-block">
						Select a group to assign to the user, remember that a user takes on the permissions of the group they are assigned.
					</span>
				</div>
			</div>
		</div>

		<!-- Permissions tab -->
		<div class="tab-pane" id="tab-permissions">
			<div class="control-group">
				<div class="controls">


					@foreach ($permissions as $area => $permissions)
					<fieldset>
						<legend>{{ $area }}</legend>

						@foreach ($permissions as $permission)
						<div class="control-group">
							<label class="control-group">{{ $permission['label'] }}</label>

							<div class="radio inline">
								<label for="{{ $permission['permission'] }}_allow" onclick="">
									<input type="radio" value="1" id="{{ $permission['permission'] }}_allow" name="permissions[{{ $permission['permission'] }}]">
									Allow
								</label>
							</div>

							<div class="radio inline">
								<label for="{{ $permission['permission'] }}_deny" onclick="">
									<input type="radio" value="-1" id="{{ $permission['permission'] }}_deny" name="permissions[{{ $permission['permission'] }}]">
									Deny
								</label>
							</div>

							@if ($permission['can_inherit'])
							<div class="radio inline">
								<label for="{{ $permission['permission'] }}_inherit" onclick="">
									<input type="radio" value="0" id="{{ $permission['permission'] }}_inherit" name="permissions[{{ $permission['permission'] }}]">
									Inherit
								</label>
							</div>
							@endif
						</div>
						@endforeach

					</fieldset>
					@endforeach
				</div>
			</div>
		</div>
	</div>

	<!-- Form Actions -->
	<div class="control-group">
		<div class="controls">
			<button class="btn btn-success" data-bind="command: addUserCmd, activity: addUserCmd.isExecuting"><i class="icon-white icon-ok"></i>&nbsp;Add User</button>
			<button class="btn btn-success" data-bind="command: updateUserCmd, activity: updateUserCmd.isExecuting"><i class="icon-white icon-ok"></i>&nbsp;Update User</button>
			<button class="btn btn-danger" data-bind="click: deleteUserCmd"><i class="icon-white icon-trash"></i>&nbsp;Delete User</button>
			<button type="reset" class="btn btn-danger" data-bind="click: clearCmd"><i class="icon-white icon-trash"></i>&nbsp;Clear</button>
		</div>
	</div>

</form>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/users.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
</div>

@stop
