    <div class="modal hide modal-payment" id="user-message-form">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Messaging Form</h3>
	    </div>
        <div class="modal-body-payment form-horizontal table-condensed" data-bind="with: selectedUser">
		        <div class="tab-pane" id="Messaging">  
		            <div class="control-group control-group-small">
						<label class="control-label" for="name">Student: </label>
						<div class="controls"><span class="input-small" data-bind="text: name"/></div>
					</div>
		            <div class="control-group control-group-small">
						<label class="control-label" for="email">Email: </label>
						<div class="controls"><span class="input-small" data-bind="text: email"/></div>
					</div>
		            <div class="control-group control-group-small">
						<label class="control-label" for="mobile">Mobile: </label>
						<div class="controls"><span class="input-small" data-bind="text: mobile"/></div>
					</div>
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
							<button class="btn btn-primary" data-bind="click: $root.submitMessagesCmd.bind($data, customer_id)">
								<i class="icon-white icon-thumbs-up"></i> Submit</button>
						</div>
		            </div>
                </div>
        </div>
    </div>


