<div class="container-fluid form-horizontal">
	<div class="online-panel-body">
			<div class="form-group">
				<label class="col-sm-2 control-label">First_Name:</label>
				<div class="col-sm-8"><input type="text" class='form-control' name="firstName" data-bind="textInput:: booking().FirstName, event:{'keydown':updateStudentOne}"/></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Last_Name:</label>
				<div class="col-sm-8"><input type="text" class='form-control' name="surname" data-bind="textInput:: booking().LastName, event:{'keyup':updateStudentOne}"/></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">DOB:</label>
				<div class="col-sm-5"><input type="text" class='form-control dob_field' name="dob" data-bind="textInput:: booking().Dob, event:{'keyup':updateStudentOne}"/></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Mobile:</label>
				<div class="col-sm-6"><input type="text" class='form-control' name="mobile" data-bind="textInput:: booking().Mobile, event:{'keyup':updateStudentOne}"/></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Email:</label>
				<div class="col-sm-8"><input type="text" class='form-control' name="email" data-bind="textInput:: booking().Email, event:{'keyup':updateStudentOne}"/></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Password:</label>
				<div class="col-sm-4"><input type="password" class='form-control' name="password" data-bind="textInput:: booking().Password, event:{'keyup':updateStudentOne}" placeholder="Password"/></div>
				<div class="col-sm-4"><input type="password" class='form-control' name="password-confirm" data-bind="textInput:: booking().PasswordConfirm, event:{'keyup':updateStudentOne}"/></div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Lang:</label>
				<div class="col-sm-9">
				<label class="radio-inline"><input type="radio" name="lang_eng_level" value="Very well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Very well</label>
				<label class="radio-inline"><input type="radio" name="lang_eng_level" value="Well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Well</label>
				<label class="radio-inline"><input type="radio" name="lang_eng_level" value="Not Well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Not Well</label>
				</div>
			</div>
	</div>
</div>
