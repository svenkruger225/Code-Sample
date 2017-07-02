    <style>
    .progress
    {
        display: none;
        border: 1px solid #ddd;
        padding: 1px;
        border-radius: 3px;
    }
    .bar
    {
        background-color: #B4F5B4;
        width: 0%;
        height: 20px;
        border-radius: 3px;
    }
    .percent
    {
        position: absolute;
        display: inline-block;
        top: 3px;
        left: 48%;
    }
    .progressError
    {
        display: none;
        color: red;
        font-size: 0.8em;
        font-weight: bold;
        margin: 5px 0;
    }
	.upload_button {
		display: none;
	}
    </style>
	<script src="/assets/js/jquery/jquery.form.js"></script>
    <div class="modal hide modal-payment" id="external-certificate-details" data-bind="with: externalCertificate">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>External Certificate Form</h3>
	    </div>
		<form>
        <div class="modal-body-payment form-horizontal table-condensed">
		    <div id="external-details-form" class="tab-content">
		        <div class="tab-pane active" id="ExternalDetails">
		            <div class="control-group control-group-small">
		                <label class="control-label" for="first_name">Customer :</label>
		                <div class="controls"><span data-bind="html: customer_id"></span>
						<input type="hidden" id="customer_id" name="customer_id" data-bind="value: customer_id"/>
						</div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="last_name">Document Type :</label>
		                <div class="controls">
						<select name="document_type" id="document_type" class="input-medium" data-bind="value: document_type">
							<option value="">Select Type</option>
						@foreach ($types as $key => $type)
							<option value="{{$key}}">{{$type}}</option>
						@endforeach
						</select>
						</div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="email">Description :</label>
		                <div class="controls"><input type="text" name="description" id="description" class="input-xxlarge" data-bind="value: description"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="last_name">Course :</label>
		                <div class="controls">
						<select name="course_id" id="course_id" class="input-xlarge" data-bind="value: course_id">
							<!-- <option value="">Select Course</option> -->
						@foreach ($courses as $key => $course)
							@if ($key == '6')
							<option value="{{$key}}">{{$course}}</option>
							@endif
						@endforeach
						</select>
						</div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="dob">Certificate Date :</label>
						<div class="controls"><input type="text" name="external_date" id="external_date" class="input-medium" data-bind="datepicker: external_date, datepickerOptions: $root.datepickerOptions"/></div>
		            </div>
		            <div class="control-group control-group-small">
		                <label class="control-label" for="email">Document File :</label>
		                <div class="controls">
						<input id="upload" name="upload" type="file" data-bind="fileUpload: { progressId: 'progressBarId', uploadButton: 'upload_button', property: 'document_file', url: '/api/documents/upload' }" /> <button type="button" id='upload_button' class="btn btn-mini btn-success upload_button" >upload</button>
						</div>
						<div id="progressBarId" class="progress span3 offset2"><div class="bar"></div><div class="percent">0%</div></div><div class="progressError"></div>
		            </div>
                </div>
            </div>
        </div >
		</form>
	    <div class="modal-footer ">
		    <div class="control-group control-group-small">
	            <button class="btn btn-warning" data-bind="click: updateExternalDocumentCmd"><i class="icon-white icon-plus-sign"></i> Update External Certificate</button>
	        </div>
	    </div>
    </div>


