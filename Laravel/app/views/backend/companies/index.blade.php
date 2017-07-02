@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Companies Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Companies Management

		<div class="pull-right">
			<a href="{{ route('backend.companies.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>


@if (count($companies) > 0)

{{ $companies->links() }}

	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
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
			@foreach ($companies as $company)
				<tr>
					<td>{{{ $company->name }}}</td>
					<td>{{{ $company->email }}}</td>
					<td>{{{ $company->phone }}}</td>
					<td>{{{ $company->mobile }}}</td>
					<td>{{{ $company->fax }}}</td>
					<td>{{{ $company->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.companies.destroy', $company->id))) }}
                    <td>
						{{ link_to_route('backend.companies.edit', 'Edit', array($company->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>

{{ $companies->links() }}

@else
	There are no Companies
@endif
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/companies.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop