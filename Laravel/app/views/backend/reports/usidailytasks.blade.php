
<table width="800" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="0">
	<tbody>
		<tr>
			<td bgcolor="#ffffff"><h3>USI Reminders</h3></td>
		</tr>
		@if(count($messages) > 0)
		<tr>
			<td>
				<table width="100%" bgcolor="#F0F0F0" border="0" cellspacing="1" cellpadding="3">
					@foreach ( $messages as $key => $course )
					<tr>
						<td colspan="2" bgcolor="#ffffff"><b>{{isset($course['course_name']) ? $course['course_name'] : 'No Students'}}</b></td>
					</tr>
					@if(isset($course['messages']))
						@foreach ( $course['messages'] as $message )
						<tr>
							<td bgcolor="#ffffff" width="30">&nbsp;</td>
							<td bgcolor="#ffffff" width="670">{{$message}}</td>
						</tr>
						@endforeach
					@endif
					@endforeach
				</table>
			</td>
		</tr>
		@endif
		<tr>
			<td bgcolor="#ffffff"><hr /></td>
		</tr>
	</tbody>
</table>

