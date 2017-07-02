<style>
#cbConfirmCourses {	font-size:11px;}
#separtor-rule {
	line-height:10px !important;
}
</style>

<div id="cbConfirmCourses" class="row-fluid">
	<div class="well span12" data-bind="with: $data.purchase">
		<div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10">Selected Products</div>
			<div class="span2 pull-right">remove</div>
		</div>
		<div class="row-fluid" data-bind="template: { name: 'template', foreach: Instances }" id="listOfInstances"></div>
		<div class="row-fluid" data-bind="'visible': Instances().length > 0">
			<div class="span10"><b>Pay Total</b></div>
			<div class="span2 pull-right"><span data-bind="html: $data.Total"></span></div>
		</div>
	</div> 
</div>

<script id="template" type="text/html">

	<table class="table table-striped table-condensed table-hover">
		    <tr class="info">
				<td class="span1"><b>Product: </b></td>
				<td class="info"><span data-bind="html: $data.product_name"></span> <span data-bind="html: $data.product_description"></span></td>
				<td class="span1"><button class="btn btn-danger btn-mini" data-bind="attr: { id: $data.id }, click: $root.removeProduct.bind($data, $data.id)"><i class="icon-white icon-remove-sign"></i></button></td>
			</tr>
			<tr>
				<td class="span1"><b>Hire Date: </b></td>
				<td colspan="2"><input class="hired_date span4" data-bind="value: $data.hire_date" /> <small>* for machine hire only</small></td>
			</tr>
			<tr>
				<td class="span1"><b>Qty: </b></td>
				<td colspan="2"><select class="input-mini" data-bind="options: $root.qtys, value: $data.qty, attr : { id: 'qty' + $data.id() }" ></select></td>
			</tr>
			<tr>
				<td class="span1"><b>Price: </b></td>
				<td colspan="2"><div class="span4"> Now: $<input class="span6" data-bind="value: $data.price" /> : </div><div class="span4"> Total: $<span data-bind="html: $data.total" /></div></td>
			</tr>
	</table></script>






