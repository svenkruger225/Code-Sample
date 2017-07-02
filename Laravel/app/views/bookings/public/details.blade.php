<div id="cbOtherDetails" class="localActivityIndicator">
	<!--<div class="row">
		<div class="offset1 span2">USI:</div>
		<div class="span8"><input type="text" class='input-medium' name="usi" data-bind="textInput: booking().Usi, event:{'keyup':updateStudentOne}"/> 
			<div class="btn-group pull-right">
				<button type="button" title="Click here to verify your USI" class='btn btn-small btn-success' data-bind="click: verifyUsiCmd.bind($data, booking)">Verify</button> 
				<button type="button" title="Click here to Fill the Form to create your USI" class='btn btn-small btn-primary' data-bind="click: displayUsiRegistrationForm">Create</button> 
				<button type="button" title="What is USI" class='btn btn-small btn-info' data-bind="click: displayWhatIsUsiModal">?</button>
			</div>
		</div>
	</div> -->
	<div class="row">
	<div class="offset1 span2">First Name:</div>
	<div class="span8"><input type="text" class='input-xlarge' name="firstName" data-bind="textInput: booking().FirstName, event:{'keydown':updateStudentOne}"/></div>
	</div>
	<div class="row">
	<div class="offset1 span2">Surname:</div>
	<div class="span8"><input type="text" class='input-xlarge' name="surname" data-bind="textInput: booking().LastName, event:{'keyup':updateStudentOne}"/></div>
	</div>
	<div class="row">
	<div class="offset1 span2">DOB:</div>
	<div class="span8"><input type="text" class='input-small dob_field' name="dob" data-bind="textInput: booking().Dob, event:{'change':updateStudentOne}"/></div>
	</div>
	<div class="row">
	<div class="offset1 span2">Mobile:</div>
	<div class="span8"><input type="text" class='input-large' name="mobile" data-bind="textInput: booking().Mobile, event:{'keyup':updateStudentOne}"/></div>
	</div>
	<div class="row">
	<div class="offset1 span2">Email:</div>
	<div class="span8"><input type="text" class='input-large' name="email" data-bind="textInput: booking().Email, event:{'keyup':updateStudentOne}"/></div>
	</div>
	<div class="row"><div class="offset1 span12">How well do you speak English? (Required)</div></div>
	<div class="row">
		<div class="span2 offset2"><input type="radio" name="lang_eng_level2" value="Very well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Very well</div>
		<div class="span2"><input type="radio" name="lang_eng_level2" value="Well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Well</div>
		<div class="span2"><input type="radio" name="lang_eng_level2" value="Not Well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Not Well</div>		
	</div>
	<div class="row"><div class="offset1 span12">Why do you wish to attend this course?</div></div>
	<div class="row"><div class="offset1"><textarea style="width:90%" rows="2" name="q1" id="q1" data-bind="textInput: booking().q1" ></textarea></div></div>
	<div class="row"><div class="offset1 span12">What do you hope to get out of this course?</div></div>
	<div class="row"><div class="offset1"><textarea style="width:90%" rows="2" name="q2" id="q2" data-bind="textInput: booking().q2" ></textarea></div></div>
	<div class="row"><div class="offset1 span12">Do you have any special needs? eg. Hearing impairment or other disability</div></div>
	<div class="row"><div class="offset1"><textarea style="width:90%" rows="2" name="q3" id="q3" data-bind="textInput: booking().q3" ></textarea></div></div>
</div>	
