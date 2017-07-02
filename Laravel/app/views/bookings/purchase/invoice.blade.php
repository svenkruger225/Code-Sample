
	<div id="invoice-details" class="well" data-bind="with: purchase().Invoice">
        <div class="row-fluid">
            <div class="row-fluid">
                <div class="span8"><b>Invoice Id: </b></div>
                <div class="span4 text-right"><span data-bind="html: InvoiceId"></span></div>
            </div>
            <div class="row-fluid">
                <div class="span8"><b>Invoice Total: </b></div>
                <div class="span4 text-right"><span data-bind="html: InvoiceTotal.Price"></span></div>
            </div>
			<!-- ko if: InvoiceDiscount() > 0 -->
            <div class="row-fluid">
                <div class="span8"><b>Invoice Discount: </b></div>
                <div class="span4 text-right"><span data-bind="html: InvoiceDiscount.Price"></span></div>
            </div>
		    <!-- /ko -->
			<!-- ko if: InvoiceFeeRebook() > 0 -->
            <div class="row-fluid">
                <div class="span8"><b>Invoice Fee Rebook: </b></div>
                <div class="span4 text-right"><span data-bind="html: InvoiceFeeRebook.Price"></span></div>
            </div>
		    <!-- /ko -->
            <div class="row-fluid">
                <div class="span8"><b>Invoice Paid: </b></div>
                <div class="span4 text-right"><span data-bind="html: InvoicePaid.Price"></span></div>
            </div>
            <div class="row-fluid">
                <div class="span8"><b>Invoice Owing: </b></div>
                <div class="span4 text-right"><span data-bind="html: InvoiceOwing.Price"></span></div>
            </div>
        </div> 
    </div>    

