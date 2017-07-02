    <div class="modal hide modal-payment" id="invoicing-update-details" data-bind="with: selectedOrder">
        <form id="paymentDetailsForm" name="paymentDetailsForm" method="post" action="">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Invoicing Form</h3>
	    </div>
        <div class="modal-body-payment form-horizontal table-condensed">
		    <div class="tab-content">
		        <div class="tab-pane active" id="InvoiceDetails">  
					<div class="control-group control-group-small">
		                <label class="control-label" for="">Order Id :</label>
		                <div class="controls"><span data-bind="text: order_id"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="">Invoice Id :</label>
		                <div class="controls"><span data-bind="text: invoice_id"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="">Paid :</label>
		                <div class="controls"><span data-bind="text: paid"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="">Owing :</label>
		                <div class="controls"><span data-bind="text: owing"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="">Transactions :</label>
						<div class="span7">
							<table class="table table-striped table-bordered table-condensed table-hover ">
								<thead>
									<tr>
										<th class="span1">Date</th>
										<th class="span1">Method</th>
										<th class="span3">Comments</th>
										<th class="span1">Status</th>
										<th class="span1">Total</th>
									</tr>
								</thead>
								<tbody id="courses_list" data-bind="foreach: payments">
									<tr>
										<td><span data-bind="text: TransactionDate" class="input-small" /></td>
										<td><span data-bind="text: PaymentMethod" class="input-small" /></td>
										<td><span data-bind="text: Comments" class="input-large" /></td>
										<td><span data-bind="text: Status" class="input-mini" /></td>
										<td><span data-bind="text: Amount" class="input-mini" /></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
                </div>
            </div>
        </div >
	    <div class="modal-footer ">
			<div class="control-group control-group-small">
					<span> <input type="checkbox" data-bind="checked: recreate_invoice">re-create invoice </span>
					<span><button class="btn btn-primary" data-bind="click: resendInvoiceEmailCmd.bind($data, order_id)" href="#">
						<i class="icon-white icon-plus-sign"></i> Email Invoice</button></span>
					<span><a class="btn btn-primary" data-bind="attr: {'href' : '/backend/invoices/download/' + invoice_id() + '/' + recreate_invoice()}" href="#" target="_blank">
						<i class="icon-white icon-plus-sign"></i> Download Invoice</a></span>
					<span><button class="btn btn-primary" data-bind="click: showNewTransactionModalCmd.bind($data, order_id)">
						<i class="icon-white icon-plus-sign"></i> New Transaction</button></span>
	        </div>
	    </div>
	    </form>
    </div>


