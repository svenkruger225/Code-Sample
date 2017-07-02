@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
CMS Page Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>CMS Page Management
		<div class="pull-right">
			<a href="{{ route('backend.cms.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>

@if (count($pages) > 0)
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th class="span1">Id</th>
				<th class="span1">ParentId</th>
				<th class="span3">Name</th>
				<th class="span4">Title</th>
				<th class="span3">Url</th>
				<th class="span1">Order</th>
				<th class="span1">Has Content</th>
				<th class="span1">Version</th>
				<th class="span1">Active</th>
				<th></th>
			</tr>
		</thead>

		<tbody>
			@foreach ($pages as $page)
				<tr>
					<td>{{{ $page->id }}}</td>
					<td>{{{ $page->parent_id }}}</td>
					<td class="td-wrap">{{{ $page->name }}}</td>
					<td class="td-wrap">{{{ $page->title }}}</td>
					<td>{{{ $page->url }}}</td>
					<td>{{{ $page->order }}}</td>
					<td>{{{ $page->has_content }}}</td>
					<td>{{{ $page->version }}}</td>
					<td>{{{ $page->active == 1 ? 'x' : '' }}}</td>
	                {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.cms.destroy', $page->id))) }}
					<td>
						{{ link_to_route('backend.cms.edit', 'Page', array($page->id), array('class' => 'btn btn-mini btn-info')) }}
						{{ link_to_route('backend.content.content', "Content", array($page->id), array('class' => 'btn btn-mini btn-info')) }}
						<a href="{{ route('backend.cms.clone', array($page->id)) }}" class="btn btn-mini btn-primary" title="Clone Page and its contents">Clone</a>
						{{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
					</td>
	                {{ Form::close() }}
				</tr>
				@foreach ($page->children as $pg)
					<tr>
						<td>{{{ $pg->id }}}</td>
						<td>{{{ $pg->parent_id }}}</td>
						<td class="td-wrap">{{{ $pg->name }}}</td>
						<td class="td-wrap">{{{ $pg->title }}}</td>
					<td>{{{ $pg->url }}}</td>
						<td><span class="pull-right">{{{ $pg->order }}}</span></td>
					<td>{{{ $pg->has_content }}}</td>
						<td>{{{ $pg->version }}}</td>
						<td>{{{ $pg->active == 1 ? 'x' : '' }}}</td>
	                    {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.cms.destroy', $pg->id))) }}
						<td>
							{{ link_to_route('backend.cms.edit', 'Page', array($pg->id), array('class' => 'btn btn-mini btn-info')) }}
							{{ link_to_route('backend.content.content', "Content", array($pg->id), array('class' => 'btn btn-mini btn-info')) }}
							<a href="{{ route('backend.cms.clone', array($pg->id)) }}" class="btn btn-mini btn-primary" title="Clone Page and its contents">Clone</a>
							{{ Form::button('Delete', array('class' => 'btn btn-mini btn-danger deleteCmd')) }}
						</td>
	                    {{ Form::close() }}
					</tr>
				@endforeach
				<tr>
					<td colspan="10"><hr /></td>
				</tr>
			@endforeach



		</tbody>
	</table>

@else
	There are no Pages
@endif
</div>	
<script src="/_scripts/src/app/require.config.js"></script>
<script data-main="/_scripts/src/app/bootstrapers/cms.js" src="/_scripts/src/bower_modules/requirejs/require.js"></script>

@stop