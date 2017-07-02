<div class="online-panel-body">
	<div class="table-responsive" data-bind="with: $data.booking">
		<table class="table table-striped table-condensed">
			<tr class="row">
				<td class="col-md-5">Course</td>
				<td class="col-md-3">$ Pay Later</td>
				<td class="col-md-3">$ Pay Now</td>
				<td class="col-md-1"></td>
			</tr>
			<!-- ko template: { name: 'comfirmation1-template', foreach: Instances } --> <!-- /ko -->
			<hr >
			<tr class="row">
				<td class="col-md-5"><span class="pull-right">Totals: </span></td>
				<td class="col-md-3"><span data-bind="html: $data.OnLineTotal.Price"></span></td>
				<td class="col-md-3"><span data-bind="html: $data.OffLineTotal.Price"></span></td>
				<td class="col-md-1"></td>
			</tr>
		</table>
	</div>
</div>
<script id="comfirmation1-template" type="text/html">
<tr class="row">
	<td class="col-md-6">
		<span data-bind="html: $data.courseName"></span>
	</td>
	<td class="col-md-3">
		<span data-bind="text: '$' + $data.priceOffLine()"></span>
	</td>
	<td class="col-md-3">
		<span data-bind="text: '$' + $data.priceOnLine()"></span>
	</td>
	<td class="col-md-1">
		<button title="Remove Course" class="btn btn-danger btn-xs" data-bind="attr: { id: $data.courseInstance }, click: $root.removeInstance.bind($data, $data.courseInstance), disable: $data.isPaid"><i class="glyphicon glyphicon-remove"></i></button>
	</td>
</tr>
</script>

