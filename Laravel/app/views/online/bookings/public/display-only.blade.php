<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Your Course(s)</h3></div>
	<div class="panel-body">
		<div class="online-panel-body">
			<div class="table-responsive" data-bind="with: $data.booking">
				<table class="table table-striped table-condensed">
					<tr class="row">
						<td class="col-md-8">Course</td>
						<td class="col-md-4">$ Amount</td>
					</tr>
					<!-- ko template: { name: 'comfirmation1-template', foreach: Instances } --> <!-- /ko -->
					<hr >
					<tr class="row">
						<td class="col-md-8"><span class="pull-right">Total to Pay: </span></td>
						<td class="col-md-4"><span data-bind="html: $data.OffLineTotal.Price"></span></td>
					</tr>
				</table>
			</div>
		</div>
		<script id="comfirmation1-template" type="text/html">
		<tr class="row">
			<td class="col-md-8">
				<span data-bind="html: $data.courseName"></span>
			</td>
			<td class="col-md-4">
				<span data-bind="text: '$' + $data.priceOffLine()"></span>
			</td>
		</tr>
		</script>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><h3 class="panel-title">Your Details </h3></div>
	<div class="panel-body">
		<div class="online-panel-body">
			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<tr class="row">
						<td class="col-md-2">First_Name:</td>
						<td class="col-md-10"><span data-bind="text: booking().FirstName"></span></td>
					</tr>
					<tr class="row">
						<td class="col-md-2">Last_Name:</td>
						<td class="col-md-10"><span data-bind="text: booking().LastName"></span></td>
					</tr>
					<tr class="row">
						<td class="col-md-2">DOB:</td>
						<td class="col-md-10"><span data-bind="text: booking().Dob"></span></td>
					</tr>
					<tr class="row">
						<td class="col-md-2">Mobile:</td>
						<td class="col-md-10"><span data-bind="text: booking().Mobile"></span></td>
					</tr>
					<tr class="row">
						<td class="col-md-2">Email:</td>
						<td class="col-md-10"><span data-bind="text: booking().Email"></span></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
