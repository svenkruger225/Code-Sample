<div class="online-panel-body">
	<div class="table-responsive">
		<table class="table table-striped table-condensed">
			<tr class="row">
				<td class="col-md-1"></td>
				<td class="col-md-5">Course</td>
				<td class="col-md-3">Pay Later</td>
				<td class="col-md-3">Pay Now</td>
			</tr>
			@foreach ( $data->courses as $course )
			<tr class="row">
				<td class="col-md-1">
					<input type="hidden" value='{{$course->toJson()}}' data-bind="initObservableArray: allCourses, modelName: 'Instance'" />
					<a href="" class="btn btn-success btn-xs" data-bind="'click': updateSelectedOnlineInstance.bind($data, '{{$course->id}}')" >
					<span class="glyphicon glyphicon-plus"></span> Add</a>
				</td>
				<td class="col-md-6">
					<label style="display:block;">
						<span>{{$course->name}}</span>
					</label>
				</td>
				<td class="col-md-3">
					<span>${{$course->prices[0]->price_offline}}</span>
				</td>
				<td class="col-md-3">
					<span>${{$course->prices[0]->price_online}}</span>
				</td>
			</tr>
			@endforeach
		</table>
	</div>
</div>