@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Page Header Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div class="page-header">
	<h3>
		Page Header Update

		<div class="pull-right">
			<a href="{{ route('backend.cms.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h3>
</div>
{{ Form::model($page, array('method' => 'PATCH', 'route' => array('backend.content.update', $page->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
	
			<div class="control-group {{ $errors->has('header') ? 'error' : '' }}">
				<label class="control-label" for="header">Header: </label>
			</div>

			<div class="control-group {{ $errors->has('header') ? 'error' : '' }}">
					{{ Form::textarea('header', '', array('id'=>'header', 'class'=>'input-xxlarge')) }}
					{{ $errors->first('header', '<span class="help-inline">:message</span>') }}
			</div>


		</div>
	</div>	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
			<div class="controls">
				{{ Form::submit('Update', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.cms.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
			</div>
		</div>
	</div>

{{ Form::close() }}


<script type="text/javascript">
    $(document).ready(function() {
		CKEDITOR.replace( 'header', {  
							on: {
									instanceReady: function(ev)
									{
									ev.editor.resize( '100%', '500', true );;
									}
								}
							});
    });
</script>



@stop	