@extends('layouts.scaffold')

@section('main')

<h1>Show CourseRepeat</h1>

<p>{{ link_to_route('backend.courserepeats.index', 'Return to all courseRepeats') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Course_id</th>
				<th>Location_id</th>
				<th>Monday</th>
				<th>Tuesday</th>
				<th>Wednesday</th>
				<th>Thursday</th>
				<th>Friday</th>
				<th>Saturday</th>
				<th>Sunday</th>
				<th>Time_start</th>
				<th>Time_end</th>
				<th>Maximum_students</th>
				<th>Maximum_alert</th>
				<th>Maximum_auto</th>
				<th>Start_date</th>
				<th>End_date</th>
				<th>Active</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $courseRepeat->course_id }}}</td>
					<td>{{{ $courseRepeat->location_id }}}</td>
					<td>{{{ $courseRepeat->monday }}}</td>
					<td>{{{ $courseRepeat->tuesday }}}</td>
					<td>{{{ $courseRepeat->wednesday }}}</td>
					<td>{{{ $courseRepeat->thursday }}}</td>
					<td>{{{ $courseRepeat->friday }}}</td>
					<td>{{{ $courseRepeat->saturday }}}</td>
					<td>{{{ $courseRepeat->sunday }}}</td>
					<td>{{{ $courseRepeat->time_start }}}</td>
					<td>{{{ $courseRepeat->time_end }}}</td>
					<td>{{{ $courseRepeat->maximum_students }}}</td>
					<td>{{{ $courseRepeat->maximum_alert }}}</td>
					<td>{{{ $courseRepeat->maximum_auto }}}</td>
					<td>{{{ $courseRepeat->start_date }}}</td>
					<td>{{{ $courseRepeat->end_date }}}</td>
					<td>{{{ $courseRepeat->active }}}</td>
                    <td>{{ link_to_route('backend.courserepeats.edit', 'Edit', array($courseRepeat->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('backend.courserepeats.destroy', $courseRepeat->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop