@extends('backend/layouts/vanilla')

{{-- Page title --}}
@section('title')
Contact Us ::
@parent
@stop

{{-- Page content --}}
@section('content')

<table style="width: 600px; border-color: #666;" cellpadding="10">
	<tr style='background: #e8e8e8;'>
		<td colspan='2'>Contact Details</td>
	</tr>
	<tr>
		<td width="20%"><strong>Name:</strong> </td>
		<td width="80%">{{{$input['name']}}}</td></tr>
	<tr>
		<td><strong>Email:</strong> </td>
		<td>{{{$input['email']}}}</td></tr>
	<tr>
		<td><strong>Phone/Mobile:</strong> </td>
		<td>{{{$input['phone']}}}</td>
	</tr>		
	<tr>
		<td><strong>Location:</strong> </td>
		<td>{{{$input['location']}}}</td>
	</tr>		
	<tr>
		<td><strong>Subject:</strong> </td>
		<td>{{{$input['subject']}}}</td>
	</tr>		
	<tr style='background: #e8e8e8; white-space:pre-wrap !important; word-wrap: break-word !important; overflow: hidden; vertical-align:text-top;'>
		<td colspan='2'>Message</td>
	</tr>
	<tr>
		<td colspan='2'>{{{$input['message']}}}</td>
	</tr>
</table>


@stop