
<table width="800" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="0">
	<tbody>
		<tr>
			<td bgcolor="#ffffff"><h3>Course SMS Reminders</h3></td>
		</tr>
		@if(count($messages) > 0)
		<tr>
			<td>
				<table width="100%" bgcolor="#F0F0F0" border="0" cellspacing="1" cellpadding="3">
					@foreach ( $messages as $key => $course )
					<tr>
						<td colspan="2" bgcolor="#ffffff"><b>{{$course['course_name']}}</b></td>
					</tr>
					@foreach ( $course['messages'] as $message )
					<tr>
						<td bgcolor="#ffffff" width="30">&nbsp;</td>
						<td bgcolor="#ffffff" width="670">{{$message}}</td>
					</tr>
					@endforeach
					@endforeach
				</table>
			</td>
		</tr>
		@endif
		<tr>
			<td bgcolor="#ffffff"><hr /></td>
		</tr>
		<tr>
			<td bgcolor="#ffffff"><h3>Classes Without Trainers</h3></td>
		</tr>
		@if(count($notrainers) > 0)
		<tr>
			<td>
				<table width="100%" bgcolor="#F0F0F0" border="0" cellspacing="1" cellpadding="3">
					@foreach ( $notrainers as $course )
					<tr>
						<td bgcolor="#ffffff" width="100">{{$course['course_type']}}</td>
						<td bgcolor="#ffffff" width="200"><b>{{$course['course_name']}}</b></td>
						<td bgcolor="#ffffff" width="200">{{$course['location']}}</td>
						<td bgcolor="#ffffff" width="200">{{$course['class']}}</td>
					</tr>
					@endforeach
				</table>
			</td>
		</tr>
		@endif

		<tr>
			<td bgcolor="#ffffff"><hr /></td>
		</tr>
		<tr>
			<td bgcolor="#ffffff"><h3>Course Repeats</h3></td>
		</tr>
		@if(count($repeats) > 0)
		<tr>
			<td>
				<table width="100%" bgcolor="#F0F0F0" border="0" cellspacing="1" cellpadding="3">
					@foreach ( $repeats as $course )
					<tr>
						<td bgcolor="#ffffff" width="300"><b>{{$course['course_name']}}</b></td>
						<td bgcolor="#ffffff" width="400">{{$course['message']}}</td>
					</tr>
					@endforeach
				</table>
			</td>
		</tr>
		@endif
			
	
		<tr>
			<td bgcolor="#ffffff"><hr /></td>
		</tr>
		<tr>
			<td bgcolor="#ffffff"><h3>SMS Account Balance : {{$balance}}</h3></td>
		</tr>
		<tr>
			<td bgcolor="#ffffff"><hr /></td>
		</tr>
		<tr>
			<td bgcolor="#ffffff"><h3>Log files deleted : {{$cleaning}}</h3></td>
		</tr>
	</tbody>
</table>

