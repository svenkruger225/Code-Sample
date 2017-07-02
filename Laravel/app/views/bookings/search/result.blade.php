		@foreach ($orders as $order)
			<tr class="{{$order->order_type}} @if ($order->status->name == 'Cancelled') inactive @endif">
				<td>
					<button class="btn btn-mini" data-bind="click: openBookingForm.bind($data,'{{$order->id}}','{{$order->order_type}}')" >Edit</button>
					<button class="btn btn-mini" data-bind="click: openBookingForm.bind($data,'{{$order->id}}','{{$order->order_type}}','Upsell')" >Upsell</button>
					<button class="btn btn-mini" data-bind="click: deactivateOrder.bind($data,'{{$order->id}}','{{$order->order_type}}')"><span id="deactivateList{{$order->id}}">@if ($order->status->name == 'Cancelled') activate @else deactivate @endif</span></button>
					<b>&nbsp;|&nbsp;</b>
					<button class="btn btn-mini" data-bind="click: openInvoicingForm.bind($data,'{{$order->id}}','{{$order->order_type}}')" >Invoicing</button>
					<button class="btn btn-mini" data-bind="click: openHistoryForm.bind($data,'{{$order->id}}','{{$order->order_type}}')" >History</button>
				</td>
				<td><button class="btn btn-mini" data-bind="click: openUpdateCustomerForm.bind($data,'{{$order->customer_id}}')" >{{$order->customer_id}}</button></td>
                <td>{{$order->id}}</td>
                <td><button class="btn btn-mini" data-bind="click: displayItems.bind($data,'{{$order->id}}')" ><i class="icon-folder-open icon-white"></i> <span class="showHide" id="showHideItemsList{{$order->id}}">Show</span></button></td>
                <td><span class="editAlt">{{$order->agent ? $order->agent->name : ($order->company ? $order->company->name : '')}}</span></td>
                <td class="td-wrap"><span class="editAlt">{{$order->customer ? $order->customer->full_name : ''}}</span></td>
				<td><a href="mailto:{{$order->customer ? $order->customer->email : ''}}" title="{{$order->customer ? $order->customer->email : ''}}"><span class="editAlt">{{$order->customer ? $order->customer->email : ''}}</span></a></td>
				<td>{{$order->customer ? $order->customer->phone : ''}}</td>
				<td>{{$order->customer ? $order->customer->mobile : ''}}</td>
				<td>${{$order->paid}}</td>
				<td>${{$order->owing}}</td>
				<td>{{$order->status ? $order->status->name : 'na'}}</td>
				<td>{{$order->updated_at}}</td>
			</tr>
			<!-- ko if: $data.itemsList().length > 0 && $data.orderId() == '{{$order->id}}' -->
			<tr><td colspan="14">
			<table class="table table-condensed">
			<tr>
				<td class="span2">{{($order->groupbooking ? $order->groupbooking->group_name : '')}}</td>
				<td class="span1">ItemId</td>
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
				<td><span> <span></td>
				<td><span data-bind="html: ItemId()"><span></td>
				<td><span data-bind="html: Type"><span></td>
				<td class="td-wrap"><span data-bind="html: Description"><span></td>
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


