
    <div class="modal hide" id="uploadAttachmentsForm-modal">
	<form>
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Upload Attachments</h3>
	    </div>
        <div class="modal-body-payment form-horizontal">
			<div class="control-group"></div>
			<div class="control-group">
				<label class="control-label" for="name">Name: </label>
				<div class="controls">
					<input type="text" class="input-xlarge" name="name" id="name" value="" data-bind="value: uploadData().name"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="attachment">Attachment: </label>
				<div class="controls">
					<input type="file" class="input-xlarge" name="attachment" id="attachment" data-bind="fileUpload: { listId: 'attachments', url: '/api/attachments/upload', uploadButton: 'uploadBtn' }"/>
				</div>
			</div>
			<div class="control-group {{ $errors->has('type') ? 'error' : '' }}">
				<label class="control-label" for="type">Type: </label>
				<div class="controls">
					<select class="input-large" name="type" id="type" data-bind="value: uploadData().type">
						<option value="marketing">Marketing</option>
						<option value="message">Message</option>
					</select>
				</div>
			</div>
			<div class="control-group {{ $errors->has('active') ? 'error' : '' }}">
				<label class="control-label" for="active">Active: </label>
				<div class="controls">
					<input type="checkbox" name="active" id="active" value="1" data-bind="checked: uploadData().active"/>
				</div>
			</div>
		</div >
	    <div class="modal-footer ">
	        <div class="control-group">
				<button type="button" id="uploadBtn" class="btn btn-success"><i class="icon-white icon-thumbs-up"></i> Upload Attachment</button>
	        </div>
	    </div>
	</form>
    </div>
