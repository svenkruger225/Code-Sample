@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
User Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Users Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.users.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.users.index', 'class'=>'form-inline')) }}
				{{ Form::text('search', Input::old('search'), array('class'=>'input-medium', 'placeholder'=>'Search Name by')) }}				
				{{ Form::select('g_id', $groups, Input::old('g_id'), array('id'=>'groupList','class'=>'input-medium')) }}
				{{ Form::select('del', $delete_types, Input::old('del'), array('class'=>'input-xlarge')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>
{{ $users->links() }}

<table class="table table-bordered table-striped table-hover table-condensed">
	<thead>
		<tr>
			<th class="span1">@lang('backend/users/table.id')</th>
			<th class="span1">UserName</th>
			<th class="span3">Name</th>
			<th class="span3">@lang('backend/users/table.email')</th>
			<th class="span2">Mobile</th>
			<th class="span1">@lang('backend/users/table.activated')</th>
			<th class="span1">@lang('backend/users/table.created_at')</th>
            <th class="span1">Message<br><input type="checkbox" value="1" data-bind="'event': {'click' : selectAllMessageList}"/></th>
            <th class="span1"><a href="#" class="btn btn-mini" data-bind="click: openMessageForm.bind($data, 'User')"><i class="icon-envelope icon-white"></i> Open Bulk<BR>Message Form</a></th>
		</tr>
	</thead>
	<tbody>
		@foreach ($users as $user)
		<tr>
			<td>{{ $user->id }}</td>
			<td>{{ $user->username }}</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->email }}</td>
			<td>{{ $user->mobile }}</td>
			<td>@lang('general.' . ($user->isActivated() ? 'yes' : 'no'))</td>
			<td>{{ $user->created_at->diffForHumans() }}</td>
			<td><input type="checkbox" class="messagelist" value="{{$user->id}}" data-bind="'event': {'click' : updateMessageList}"/></td>
			<td>
				<a href="{{ route('backend.users.edit', $user->id) }}" class="btn btn-mini">@lang('button.edit')</a>

				@if ( ! is_null($user->deleted_at))
				<a href="{{ route('restore/user', $user->id) }}" class="btn btn-mini btn-warning">@lang('button.restore')</a>
				@else
				@if (Sentry::getId() !== $user->id)
				<a href="{{ route('delete/user', $user->id) }}" class="btn btn-mini btn-danger deleteCmd">@lang('button.delete')</a>
				@else
				<span class="btn btn-mini btn-danger disabled">@lang('button.delete')</span>
				@endif
				@endif
				<a href="#" class="btn btn-mini btn-warning" data-bind="click: openSingleMessageForm.bind($data, 'User','{{$user->id}}','{{$user->name}}','{{$user->email}}','{{$user->mobile}}')"><i class="icon-envelope icon-white"></i> Message</a>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

{{ $users->links() }}

@include('backend/common/bulk-message')
@include('backend/common/message')

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/users.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
