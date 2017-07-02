@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Customers Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Customers Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.customers.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.customers.index', 'class'=>'form-inline')) }}
				{{ Form::text('search', Input::old('search'), array('class'=>'input-medium', 'placeholder'=>'Search Name by')) }}				
				{{ Form::submit('Search', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>	
</div>

@if (count($customers) > 0)

{{ $customers->links() }}
<form method="POST" action="/backend/customers/merge" >
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th width="5%">id</th>
				<th width="5%">USI</th>
				<th width="20%">First_name</th>
				<th width="20%">Last_name</th>
				<th width="20%">Email</th>
				<th width="10%">Agent</th>
				<th width="10%">Dob</th>
				<th width="5%">Mail_out_email</th>
				<th width="5%">Mail_out_sms</th>
				<th width="5%">Active</th>
				<th width="5%">Master</th>
				<th width="5%"><button type="button" class="btn btn-mini btn-inverse mergeCmd">Merge</button></th>
				<th width="10%" colspan="2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($customers as $customer)
				<tr>
					<td>{{{ $customer->id }}}</td>
					<td>{{{ $customer->unique_student_identifier }}}</td>
					<td>{{{ $customer->first_name }}}</td>
					<td>{{{ $customer->last_name }}}</td>
					<td>{{{ $customer->email }}}</td>
					<td>{{{ $customer->agent_id }}}</td>
					<td>{{{ $customer->dob }}}</td>
					<td>{{{ $customer->mail_out_email }}}</td>
					<td>{{{ $customer->mail_out_sms }}}</td>
					<td>{{{ $customer->active }}}</td>
					<td>
						<input type="radio" name="master" value="{{ $customer->id }}" />
                    </td>
					<td>
						<input type="checkbox" name="merge[]" value="{{ $customer->id }}" />
                    </td>
					
                    <td>
						{{ link_to_route('backend.customers.edit', 'Edit', array($customer->id), array('class' => 'btn btn-mini btn-info')) }}
						<a href="#" class="btn btn-mini btn-warning" data-bind="click: openMessageForm.bind($data, 'Customer','{{$customer->id}}')"><i class="icon-envelope icon-white"></i> Send Message</a>
						<a href="{{ route('customers/delete/customer', $customer->id) }}" class="btn btn-mini btn-danger deleteCmd">Delete</a>
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
</form>

{{ $customers->links() }}

@include('backend/common/bulk-message')
@include('bookings/common/whatis-usi')


@else
	There are no Customers
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/customers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop