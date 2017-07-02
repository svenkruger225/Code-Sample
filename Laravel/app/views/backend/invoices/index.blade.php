@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Invoices Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Invoices Management</h4></div>
		<div class="span2 pull-right">
			<a href="{{ route('backend.invoices.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">  
			{{ Form::open(array('method' => 'GET', 'route' => 'backend.invoices.index', 'class'=>'form-inline')) }}
				{{ Form::text('from', Input::old('from'), array('id'=>'date_from','class'=>'input-small')) }}
				{{ Form::text('to', Input::old('to'), array('id'=>'date_to','class'=>'input-small')) }}
				{{ Form::submit('Load', array('class' => 'btn btn-info loadCmd')) }}
			{{ Form::close() }}
		</div> 
	</div>
</div>

@if (count($invoices) > 0)
{{ $invoices->appends(array('from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}

	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th width="10%">Invoice_id</th>
				<th width="10%">Order_id</th>
				<th width="10%">Invoice_date</th>
				<th width="30%">Comments</th>
				<th width="10%">Status</th>
				<th width="20%">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($invoices as $invoice)
				<tr>
					<td>{{{ $invoice->id }}}</td>
					<td><a href="/backend/booking/search/{{{ $invoice->order_id }}}" data-bind="attr: { href : '/backend/booking/search/{{{ $invoice->order_id }}}' }" class="btn btn-mini" >{{{ $invoice->order_id }}} </a> 
					</td>
					<td>{{{ $invoice->invoice_date }}}</td>
					<td>{{{ $invoice->comments }}}</td>
					<td>{{{ $invoice->status->name }}}</td>
                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.invoices.destroy', $invoice->id))) }}
                    <td>
                        {{ link_to_route('backend.invoices.download', 'Email', array($invoice->id), array('class' => 'btn btn-mini btn-default')) }}
                        {{ link_to_route('backend.invoices.download', 'Download', array($invoice->id), array('class' => 'btn btn-mini btn-success')) }}
						<a href="{{ route('backend.invoices.edit', array($invoice->id)) }}?from={{Input::old('from')}}&to={{Input::old('to')}}" class="btn btn-mini btn-info">Edit</a>
						{{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
                    </td>
                    {{ Form::close() }}
				</tr>
			@endforeach
		</tbody>
	</table>
{{ $invoices->appends(array('from'=>Input::old('from'),'to'=>Input::old('to')))->links() }}
@else
	There are no invoices
@endif
</div>
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/invoices.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop
