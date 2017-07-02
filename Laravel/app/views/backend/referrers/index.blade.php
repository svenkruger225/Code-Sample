@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Referrers Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="page-header">
	<h3>
		Referrers Management

		<div class="pull-right">
			<a href="{{ route('backend.referrers.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>


@if (count($referrers) > 0)

{{ $referrers->links() }}

	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th class="span3">Name</th>
				<th class="span3">Url</th>
				<th class="span3">Ad Id</th>
				<th class="span1">Order</th>
				<th class="span1">Active</th>
				<th class="span2">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($referrers as $referrer)
				<tr>
					<td>{{{ $referrer->name }}}</td>
					<td>{{{ $referrer->url }}}</td>
					<td>{{{ $referrer->ad_id }}}</td>
					<td>{{{ $referrer->order == '999999' ? '' : $referrer->order }}}</td>
					<td>{{{ $referrer->active }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.referrers.destroy', $referrer->id))) }}
                    <td>
						{{ link_to_route('backend.referrers.edit', 'Edit', array($referrer->id), array('class' => 'btn btn-mini btn-info')) }}
                        {{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>

{{ $referrers->links() }}

@else
	There are no Referrers
@endif
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/referrers.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop