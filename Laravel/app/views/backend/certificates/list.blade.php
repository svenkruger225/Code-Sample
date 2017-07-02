@extends('backend/layouts/default')

@section('content')
<div id="content">
<div class="page-header">
	<div class="row-fluid">
		<div class="span4"><h4>Certificates Management</h4></div>
	</div>
</div>

@if ($students->count())
			@foreach ($students as $student)
                {{ Form::open(array('method' => 'Post', 'route' => array('backend.certificates.save'))) }}
				<div class="well">
	<table class="table table-bordered table-striped table-condensed table-hover">
		<tbody>
				<tr>
					<td class="span2">{{ Form::hidden('id', $student->id) }}
						{{ Form::text('first_name', $student->customer->first_name, array('class'=>'input-medium')) }}
					<td class="span2">{{ Form::text('last_name', $student->customer->last_name, array('class'=>'input-medium')) }}</td>
					<td class="span1">{{ Form::text('dob', $student->customer->dob, array('class'=>'input-small')) }}</td>
					<td class="span1">{{ Form::text('mobile', $student->customer->mobile, array('class'=>'input-small')) }}</td>
					<td class="span2">{{ Form::text('phone', $student->customer->phone, array('class'=>'input-medium')) }}</td>
					<td class="span2">{{ Form::text('email', $student->customer->email, array('class'=>'input-large')) }}</td>
				</tr>
				<tr>
					<td class="span2">{{ Form::text('address', $student->customer->address, array('class'=>'input-medium')) }}</td>
					<td class="span2">{{ Form::text('city', $student->customer->city, array('class'=>'input-medium')) }}</td>
					<td class="span1">{{ Form::select('state', $states, $student->customer->state, array('class'=>'input-medium')) }}</td>
					<td class="span1">{{ Form::text('post_code', $student->customer->post_code, array('class'=>'input-small')) }}</td>
					<td class="span2">{{ Form::text('notes', $student->customer->notes, array('class'=>'input-large')) }}</td>
                    <td class="span2">
                        <div class="pull-left">{{ Form::submit('Save & Download', array('class' => 'btn btn-success')) }}</div>
                        <div class="pull-right">{{ Form::submit('Save', array('class' => 'btn btn-success')) }}</div>
                    </td>
				</tr>
		</tbody>
	</table>
				</div>
                {{ Form::close() }}
			@endforeach
@else
	There are no students
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/certificates.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>
@stop