    <div class="modal hide modal-payment" id="bulk-message-modal" data-bind="with: selectedMessage">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Bulk Message Form [ <span data-bind="text: Type"></span> ]</h3>
	    </div>
        <div class="modal-body-payment form-horizontal table-condensed">
					<div class="control-group control-group-small">
		                <label class="control-label" for="sendEmail">Send Email :</label>
		                <div class="controls"><input type="checkbox" name="sendEmail" id="sendEmail" value="1" data-bind="checked: SendEmail"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="sendSms">Send Sms :</label>
		                <div class="controls"><input type="checkbox" name="sendSms" id="sendSms" value="1" data-bind="checked: SendSms"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="subject">Subject :</label>
		                <div class="controls"><input type="text" name="subject" id="subject" class="input-xlarge" data-bind="value: Subject"/></div>
		            </div>
					<div class="control-group control-group-small">
		                <label class="control-label" for="message">Message :</label>
		                <div class="controls"><textarea name="message" id="message" rows="6" class="input-xxlarge" data-bind="value: Message"></textarea></div>
		            </div>
					<div class="control-group control-group-small">
		                <div class="controls pull-right">
							<button class="btn btn-primary" data-bind="click: submitBulkMessagesCmd">
								<i class="icon-white icon-thumbs-up"></i> Submit</button>
						</div>
		            </div>
        </div >
    </div>


