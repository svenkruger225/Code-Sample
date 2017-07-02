@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Suppliers Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Suppliers Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.suppliers.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.suppliers.index', 'class'=>'form-inline')) }}
				{{ Form::text('search', Input::old('search'), array('class'=>'input-medium', 'placeholder'=>'Search Name by')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>
{{ $suppliers->links() }}

<table class="table table-bordered table-striped table-hover table-condensed">
	<thead>
		<tr>
			<th class="span1">Supplier Id</th>
			<th class="span1">Supplier Name</th>
			<th class="span3">Contact Name</th>
			<th class="span3">Email</th>
			<th class="span3">Mobile</th>
			<th class="span3">State</th>
			<th class="span2">Actions</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($suppliers as $user)
		<tr>
			<td>{{ $user->id }}</td>
			<td>{{ $user->business_name }}</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->email }}</td>
			<td>{{ $user->mobile }}</td>
			<td>{{ $user->business_state }}</td>
			<td>
				<a href="{{ route('backend.suppliers.edit', $user->id) }}" class="btn btn-mini">Edit</a>
				<a href="{{ route('delete/user', $user->id) }}" class="btn btn-mini btn-danger deleteCmd">Delete</a>
			</td>
		</tr>
		@endforeach
	</tbody>
</table>

{{ $suppliers->links() }}

<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/users.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
