<div class="well well-small" style="margin-bottom:0px; @if (!empty($order_type) && strpos($order_type,'Upsell') !== false)  background-color: green; @endif">
	<div class="row-fluid"style="margin-top: 5px;">
		<div class="pull-left">
			<a href="#" data-bind="click: displayChangeBookingForm" class="btn btn-small btn-info" >Change/Pay a Booking</a> | 
			<a href="#" data-bind="click: displayGiftVoucherForm" class="btn btn-small btn-info" >Use a Gift Voucher</a> | 
			<a href="#" data-bind="click: displayAgentForm" class="btn btn-small btn-info" >Select Commission Agent</a> | 
			<a href="#" data-bind="click: displayCompanyForm" class="btn btn-small btn-info" >Select Company</a> | 
			<a href="#" data-bind="click: updateCoursesInstances" class="btn btn-small btn-info" >Reload Current Dates</a> | 
			<input type="text" id="SpecialDate" data-bind="datepicker: specialDate, datepickerOptions: datepickerOptions" class="input-small" placeholder="Special Date"/>
		</div>
		<div class="pull-right">
		<!-- ko if: booking().PurchaseId.Visible() -->
			<a href="#" data-bind="attr: { href : '/backend/booking/newBooking/?OrderId=' + booking().OrderId() + '&OrderType=' + booking().OrderType() + 'Upsell'}" class="btn btn-small btn-success" >Upsell</a>
		<!-- /ko -->
		<a href="#" data-bind="click: clearBooking" class="btn btn-small btn-info" >Clear Booking</a>
		</div>
	</div>
</div>
<div id="cbLogins">   
    <div id="gvcb" class="modal hide">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">x</a>
            <h2>Use a Gift Voucher</h2>
        </div>
        <div class="modal-body form-horizontal table-condensed">
            <div class="control-group">
			    <label class="control-label" for="">Gift Voucher ID:</label>
                <div class="controls">
					@if (count($vouchers) > 0)
					{{ Form::select('voucher_id', $vouchers, '', array('class'=>'input-xlarge voucher', 'data-bind'=>"'value': voucherId")) }}
					@endif
				</div>
		    </div>                
        </div>
	    <div class="modal-footer ">
		    <div class="control-group">
                <button class="btn btn-success" data-bind="click: retrieveGiftVoucher"><i class="icon-white icon-ok"></i>&nbsp;Submit</button>
		    </div>
	    </div> 
    </div>

    <div id="acb" class="modal hide">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">x</a>
            <h3>Select Agent</h3>
        </div>
        <div class="modal-body form-horizontal table-condensed">
			<!-- Tabs -->
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-general" data-toggle="tab">Existing Agents</a></li>
				<li><a href="#tab-new" data-toggle="tab">Create New Agent</a></li>
			</ul>
			<div class="tab-content">
				<!-- General tab -->
				<div class="tab-pane active" id="tab-general">

					<div class="control-group">
						<label class="control-label" for="">Select Agent:</label>
						<div class="controls">
							{{ Form::select('agents', $agents, '', array('class'=>'input-xlarge agent', 'id'=>"agents", 'data-bind'=>"'event': {'change': updateAgentTitle }")) }}            
						</div>
					</div>                
 
					<div class="modal-footer ">
						<div class="control-group">
							<button class="btn btn-success" data-bind="click: retrieveAgent"><i class="icon-white icon-ok"></i> Retrieve Agent</button>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="tab-new" data-bind="with: newAgent">
					<div class="control-group control-group-small">
						<label class="control-label" for="name">Name: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="agent_name" id="agent_name" data-bind="value: name"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="contact_name">Contact Name: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="contact_name" id="contact_name" data-bind="value: contact_name"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="contact_position">Contact Position: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="contact_position" id="contact_position" data-bind="value: contact_position"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="agent_email">Email: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="agent_email" id="agent_email" data-bind="value: email"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="agent_phone">Phone: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="agent_phone" id="agent_phone" data-bind="value: phone"/>
						</div>
					</div>
 
					<div class="modal-footer ">
						<div class="control-group">
							<button class="btn btn-danger" data-bind="click: createNewAgent"><i class="icon-white icon-plus-sign"></i> Add Agent</button>
						</div>
					</div>

				</div>

			</div>

       </div>
    </div>

    <div id="company-modal" class="modal hide">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">x</a>
            <h3>Select Company</h3>
        </div>
        <div class="modal-body form-horizontal table-condensed">
			<!-- Tabs -->
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-existing-companies" data-toggle="tab">Existing Companies</a></li>
				<li><a href="#tab-newcompany" data-toggle="tab">Create New Company</a></li>
			</ul>
			<div class="tab-content">
				<!-- General tab -->
				<div class="tab-pane active" id="tab-existing-companies">

					<div class="control-group">
						<label class="control-label" for="">Select Company:</label>
						<div class="controls">
							{{ Form::select('companies', $companies, '', array('class'=>'company input-xlarge', 'id'=>"companies", 'data-bind'=>"'event': {'change': updateCompanyValue }")) }}
						</div>
					</div>                
 
					<div class="modal-footer ">
						<div class="control-group">
							<button class="btn btn-success" data-bind="click: retrieveCompany"><i class="icon-white icon-ok"></i> Retrieve Company</button>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="tab-newcompany" data-bind="with: newCompany">
					<div class="control-group control-group-small">
						<label class="control-label" for="name">Name: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="agent_name" id="agent_name" data-bind="value: name"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="contact_name">Contact Name: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="contact_name" id="contact_name" data-bind="value: contact_name"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="contact_position">Contact Position: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="contact_position" id="contact_position" data-bind="value: contact_position"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="agent_email">Email: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="agent_email" id="agent_email" data-bind="value: email"/>
						</div>
					</div>
					<div class="control-group control-group-small">
						<label class="control-label" for="agent_phone">Phone: </label>
						<div class="controls">
							<input type="text" class='input-xlarge' name="agent_phone" id="agent_phone" data-bind="value: phone"/>
						</div>
					</div>
 
					<div class="modal-footer ">
						<div class="control-group">
							<button class="btn btn-danger" data-bind="click: createNewCompany"><i class="icon-white icon-plus-sign"></i> Add Company</button>
						</div>
					</div>

				</div>

			</div>

       </div>
    </div>

    <div id="cbcb" class="modal hide">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">x</a>
            <h2>Change/Pay Booking</h2>
        </div>
        <div class="modal-body form-horizontal table-condensed" data-bind="with: booking">
            <div class="control-group">
			    <label class="control-label" for="">Order ID:</label>
			    <div class="controls"><input type="text" value="" data-bind="value: OrderId"/></div>
		    </div>                
        </div>
	    <div class="modal-footer ">
		    <div class="control-group">
                <button class="btn btn-success" data-bind="click: retrieveChangeBooking"><i class="icon-white icon-ok"></i>&nbsp;Submit</button>
		    </div>
	    </div> 
    </div>
     
</div>      
        
<!-- ko if: booking().DisplayVoucher() -->
<div class="alert alert-success" style="margin-bottom:0px; display:none;" data-bind="'visible': booking().DisplayVoucher()">
	<h4 class="info">
		Gift Voucher: <span data-bind="html: booking().Payment().Voucher().id() + ' - ' + booking().Payment().Voucher().message()"></span>
	</h4>
</div>
<!-- /ko -->
       
<!-- ko if: booking().PurchaseId.Visible() -->
<div class="alert alert-default" style="margin-bottom:0px; display:none;" data-bind="'visible': booking().PurchaseId.Visible()">
	<h4 class="info">Order ID: <span data-bind="html: booking().OrderId"></span></h4>
</div>
<!-- /ko -->
@if (!empty($order_type) && strpos($order_type,'Upsell') !== false) 
	<div class="alert alert-default" style="margin-bottom:0px;"><h4 class="text-center">UPSELL</h4></div> 
@endif