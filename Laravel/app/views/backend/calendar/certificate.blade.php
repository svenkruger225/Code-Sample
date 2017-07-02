    <div class="modal hide" id="certificate-details">
	    <div class="modal-header alert-info">
		<div class="row-fluid" style="display: inline-block; max-height: 30px">
			<div class="span6">
				<h3 style="display:inline;">Certificate Form</h3>
			</div>
			<div id="search_div" class="span5 localActivityIndicator">
				<div class="input-append pull-right">
					<input class="input-large" type="text" data-bind="value: search_text">
					<button class="btn" type="button" data-bind="click: submitCustomersSearch">Search</button>
				</div>
				@include('bookings/common/usi-create-search-student')				
			</div>
			<div class="span1">
				<a class="close" data-dismiss="modal">x</a>
			</div>
		</div>
			
			<div id="Floater" class="row-fluid floater-nav" data-bind="css: { hide: usicreate().is_course_accredited() == false }">
				<a class="btn pull-left" href="#" data-bind="click: navigatePreviousTab"><span class="float-image img-responsive"> <i class="icon-arrow-left icon-white"></i> Prev</span></a>
				<a class="btn pull-right" href="#" data-bind="click: navigateNextTab"><span class="float-image img-responsive"> Next <i class="icon-arrow-right icon-white"></i></span></a>
			</div>
	    </div>
		<div class="localActivityIndicator">
		@include('bookings/common/usi-create-modal-body')
	    <div class="modal-footer">
		    <div class="control-group control-group-small">
				<!-- <button class="btn btn-small btn-success" data-bind="css: { hide: usicreate().is_course_accredited() == false }, click: $root.createUsiCmd"><i class="icon-white icon-thumbs-up"></i> Submit USI</button> -->
	            <button class="btn btn-small btn-warning" data-bind="click: $root.createNewCustomerCmd"><i class="icon-white icon-plus-sign"></i> Create New Student</button>
	            <button class="btn btn-small btn-primary" data-bind="click: $root.updateCustomerCmd"><i class="icon-white icon-thumbs-up"></i> Update Student</button>
				<div class="input-append" style="vertical-align: top;">
					<input type="text" name="certificate_date" id="certificate_date" class="input-small" data-bind="datepicker: usicreate().certificate_date, datepickerOptions: $root.datepickerOptions"/>
					<button class="btn btn-small btn-primary" data-bind="click: $root.updateCustomerAndDonloadCmd"><i class="icon-white icon-thumbs-up"></i> Upd Student & Certificate</button>
				</div>
	            <button class="btn btn-small btn-primary" data-bind="click: $root.downloadCertificateCmd" id="download_certificate_btn"><i class="icon-white icon-thumbs-up"></i> Download Certificate</button>
	        </div>
	    </div>
	    </div>
    </div>

