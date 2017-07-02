@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Agents Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Agents Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.agents.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.agents.index', 'class'=>'form-inline')) }}
				{{ Form::text('search', Input::old('search'), array('class'=>'input-medium', 'placeholder'=>'Search Name by')) }}				
				{{ Form::submit('Search', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>	
</div>


@if (count($agents) > 0)

{{ $agents->links() }}

	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th class="span1">ID</th>
				<th class="span1">Code</th>
				<th class="span3">Name</th>
				<th class="span3">Email</th>
				<th class="span2">Phone</th>
				<th class="span2">Mobile</th>
				<th class="span2">Fax</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($agents as $agent)
				<tr>
					<td>{{{ $agent->id }}}</td>
					<td>{{{ $agent->code }}}</td>
					<td>{{{ $agent->name }}}</td>
					<td>{{{ $agent->email }}}</td>
					<td>{{{ $agent->phone }}}</td>
					<td>{{{ $agent->mobile }}}</td>
					<td>{{{ $agent->fax }}}</td>
					<td>{{{ $agent->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.agents.destroy', $agent->id))) }}
                    <td>
						{{ link_to_route('backend.agents.edit', 'Edit', array($agent->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>

{{ $agents->links() }}

@else
	There are no Agents
@endif
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/agents.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop