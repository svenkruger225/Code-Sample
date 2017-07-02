@extends('backend/layouts/default')

@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Certificates Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.certificates.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12"> 
		<h2> 
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.certificates.index', 'class'=>'form-inline')) }}
				{{ Form::text('search_text', Input::old('search_text'), array('id'=>'search_text','class'=>'input-medium')) }}				
				{{ Form::select('search_type', $search_types, Input::old('search_type'), array('class'=>'input-medium search_type')) }}				
				{{ Form::select('l_id', $locations, Input::old('l_id'), array('class'=>'input-medium')) }}				
				{{ Form::select('c_id', $courses, Input::old('c_id'), array('class'=>'input-medium')) }}				
				{{ Form::submit('Search', array('class' => 'btn btn-info searchCmd')) }}
			{{ Form::close() }}
		</h2>
		</div> 
	</div>
</div>

@if (count($certificates) > 0)

{{ $certificates->links() }}
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th>Certificate Id</th>
				<th>Order Id</th>
				<th>Location</th>
				<th>Course</th>
				<th>Roster</th>
				<th>Customer</th>
				<th>Certificate date</th>
				<th>Description</th>
				<th>Status</th>
				<th>Active</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($certificates as $certificate)
				<tr>
					<td>{{{ $certificate->id }}}</td>
					<td>{{{ $certificate->roster ? $certificate->roster->order_id : 'n/a' }}}</td>
					<td>{{{ $certificate->location ? $certificate->location->name : $certificate->location_id }}}</td>
					<td>{{{ $certificate->course ? $certificate->course->name : $certificate->course_id }}}</td>
					<td>{{{ $certificate->roster_id }}}</td>
					<td>{{{ $certificate->customer ? $certificate->customer->full_name : $certificate->customer_id }}}</td>
					<td>{{{ $certificate->certificate_date }}}</td>
					<td>{{{ $certificate->description }}}</td>
					<td>{{{ $certificate->status ? $certificate->status->name : $certificate->status_id }}}</td>
					<td>{{{ $certificate->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.certificates.destroy', $certificate->id))) }}
                    <td>
                        {{ link_to_route('backend.certificates.download', 'Download', array($certificate->id), array('class' => 'btn btn-mini btn-success')) }}
						{{ link_to_route('backend.certificates.edit', 'Edit', array($certificate->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
{{ $certificates->links() }}
@else
	There are no certificates
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/certificates.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop