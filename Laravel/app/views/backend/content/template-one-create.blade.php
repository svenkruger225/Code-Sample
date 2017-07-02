@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Page Content Create ::
@parent
@stop

{{-- Page content --}}
@section('content')
<script type="text/javascript" src="/_scripts/src/bower_modules/ckeditor/ckeditor.js"></script>
<div class="page-header">
	<h4>Create - @if ($page->parent_id != 0) {{$page->parent->name}} : @endif {{$page->name}}

		<div class="pull-right">
			<a href="{{ route('backend.cms.index') }}" class="btn btn-small btn-inverse"><i class="icon-circle-arrow-left icon-white"></i> Back</a>
		</div>
	</h4>
</div>
{{ Form::open(array('route' => 'backend.content.store', 'class'=>'form-horizontal')) }}
	<!-- CSRF Token -->
	{{ Form::token() }}
	{{ Form::hidden('type', $page->view_name) }}
	{{ Form::hidden('cms_page_id', $page->id) }}

	<!-- Tabs Content -->
	<div class="tab-content">
			<div class="control-group">
				{{ Form::textarea('content', '', array('id'=>'content', 'class'=>'input-xxlarge')) }}
			</div>

			<div class="control-group">
				{{ Form::textarea('blocks', '', array('id'=>'blocks', 'class'=>'input-xxlarge')) }}
			</div>
	</div>	
	<hr />
	
	<!-- Form Actions -->
	<div class="footer">
		<div class="control-group">
				{{ Form::submit('Create', array('class' => 'btn btn-small btn-info')) }}
				{{ link_to_route('backend.cms.index', 'Cancel', array(), array('class' => 'btn btn-small')) }}
				{{ Form::reset('Reset', array('class' => 'btn btn-small btn-danger')) }}
		</div>
	</div>

{{ Form::close() }}


<script type="text/javascript">
    $(document).ready(function() {
		CKEDITOR.replace( 'content', {  on: {instanceReady: function(ev) {ev.editor.resize( '100%', '600', true );}	}	});
		CKEDITOR.replace( 'blocks', {  on: {instanceReady: function(ev) {ev.editor.resize( '100%', '200', true );}	}	});
    });
</script>



@stop	