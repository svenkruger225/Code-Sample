    <div class="modal hide" id="send-page-to-friend-modal">
	    <div class="modal-header alert-info">
		    <a class="close" data-dismiss="modal">x</a>
		    <h3>Send this page to a friend</h3>
	    </div>
        <div class="modal-body form-horizontal" style="background-color: #fff !important;">
			<div class="control-group"></div>
		    <div class="control-group"> 
		        <label class="control-label" for="your_name">Your Name :</label>
		        <div class="controls">
					<input type="text" name="your_name" id="your_name" value="" data-bind="value: msgData().your_name"/>
				</div>
		    </div>
		    <div class="control-group"> 
		        <label class="control-label" for="friend_name">Friend's Name :</label>
		        <div class="controls">
					<input type="text" name="friend_name" id="friend_name" value="" data-bind="value: msgData().friend_name"/>
				</div>
		    </div>
		    <div class="control-group"> 
		        <label class="control-label" for="friend_email">Friend's Email : *</label>
		        <div class="controls">
					<input type="text" name="friend_email" id="friend_email" value="" data-bind="value: msgData().friend_email"/>
				</div>
		    </div>
		</div >
	    <div class="modal-footer ">
	        <div class="control-group">
				<button class="btn btn-success" data-bind="click: submitEmail"><i class="icon-white icon-thumbs-up"></i> Submit</button>
	        </div>
	    </div>
    </div>
