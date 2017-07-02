@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
CMS Resources Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>CMS Resources Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.resources.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
</div>

@if (count($resources) > 0)
	<table class="table table-striped table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<th class="span2">id</th>
				<th class="span3">type</th>
				<th class="span2">description</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($resources as $resource)
				<tr>
					<td>{{{ $resource->id }}}</td>
					<td>{{{ $resource->type }}}</td>
					<td>{{{ $resource->description }}}</td>
					<td>{{{ $resource->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.resources.destroy', $resource->id))) }}
                    <td>
						<a href="{{ route('backend.resources.edit', array($resource->id)) }}" class="btn btn-mini btn-info">Edit</a>		
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd', 'data-bind'=> 'click: deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no resources
@endif
</div>

@stop