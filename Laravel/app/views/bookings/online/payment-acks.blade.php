	<div class="form-group">
		<label class="col-sm-1"><input type="checkbox" class='check-control' name="handbook" id="handbook" data-bind="checked: handbook"></label>
		<div class="col-sm-11">
            <p class='check-control'>I have read and agree to the <a href="/participant_handbook" target="_blank">Participant Handbook</a></br>
            <span class="validationMessage" data-bind='validationMessage: handbook'/></p>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-1"><input type="checkbox" class='check-control' name="TAC" id="TAC" data-bind="checked: TAC"></label>
		<div class="col-sm-11">
		    <p class='check-control'>I have read and agree to the <a href="/privacy_terms_conditions" target="_blank">Privacy Policy. Terms &amp; Conditions</a></br>
			<span class="validationMessage" data-bind='validationMessage: TAC'/></p>
		</div>
	</div>
	<div class="form-group" data-bind="visible: $root.booking().HasCoffeeCourse()">
		<label class="col-sm-1"><input type="checkbox" class='check-control' name="CoffeeAck" id="CoffeeAck" data-bind="checked: CoffeeAck"></label>
		<div class="col-sm-11">
		    <p class='check-control'>I understand that to satisfy the Professional Barista Course requirements successful<br>
			completion of Food Hygiene certificate SITXOH002A or SITXFSA101 Use hygienic practices for food safety is required prior.</br>
			<span class="validationMessage" data-bind='validationMessage: CoffeeAck'/></p>
		</div>
	</div>
	<div class="form-group" data-bind="visible: $root.booking().HasFssCourseOnly()">
		<label class="col-sm-1"><input type="checkbox" class='check-control' name="FssAck" id="FssAck" data-bind="checked: FssAck"></label>
		<div class="col-sm-11">
		    <p class='check-control'><span data-bind="visible: $root.booking().parentLocation() === undefined || $root.booking().parentLocation() == '1'">
				I understand that to satisfy the NSW Food Authority and obtain my Food Safety Supervisor Certificate, 
				I have already completed my Use Hygenic Practice certificate SITXOH002A or SITXFSA101 <b>with this 
				Registered Training Organisation. (The Coffee School)</b><br>A student cannot complete FSS if they have already 
				completed Use Hygenic Practice certificate SITXOH002A or SITXFSA101 with a different Registered Training Organisation<br /></span>
				<span data-bind="visible: $root.booking().parentLocation() != '1'">I understand that to obtain my Food Safety Supervisor Certificate successful<br>
				completion of Food Hygiene certificate SITXOH002A or SITXFSA101 Use hygienic practices for food safety is required prior. <br /></span></p>
			<span class="validationMessage" data-bind='validationMessage: FssAck'/></br>
		</div>
	</div>
	<div class="form-group">
	    <label class="col-sm-1"><input type="checkbox" class='check-control' name="mail_out" data-bind="checked: mail_out"></label>
		<div class="col-sm-11"><p class='check-control'>Yes, I want to receive other Promotional Offers</p></div>
	</div>
	<div class="form-group">
		<label class="col-sm-12"><br><strong>Please do not make your booking twice. If there is a query, please contact our office (02) 9211 9779</strong></label>
	</div>
