		@foreach ($result as $key => $res)
			<tr>
				<td>
					<button class="btn btn-mini" data-bind="click: openBookingForm.bind($data,'{{$res['order_id']}}','{{$res['order_type']}}')" >Edit</button>
					<button class="btn btn-mini" data-bind="click: deactivateOrder.bind($data,'{{$res['order_id']}}','{{$res['order_type']}}')"><span id="deactivateList{{$res['order_id']}}">@if ($res['status'] == 'Cancelled') activate @else deactivate @endif</span></button>
					<b>&nbsp;|&nbsp;</b>
					<button class="btn btn-mini" data-bind="click: openInvoicingForm.bind($data,'{{$res['order_id']}}','{{$res['order_type']}}')" >Invoicing</button>
					<button class="btn btn-mini" data-bind="click: openHistoryForm.bind($data,'{{$res['order_id']}}','{{$res['order_type']}}')" >History</button>
				</td>
				<td><button class="btn btn-mini" data-bind="click: openUpdateCustomerForm.bind($data,'{{$res['customer_id']}}')" >{{$res['customer_id']}}</button></td>
                <td>{{$res['order_id']}}</td>
                <td><button class="btn btn-mini" data-bind="click: displayItems.bind($data,'{{$res['order_id']}}')" ><i class="icon-folder-open icon-white"></i> <span class="showHide" id="showHideItemsList{{$res['order_id']}}">Show</span></button></td>
                <td><span class="editAlt">{{$res['agent']}}</span></td>
                <td><span class="editAlt">{{$res['first_name']}} {{$res['last_name']}}</span></td>
				<td><span class="editAlt">{{$res['email']}}</span></td>
				<td></td>
				<td>{{$res['mobile']}}</td>
				<td>${{$res['paid']}}</td>
				<td>${{$res['owing']}}</td>
				<td>{{$res['updated_at']}}</td>
			</tr>
			<!-- ko if: $data.itemsList().length > 0 && $data.orderId() == '{{$res['order_id']}}' -->
			<tr><td colspan="14">
			<table class="table table-condensed">
			<tr>
				<td class="span1">Course</td>
				<td class="span1">Group</td>
				<td class="span1">Voucher</td>
				<td class="span1">Product</td>
				<td class="span1">Type</td>
				<td class="span3">Description</td>
				<td class="span1">Qty</td>
				<td class="span1">Price</td>
				<td class="span1">Gst</td>
				<td class="span1">Total</td>
				<td class="span1">Active</td>
			</tr>
			<!-- ko foreach: $data.itemsList -->
			<tr>
				<td><span data-bind="html: CourseId"><span></td>
				<td><span data-bind="html: GroupId"><span></td>
				<td><span data-bind="html: VoucherId"><span></td>
				<td><span data-bind="html: ProductId"><span></td>
				<td><span data-bind="html: Type"><span></td>
				<td><span data-bind="html: Description"><span></td>
				<td><span data-bind="html: Qty"><span></td>
				<td><span data-bind="html: Price"><span></td>
				<td><span data-bind="html: Gst"><span></td>
				<td><span data-bind="html: Total"><span></td>
				<td><span data-bind="html: Active"><span></td>
			</tr>
			<!-- /ko -->
			</table>
			</td></tr>
			<!-- /ko -->
		@endforeach


