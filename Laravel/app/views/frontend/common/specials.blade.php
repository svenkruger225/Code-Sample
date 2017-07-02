@extends('frontend/layouts/default')

{{-- Page title --}}
@section('title')
Specials ::
@parent
@stop

{{-- Page content --}}
@section('content')

	<div class="row-fluid">
	<div class="controls">
		<span class="location-selector">Select specials location <i class="icon-chevron-right"></i> </span> <select class="location-selector" onchange="window.location=this.value">
			@foreach ( $locations as $location )
			<option value="/specials/{{$location->name}}" {{strtolower($page->location_name) == strtolower($location->name) ? 'selected' : '' }}>{{strtoupper($location->name)}}</option>
			<optgroup style="margin: 1px 0;"></optgroup>
			@endforeach
		</select>
	</div>
	</div>
	<div class="row-fluid">
	
		{{$page->content_top }}
		
		<table class="tableizer-table">
			<tr class="tableizer-firstrow">
				<th width="410" align="left">SPECIALS {{$page->location_name}}</th>
				<th width="83" align="right" bgcolor="#22a6de">Special Price</th>
				<th width="83" align="right">Regular Price</th>
				<th width="78" align="right" bgcolor="#f26924">SAVE</th>
				<th width="60" align="right">BOOK</th>
			</tr>
			@foreach( $specials as $special)
			<tr>
				<td align="left" valign="top">{{$special->course_name}} on <b>{{date('d/m/Y', strtotime($special->course_date))}} at {{$special->time_start}} : {{$special->time_end}}</b></td>
				<td align="right" valign="top" bgcolor="#22a6de">${{$special->price_online}}</td>
				<td align="right" valign="top">${{$special->price_original}}</td>
				<td align="right" valign="top" bgcolor="#f26924">${{$special->savings}}</td>
				<td><p><a class="btn" href="{{$special->booking}}"><span style="font-size:18px; font-weight:bold; font-family: 'Economica', sans-serif;">&gt;</span></a></p></td>
			</tr>
			@endforeach
			<tr>
				<td align="left" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td>
			</tr>
			<tr>
				<td align="left" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td> 
				<td align="right" valign="top" class="noborder">&nbsp;</td>
				<td align="right" valign="top" class="noborder">&nbsp;</td>
			</tr>
			<tr>
				<th align="left" valign="top" bgcolor="#42205d"><strong>PACKAGES</strong></th>
				<th align="right" valign="top" ></th>
				<th align="right" valign="top" ></th> 
				<th align="right" valign="top" ></th>
				<th align="right" valign="top" ></th>
			</tr>
			@foreach( $bundles as $bundle)
			<tr>
				<td align="left" valign="top">{{$bundle->name}}</td>
				<td align="right" valign="top" bgcolor="#22a6de">${{$bundle->total_online}}</td>
				<td align="right" valign="top">${{$bundle->total_original}}</td>
				<td align="right" valign="top" bgcolor="#f26924">${{$bundle->savings}}</td>
				<td><p><a class="btn" href="{{$bundle->booking}}"><span style="font-size:18px; font-weight:bold; font-family: 'Economica', sans-serif;">&gt;</span></a></p></td>
			</tr>
			@endforeach
		</table>

		{{$page->content_bottom }}

	</div><!--/row-->
	
@stop
