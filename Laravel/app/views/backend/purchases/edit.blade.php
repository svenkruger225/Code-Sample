@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Purchase Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">
<div class="page-header">
	<h3>
		Purchase Update

		<div class="pull-right">
			<a href="{{ route('backend.purchases.index', array('from'=>Input::old('from'),'to'=>Input::old('to'))) }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($purchase, array('method' => 'PATCH', 'route' => array('backend.purchases.update', $purchase->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
		<!-- General tab -->
		<div class="tab-pane active" id="tab-general">
			<div class="control-group {{ $errors->has('id') ? 'error' : '' }}">
				<label class="control-label" for="id">Customer: </label>
				<div class="controls">
					{{ Form::label('l_id', $purchase->id, array('class'=>'input-small')) }}
					{{ $errors->first('id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('location_id') ? 'error' : '' }}">
				<label class="control-label" for="location_id">Location: </label>
				<div class="controls">
					{{ Form::hidden('location_id', $purchase->location_id) }}
					{{ Form::label('location', ($purchase->location ? $purchase->location->name : $purchase->location_id), array('class'=>'input-xlarge')) }}
					{{ $errors->first('customer_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('customer_id') ? 'error' : '' }}">
				<label class="control-label" for="customer_id">Customer: </label>
				<div class="controls">
					{{ Form::hidden('customer_id', $purchase->customer_id) }}
					{{ Form::label('customer', ($purchase->customer ? $purchase->customer->fullName : $purchase->customer_id), array('class'=>'input-xlarge')) }}
					{{ $errors->first('customer_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('order_id') ? 'error' : '' }}">
				<label class="control-label" for="order_id">Order Id: </label>
				<div class="controls">
					{{ Form::hidden('order_id', $purchase->order_id) }}
					{{ Form::label('order', $purchase->order_id, array('class'=>'input-small')) }}
					{{ $errors->first('order_id', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('date_hire') ? 'error' : '' }}">
				<label class="control-label" for="date_hire">Date Hire: </label>
				<div class="controls">
					{{ Form::text('date_hire', $purchase->date_hire, array('class'=>'input-small')) }}
					{{ $errors->first('date_hire', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('notes') ? 'error' : '' }}">
				<label class="control-label" for="notes">Notes: </label>
				<div class="controls">
					{{ Form::textarea('notes', $purchase->notes, array('rows'=> 3,'class'=>'input-xxlarge')) }}
					{{ $errors->first('notes', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group {{ $errors->has('description') ? 'error' : '' }}">
				<label class="control-label" for="description">Description: </label>
				<div class="controls">
					{{ Form::textarea('description', $purchase->description, array('rows'=> 3,'class'=>'input-xxlarge')) }}
					{{ $errors->first('description', '<span class="help-inline">:message</span>') }}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="course_id">Items: </label>
				<div class="controls span10">
					<table class="table table-striped table-bpurchaseed table-parent">
						<thead>
							<tr>
								<th>Class</th>
								<th>Qty</th>
								<th>Price</th>
								<th>Gst</th>
								<th>Total</th>
								<th>Active</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($purchase->order->items as $item)
							<tr>
								<td>
								{{ Form::label('product_id[]', $item->product->name, array('class'=>'input-xlarge course-instance')) }}
								</td>
								<td>{{ Form::label('qty[]', $item->qty, array('class'=>'input-mini price')) }}</td>
								<td>{{ Form::label('price[]', $item->price, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('gst[]', $item->gst, array('class'=>'input-small ')) }}</td>
								<td>{{ Form::label('total[]', $item->total, array('class'=>'input-small')) }}</td>
								<td>{{ Form::label('active[]', $item->active, array('class'=>'input-small')) }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="total">Total: </label>
				<div class="controls">
					{{ Form::label('total', $purchase->order->total, array('class'=>'input-medium', 'id'=>'purchase_total')) }}
				</div>
			</div>
		</div>
	</div>
	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				{{ Form::submit('Update', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.purchases.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}

</div>

@stop
