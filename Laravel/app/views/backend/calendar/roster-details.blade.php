    <div class="modal hide modal-payment" id="roster-update-details" data-bind="with: selectedRoster">
        <form id="rosterDetailsForm" name="rosterDetailsForm" method="post" action="">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Roster Details Form</h3>
	    </div>
        <div class="modal-body-payment form-horizontal table-condensed">
		    <ul class="nav nav-tabs" id="myTab">
			    <li class="tabs active"><a href="#RosterDetails" data-toggle="tab">Roster Details</a></li>
			    <li class="tabs"><a href="#InvoiceDetails" data-toggle="tab">Order / Transactions</a></li>
			    <li class="tabs"><a href="#Messaging" data-toggle="tab">Messages</a></li>
		    </ul>
		    <div class="tab-content">
		        <div class="tab-pane active" id="RosterDetails">  
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Class Id :</label>
		                <div class="controls"><span data-bind="text: instance_id"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Student :</label>
		                <div class="controls"><span data-bind="text: name"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Certificate Id :</label>
		                <div class="controls"><input type="text" class="input-small" data-bind="value: certificate_id"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Attendance :</label>
		                <div class="controls"><input type="checkbox" value="1" data-bind="checked: attendance"/></div>
		            </div>
		            <div class="control-group control-group-small">
						<label class="control-label" for="reassessed">Re-Assessed?: </label>
						<div class="controls"><input type="checkbox" name="reassessed" id="reassessed" value='1' data-bind="checked: reassessed"/></div>
					</div>
		            <div class="control-group control-group-small">
						<label class="control-label" for="reassessed_date">Re-Assessed Date: </label>
						<div class="controls"><input type="text" name="reassessed_date" id="reassessed_date" class="input-small" data-bind="datepicker: reassessed_date, datepickerOptions: $root.datepickerOptions"/></div>
					</div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Notes Admin :</label>
		                <div class="controls"><textarea class="input-xxlarge" rows="3" data-bind="value: notes_admin"></textarea></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Notes Class :</label>
		                <div class="controls"><textarea class="input-xxlarge" rows="3" data-bind="value: notes_class"></textarea></div>
		            </div>
		            <div class="control-group control-group-small">
		                <div class="controls pull-right">
							<button class="btn btn-primary" data-bind="click: $root.updateRosterCmd.bind($data, order_id)">
								<i class="icon-white icon-plus-sign"></i> Update Roster</button>
						</div>
		            </div>
                </div>
		        <div class="tab-pane" id="InvoiceDetails">  
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Order Id :</label>
		                <div class="controls"><span data-bind="text: order_id"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Paid :</label>
		                <div class="controls"><span data-bind="text: Paid"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="">Owing :</label>
		                <div class="controls"><span data-bind="text: Owing"/></div>
		            </div>
					<div class="control-group">
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
		            <div class="control-group control-group-small">
		                <div class="controls pull-right">
							<button class="btn btn-primary" data-bind="click: $root.showNewTransactionModalCmd.bind($data, order_id)">
								<i class="icon-white icon-plus-sign"></i> New Transaction</button>
						</div>
		            </div>
                </div>
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
        </div >
	    </form>
    </div>


