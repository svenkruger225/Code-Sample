	<div data-bind="with: $data.booking">
		<div class="row-fluid " data-bind="template: { name: 'comfirmation-template', foreach: Instances }" id="listOfInstances"></div>

		<div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10"><b>Pay Now Total</b></div>
			<div class="span2 pull-right"><span data-bind="html: $data.OnLineTotal.Price"></span></div>
		</div>
		<!--<div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10"><b>Pay Later Total</b></div>
			<div class="span2 pull-right"><span data-bind="html: $data.OffLineTotal.Price"></span></div>
		</div> -->
	</div> 
	
	<script id="comfirmation-template" type="text/html">
		<table class="table table-condensed">
				<tr>
					<td class="span1"><b>Course: </b></td>
					<td class="span10" ><span data-bind="html: $data.courseName"></span> - <span data-bind="html: $data.parentLocationName"></span></td>
					<td class="span1"><button title="Remove Course" class="btn btn-danger btn-mini" data-bind="attr: { id: $data.courseInstance }, click: $root.removeInstance.bind($data, $data.courseInstance), disable: $data.isPaid"><i class="icon-white icon-remove-sign"></i></button></td>
				</tr>
				<tr class="noborder">
					<td class="span1"><b>Date: </b></td>
					<td class="span11" colspan="2"><span data-bind="html: $data.courseDate.Display()"></span></td>
				</tr>
				<tr class="noborder">
					<td class="span1"><b>Address: </b></td>
					<td class="span11" colspan="2"><span data-bind="html: $data.courseAddress"></span></td>
				</tr>
				<tr class="noborder">
					<td class="span1"><b>Students: </b></td>
					<td class="span11" colspan="2"><select class="ipt-mini" data-bind="options: $root.qtyStudents, value: $data.studentQty, attr : { id: 'qty' + $data.id() }, event: {'change': $root.updateInstanceQty.bind($data, $data.courseInstance) }, disable: $data.isPaid" ></select></td>
				</tr>
				<tr class="noborder">
					<td colspan="3">
					<div class="span6"><span data-bind="html: '<b>Price: </b>$' + $data.priceOn()"></span></div>
					<div class="span6"><span data-bind="html: '<b>Total: </b>$' + $data.priceOnLine()"></span></div>
					</td>
				</tr>
				<!--
				<tr class="noborder">
					<td class="span1"><b>Price: </b></td>
					<td class="span11" colspan="2"><span class="span6" data-bind="text: 'Now: $' + $data.priceOn()"></span><span class="span6" data-bind="text: ' :  Later: $' + $data.priceOff()"></span></td>
				</tr>
				<tr class="noborder">
					<td class="span1"><b>Total: </b></td>
					<td class="span11" colspan="2"><span class="span6" data-bind="text: 'Now: $' + $data.priceOnLine()"></span><span class="span6" data-bind="text: ' :  Later: $' + $data.priceOffLine()"></span></td>
				</tr>
				-->
				<tr class="noborder" data-bind="visible: $data.feeRebook() > 0">
					<td class="span1"><b>Rebook fee: </b></td>
					<td class="span11" colspan="2">$<span class="span6" data-bind="html: $data.feeRebook"></span></td>
				</tr>
		</table>
		<hr class="alert-info" />
	</script>
