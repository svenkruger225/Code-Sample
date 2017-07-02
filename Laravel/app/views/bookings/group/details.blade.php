    
<div class="well well-small" data-bind="template: { name: 'students-template', foreach: booking().Instances }" id="listOfStudents"></div>
<script id="students-template" type="text/html">
	<h6 >Course: <span data-bind="text: $data.courseName() + ' - [' + $data.courseInstance() + ']'"></span></h6>
	<!-- ko foreach: $data.Students -->
	<div id="cbStudentDetails" class="well well-small">
		<!-- <div class="row">
			<div class="offset1 span2">USI:</div>
			<div class="span9"><input type="text" class='input-medium' name="usi" data-bind="value: Usi"/> 
				<div class="btn-group pull-right">
					<button type="button" title="Click here to Fill the Form to create your USI" class='btn btn-medium btn-primary' data-bind="click: $root.displayUsiRegistrationForm">Create</button> 
					<button type="button" title="What is USI" class='btn btn-medium btn-info' data-bind="click: $root.displayWhatIsUsiModal">?</button>
				</div>
			</div>
		</div> -->
		<div class="row">
		<div class="offset1 span2">Name:</div>
		<div class="span9">
				<input type="text" class='input-small' name="firstName" placeholder="First Name" data-bind="value: FirstName"/>&nbsp; 
				Last: <input type="text" class='input-small' name="surname" placeholder="Last Name" data-bind="value: LastName"/>&nbsp;
				<button type="button" class="btn btn-danger btn-mini" data-bind="click: $root.removeStudent.bind($data, $data.courseInstance())"><i class="icon-white icon-remove-sign"></i></button>
		</div></div>
		<div class="row"><div class="offset1 span2">DOB:</div><div class="span9"><input type="text" class='input-small dob_field' name="dob" placeholder="DateOfBirth" data-bind="value: Dob"/>&nbsp; Mob: <input type="text" class='input-small' name="mobile" data-bind="value: Mobile"/></div></div>
		<div class="row"><div class="offset1 span2">Email:</div><div class="span9"><input type="text" class='input-large' name="email" data-bind="value: Email"/></div></div>
	</div>
	<!-- /ko -->
</script>
