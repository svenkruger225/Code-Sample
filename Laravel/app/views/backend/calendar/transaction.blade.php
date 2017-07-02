    <div class="modal hide modal-payment" id="new-transaction-details" data-bind="with: selectedPayment">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>New Transaction Form</h3>
	    </div>
        <div class="modal-body-payment form-horizontal table-condensed">
		    <div class="control-group control-group-small">
				<label class="control-label" for="order_id">Order Id: </label>
				<div class="controls"><span class="input-small" data-bind="text: OrderId()"/></div>
			</div>
		    <div class="control-group control-group-small">
				<label class="control-label" for="payment_date">Payment_date: </label>
				<div class="controls"><input type="text" class="input-small" data-bind="datepicker: TransactionDate, datepickerOptions: $root.datepickerOptions"/></div>
			</div>
		    <div class="control-group control-group-small">
				<label class="control-label" for="payment_method_id">Payment_method: </label>
				<div class="controls">
					{{ Form::select('method', $methodsCode, '', array('class'=>'input-medium', 'data-bind'=>'value: PaymentMethod')) }}				
				</div>
			</div>
		    <div class="control-group control-group-small">
				<label class="control-label" for="backend">Backend: </label>
				<div class="controls"><span class="input-small" data-bind="text: Backend"/></div>
			</div>
		    <div class="control-group control-group-small">
				<label class="control-label" for="comments">Comments: </label>
				<div class="controls"><input type="text" class="input-xxlarge" data-bind="value: Comments"/></div>
			</div>
		    <div class="control-group control-group-small">
				<label class="control-label" for="total">Total: </label>
				<div class="controls"><input id="transaction_total" type="text" class="transaction_total input-small" data-bind="value: Amount"/></div>
			</div>
        </div >
		<div class="control-group control-group-small">
	        <div class="control-group">
	            <button class="btn btn-primary" data-bind="click: $root.newTransactionCmd"><i class="icon-white icon-thumbs-up"></i> Create Transaction</button>
	        </div>
	    </div>
    </div>


