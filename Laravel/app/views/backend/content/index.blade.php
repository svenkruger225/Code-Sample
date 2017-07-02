@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
CMS Content Management ::
@parent
@stop

{{-- Page content --}}
@section('content')
<div id="content">

<div class="page-header">
	<h3>CMS Content Management
		<div class="pull-right">
			<a href="{{ route('backend.content.create') }}" class="btn btn-small btn-info"><i class="icon-plus-sign icon-white"></i> Create</a>
		</div>
	</h3>
</div>

@if (count($blocks) > 0)
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th class="span1">Block</th>
				<th class="span1">Active</th>
				<th></th>
			</tr>
		</thead>

		<tbody>
			@foreach ($blocks as $block)
				<tr>
					<td>{{{ $block }}}</td>
                    <td>
						{{ link_to_route('backend.content.editblock', 'Edit', array('page_id'=>$page->id, 'block_type'=>$block), array('class' => 'btn btn-mini btn-info')) }}
                    </td>
				</tr>
			@endforeach



		</tbody>
	</table>

@else
	There are Blocks
@endif
</div>	

@stop