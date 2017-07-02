@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Vouchers Management ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Vouchers Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.vouchers.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.vouchers.index', 'class'=>'form-inline')) }}
				{{ Form::text('search_text', Input::old('search_text'), array('class'=>'input-medium')) }}
				{{ Form::select('search_type', $search_types, Input::old('search_type'), array('class'=>'input-medium')) }}				
				{{ Form::select('l_id', $locations, Input::old('l_id'), array('class'=>'input-medium')) }}				
				{{ Form::select('c_id', $courses, Input::old('c_id'), array('class'=>'input-medium')) }}				
				{{ Form::text('from', Input::old('from'), array('id'=>'date_from','class'=>'input-small')) }}
				{{ Form::text('to', Input::old('to'), array('id'=>'date_to','class'=>'input-small')) }}
				{{ Form::select('status_id', $statuses, Input::old('status_id'), array('class'=>'input-medium')) }}				
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if (count($vouchers) > 0)
{{ $vouchers->links() }}

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class="span1">Voucher id</th>
				<th class="span3">Customer</th>
				<th class="span3">Course</th>
				<th class="span3">Location</th>
				<th class="span2">Expiry date</th>
				<th class="span1">Status</th>
				<th class="span1">Active</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($vouchers as $voucher)
				<tr>
					<td>{{{ $voucher->id }}}</td>
					<td>{{{ $voucher->customer ? $voucher->customer->full_name : $voucher->customer_id }}}</td>
					<td>{{{ $voucher->course->name }}}</td>
					<td>{{{ $voucher->location ? $voucher->location->name : $voucher->location_id }}}</td>
					<td>{{{ $voucher->expiry_date }}}</td>
					<td>{{{ $voucher->status->name }}}</td>
					<td>{{{ $voucher->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.vouchers.destroy', $voucher->id))) }}
                    <td>
                        {{ link_to_route('backend.vouchers.download', 'Download', array($voucher->id), array('class' => 'btn btn-mini btn-success')) }}
						{{ link_to_route('backend.vouchers.edit', 'Edit', array($voucher->id), array('class' => 'btn btn-mini btn-info')) }}
						{{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
{{ $vouchers->links() }}
@else
	There are no vouchers
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/vouchers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop