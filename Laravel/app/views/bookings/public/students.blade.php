<div class="alert alert-info">
	<h5><input type="checkbox" name="repeatStudentsDetails" data-bind="checked: booking().RepeatStudentsDetails, click: $root.updateAllStudentDetailsForAllCourses"/> Use <i>'Student 1'</i> details for all students in all courses</h5>
</div>

<div class="studentsDetails" data-bind="template: { name: 'students-template', foreach: booking().Instances }" id="listOfStudents"></div>
<script id="students-template" type="text/html">
	<!-- ko foreach: { data: $data.Students, as: 'student' } -->
	<div id="cbStudentDetails" class="localActivityIndicator" data-bind="style: {backgroundColor: $parent.instanceColor()}">
		<div class="row" style="background-color:#f4f4f4; border:1px solid #F0f0f0; padding:10px; margin: 10px">
			<div class="span11">
				<span data-bind="html: '<b>Student ' + order() + '</b>'"></span> <span data-bind="text: $parent.courseName() + ' (' + $parent.courseDate.Display() + ') - ' + $parent.parentLocationName() "></span>
				<br /><span><input type="checkbox" id="enableStudentEditing" data-bind="checked: student.EnableEditing"/> Enable editing of this student</span>
			</div>
			<div class="span1">
				<button type="button" class="btn btn-danger btn-mini pull-right" data-bind="click: $root.removeStudent.bind($data, $parent.courseInstance())"><i class="icon-white icon-remove-sign"></i></button></span>
			</div>
		</div>
		
		<!-- <div class="row">
			<div class="offset1 span2">USI:</div>
			<div class="span8"><input type="text" class='input-medium' name="usi" data-bind="textInput: student.Usi, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/> 
				<div class="btn-group pull-right">
					<button type="button" title="Click here to verify your USI" class='btn btn-small btn-success' data-bind="click: $root.verifyUsiCmd.bind($data, student)">Verify</button> 
					<button type="button" title="Click here to Fill the Form to create your USI" class='btn btn-small btn-primary' data-bind="click: $root.displayUsiRegistrationForm">Create</button> 
					<button type="button" title="What is USI" class='btn btn-small btn-info' data-bind="click: $root.displayWhatIsUsiModal">?</button>
				</div>
			</div>
		</div> -->
		<div class="row"><div class="offset1 span2">First Name:</div><div class="span8"><input type="text" class='input-large' name="firstName" data-bind="textInput: student.FirstName, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span2">Surname:</div><div class="span8"><input type="text" class='input-large' name="surname" data-bind="textInput: student.LastName, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span2">DOB:</div><div class="span8"><input type="text" class='input-small dob_field' name="dob" data-bind="textInput: student.Dob, event:{'change':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span2">Mobile:</div><div class="span8"><input type="text" class='input-large' name="mobile" data-bind="textInput: student.Mobile, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span2">Email:</div><div class="span8"><input type="text" class='input-large' name="email" data-bind="textInput: student.Email, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span2">English level:</div>
			<div class="span2"><input type="radio" value="Very well" data-bind="attr: { name: 'LangLevel' + $data.courseInstance() + $data.order()}, checked: student.LangLevel, click:$root.repeatStudentDetailsForAllCourses" /> Very well</div>
			<div class="span2"><input type="radio" value="Well" data-bind="attr: { name: 'LangLevel' + $data.courseInstance() + $data.order()}, checked: student.LangLevel, click:$root.repeatStudentDetailsForAllCourses" /> Well</div>
			<div class="span2"><input type="radio" value="Not Well" data-bind="attr: { name: 'LangLevel' + $data.courseInstance() + $data.order()}, checked: student.LangLevel, click: $root.repeatStudentDetailsForAllCourses" /> Not Well</div>
		</div>

	</div>
	<!-- /ko -->
	<hr class="alert-info" />
</script>        
