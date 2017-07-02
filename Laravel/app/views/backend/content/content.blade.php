@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Page Content Update ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div class="page-header">
	<h4>Update - {{$page->name}} : {{$page->page_title}}

		<div class="pull-right">
			<a href="{{ route('backend.cms.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h4>
</div>
{{ Form::model($page, array('method' => 'PATCH', 'route' => array('backend.content.update', $page->id), 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}

	<!-- Tabs Content -->
	<div class="tab-content">
	
			<div class="control-group {{ $errors->has('content') ? 'error' : '' }}">
				<label class="control-label" for="content">Content: </label>
			</div>

			<div class="control-group {{ $errors->has('content') ? 'error' : '' }}">
					{{ Form::textarea('content', '', array('id'=>'content', 'class'=>'input-xxlarge')) }}
					{{ $errors->first('content', '<span class="help-inline">:message</span>') }}
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
		CKEDITOR.replace( 'content', {  
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