<style>
#cbConfirmCourses {	font-size:11px;}
#separtor-rule {
	line-height:10px !important;
}
.info-row  {
  background-color: #d9edf7;
}
.success-row {
  background-color: #dff0d8;
}
</style>

<div id="cbConfirmCourses" class="row-fluid">
	<div class="well well-small span12" data-bind="with: $data.booking">
		<div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10"><b>Selected Courses</b></div>
			<div class="span2 pull-right">remove</div>
		</div>
		<div class="row-fluid" data-bind="template: { name: 'template', foreach: Instances }" id="listOfInstances"></div>
                <!-- Pay now div -->
                @if ($group !='agent')
                    <div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10"><b>Pay Now Total</b></div>
                        <div class="span2 pull-right"><span data-bind="html: $data.OnLineTotal.Price"></span></div>
                    </div>
                 @else
                        @if ($agentData['payment_type'] =='paynow' || $agentData['payment_type'] =='both')
                            <div class="row-fluid" data-bind="'visible': Instances().length > 0">
                                <div class="span10"><b>Pay Now Total</b></div>
                                <div class="span2 pull-right"><span data-bind="html: $data.OnLineTotal.Price"></span></div>
                            </div>
                        @else
                        @endif
                @endif
                <!-- Pay later div -->
                 @if ($group !='agent')
                    <div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10"><b>Pay Later Total</b></div>
                        <div class="span2 pull-right"><span data-bind="html: $data.OffLineTotal.Price"></span></div>
                    </div>
                 @else
                        @if ($agentData['payment_type'] =='paylater' || $agentData['payment_type'] =='both')
                            <div class="row-fluid" data-bind="'visible': Instances().length > 0">
                                <div class="span10"><b>Pay Later Total</b></div>
                                <div class="span2 pull-right"><span data-bind="html: $data.OffLineTotal.Price"></span></div>
                            </div>
                        @else
                        @endif
                @endif
	</div> 
</div>
<script id="template" type="text/html">
	<table class="table table-striped table-condensed table-hover">
		    <tr class="info">
				<td class="span1"><b>Course: </b></td>
				<td class="info"><span data-bind="html: $data.courseName"></span> - [<span data-bind="html: $data.courseInstance"></span>]</td>
				<td class="span1"><button class="btn btn-danger btn-mini" data-bind="attr: { id: $data.courseInstance }, click: $root.removeInstance.bind($data, $data.courseInstance), disable: $data.isPaid"><i class="icon-white icon-remove-sign"></i></button></td>
			</tr>
			<tr>
				<td class="span1"><b>Date: </b></td>
				<td colspan="2"><span data-bind="html: $data.courseDate.Display()"></span></td>
			</tr>
			<tr>
				<td class="span1"><b>Address: </b></td>
				<td colspan="2"><span data-bind="html: $data.courseAddress"></span></td>
			</tr>
			<tr>
				<td class="span1"><b>Students: </b></td>
				<td colspan="2"><select class="input-mini" data-bind="options: $root.qtyStudents, value: $data.studentQty, attr : { id: 'qty' + $data.id() }, event: {'change': $root.updateInstanceQty.bind($data, $data.courseInstance) }, disable: $data.isPaid" ></select></td>
			</tr>
                        <tr>
				<td class="span1"><b>Price: </b></td>
                                @if ($group !='agent')
                                    <td colspan="2"><div class="span4"> Now: $<input class="span6" data-bind="value: $data.priceOn" /> : </div><div class="span4"> Later: $<input class="span6" data-bind="value: $data.priceOff" /></div></td>
			        @else
                                    <td colspan="2">
                                    @if ($agentData['payment_type'] =='paynow')
                                        <div class="span4"> Now: $<input class="span6" data-bind="value: $data.priceOn" disabled/> : </div>
                                    @elseif($agentData['payment_type'] =='paylater')
                                        <div class="span4"> Later: $<input class="span6" data-bind="value: $data.priceOff" disabled/></div>
                                    @else
                                        <div class="span4"> Now: $<input class="span6" data-bind="value: $data.priceOn" disabled/> : </div><div class="span4"> Later: $<input class="span6" data-bind="value: $data.priceOff" disabled/></div>
                                    @endif
                                    </td>
                                @endif
				</tr>
			<tr>
				<td class="span1"><b>Total: </b></td>
                                @if ($group !='agent')
				<td colspan="2"><div class="span4"> Now: $<span data-bind="html: $data.priceOnLine"></span> : </div><div class="span4"> Later: $<span data-bind="html: $data.priceOffLine"></span></div></td>
                                @else
                                    <td colspan="2">
                                        @if ($agentData['payment_type'] =='paynow')
                                            <div class="span4"> Now: $<span data-bind="html: $data.priceOnLine"></span> : </div>
                                        @elseif($agentData['payment_type'] =='paylater')
                                            <div class="span4"> Later: $<span data-bind="html: $data.priceOffLine"></span></div>
                                        @else
                                            <div class="span4"> Now: $<span data-bind="html: $data.priceOnLine"></span> : </div><div class="span4"> Later: $<span data-bind="html: $data.priceOffLine"></span></div>
                                        @endif
                                    </td>
                                @endif
                        </tr>
			<tr data-bind="visible: $data.feeRebook() > 0">
				<td class="span1"><b>Rebook fee: </b></td>
				<td colspan="2">$<span data-bind="html: $data.feeRebook"></span></td>
			</tr>
			<tr data-bind="visible: $data.discount() > 0">
				<td class="span1"><b>Discount: </b></td>
				<td colspan="2">$<span data-bind="html: $data.discount"></span></td>
			</tr>
	</table>
</script>






