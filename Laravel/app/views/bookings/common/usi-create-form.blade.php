    <div class="modal hide localActivityIndicator" id="usi-create-form">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Enrolment Data Form</h3>
			<div class="row-fluid">
				<div class="span12">
				<label>You must complete your Enrolment Data. 
					If you already have an USI please enter it below and complete the rest of the Enrolment Data.<br>
					If you do not have an USI, You have the option to visit the <a style="color: black !important" href="http://usi.gov.au/create-your-USI/Pages/default.aspx" target="_blank">USI Registery website to create it</a>. </label>
				</div>
			</div>
			<div id="Floater" class="row-fluid floater-nav">
				<a class="btn pull-left" href="#" data-bind="click: navigatePreviousTab"><span class="float-image img-responsive"> <i class="icon-arrow-left icon-white"></i> Prev</span></a>
				<a class="btn pull-right" href="#" data-bind="click: navigateNextTab"><span class="float-image img-responsive"> Next <i class="icon-arrow-right icon-white"></i></span></a>
			</div>
	    </div>
		<div class="localActivityIndicator">
		@include('bookings/common/usi-create-modal-body')
	    <div class="modal-footer bg-payment">
	        <div class="control-group control-group-small">
				<label class="checkbox pull-left">
				<!-- ko if:usicreate().verify_usi() -->
				<input type="checkbox" data-bind="checked: usicreate_authorised" ></input>
				I request The Coffee School to verify my Unique Student Identifier on my behalf & have read the 
				<a href="/usi_privacy_notice" target="_blank" title="USI privacy notice" style="color: black !important;">Privacy Notice</a>
				<!-- /ko -->
				</label>
				<button class="btn btn-success" data-bind="enable: usicreate_authorised, click: $root.createUsiCmd"><i class="icon-white icon-thumbs-up"></i> 
				<!-- ko if:usicreate().verify_usi() -->
				Submit Enrolment data & Verify USI
				<!-- /ko -->
				<!-- ko ifnot:usicreate().verify_usi() -->
				Submit Enrolment data 
				<!-- /ko -->				
				</button>
				<br><p class="validationMessage pull-left" data-bind='validationMessage: usicreate_authorised'></p>
	        </div>
	    </div>
	    </div>
    </div>
