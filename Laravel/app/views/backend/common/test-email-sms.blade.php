    <div class="modal hide" id="test-email-modal">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Test Email / SMS</h3>
	    </div>
        <div class="modal-body-payment form-horizontal">
			<div class="control-group"></div>
		    <div class="control-group"> 
		        <label class="control-label" for="email">Email :</label>
		        <div class="controls">
					<input type="text" name="email" id="email" value="" data-bind="value: testData().email"/>
				</div>
		    </div>
		    <div class="control-group"> 
		        <label class="control-label" for="email">Mobile :</label>
		        <div class="controls">
					<input type="text" name="mobile" id="mobile" value="" data-bind="value: testData().mobile"/>
				</div>
		    </div>
		</div >
	    <div class="modal-footer ">
	        <div class="control-group">
				<button class="btn btn-success" data-bind="click: submitTestEmail"><i class="icon-white icon-thumbs-up"></i> Submit Test</button>
	        </div>
	    </div>
    </div>
