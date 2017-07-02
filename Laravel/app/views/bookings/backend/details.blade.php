<div id="cbOtherDetails" class="well well-small localActivityIndicator">
	<!--<div class="row">
		<div class="offset1 span3">USI:</div>
		<div class="span8"><input type="text" class='input-small' name="usi" data-bind="textInput: booking().Usi, event:{'keyup':updateStudentOne}"/> 
			<div class="btn-group pull-right">
				<button type="button" title="Click here to verify your USI" class='btn btn-small btn-success' data-bind="click: verifyUsiCmd.bind($data, booking)">Verify</button> 
				<button type="button" title="Click here to Fill the Form to create your USI" class='btn btn-small btn-primary' data-bind="click: displayUsiRegistrationForm">Create</button> 
				<button type="button" title="What is USI" class='btn btn-small btn-info' data-bind="click: displayWhatIsUsiModal">?</button>
			</div>
		</div>
	</div>-->
        @if ($group !='agent')
                <input type="hidden" id="agentData" value='0'/>
                <div class="row"><div class="offset1 span3">First Name:</div><div class="span8"><input type="text" class='input-large' name="firstName" data-bind="textInput: booking().FirstName, event:{'keyup':updateStudentOne}"/></div></div>
                <div class="row"><div class="offset1 span3">Surname:</div><div class="span8"><input type="text" class='input-large' name="surname" data-bind="textInput: booking().LastName, event:{'keyup':updateStudentOne}"/></div></div>
                <div class="row"><div class="offset1 span3">DOB:</div><div class="span8"><input type="text" class='input-small dob_field'  name="dob" data-bind="textInput: booking().Dob, event:{'change':updateStudentOne}"/></div></div>
                <div class="row"><div class="offset1 span3">Mobile:</div><div class="span8"><input type="text" class='input-large' name="mobile" data-bind="textInput: booking().Mobile, event:{'keyup':updateStudentOne}"/></div></div>
                <div class="row"><div class="offset1 span3">Email:</div><div class="span8"><input type="text" class='input-large' name="email" data-bind="textInput: booking().Email, event:{'keyup':updateStudentOne}"/></div></div><br>
            <div class="row" data-bind="'visible': booking().DisplayAgent()"><div class="offset1 span3">Agent:</div><div class="span8"><span data-bind="html: booking().Agent().name"></span></div></div>
            <div class="row" data-bind="'visible': booking().DisplayCompany()"><div class="offset1 span3">Company:</div><div class="span8"><span data-bind="html: booking().Company().name"></span></div></div>
    
        @else
               <input type="hidden" id="agentData" value='{{$agentRecord}}'/>
               <div class="row"><div class="offset1 span3">First Name:</div><div class="span8"><input type="text" class='input-large' name="firstName" data-bind="textInput: booking().FirstName"/></div></div>
                <div class="row"><div class="offset1 span3">Surname:</div><div class="span8"><input type="text" class='input-large' name="surname" data-bind="textInput: booking().LastName"/></div></div>
                <div class="row"><div class="offset1 span3">DOB:</div><div class="span8"><input type="text" class='input-small dob_field'  name="dob" data-bind="textInput: booking().Dob"/></div></div>
                <div class="row"><div class="offset1 span3">Mobile:</div><div class="span8"><input type="text" class='input-large' name="mobile" data-bind="textInput: booking().Mobile"/></div></div>
                <div class="row"><div class="offset1 span3">Email:</div><div class="span8"><input type="text" class='input-large' name="email" data-bind="textInput: booking().Email"/></div></div><br>
            <div class="row" data-bind="'visible': booking().DisplayAgent()"><div class="offset1 span3">Agent:</div><div class="span8"><span data-bind="html: booking().Agent().name"></span></div></div>
            <div class="row" data-bind="'visible': booking().DisplayCompany()"><div class="offset1 span3">Company:</div><div class="span8"><span data-bind="html: booking().Company().name"></span></div></div>
    
        @endif
    @if ($group !='agent')
    <div class="row"><div class="offset1 span11">Why do you wish to attend this course?</div></div>
    <div class="row"><div class="offset1 span11"><input type="text" class='input-xlarge' name="q1" id="q1" data-bind="textInput: booking().q1"/></div></div>
    <div class="row"><div class="offset1 span11">What do you hope to get out of this course?</div></div>
    <div class="row"><div class="offset1 span11"><input type="text" class='input-xlarge' name="q2" id="q2" data-bind="textInput: booking().q2"/></div></div>
    <div class="row"><div class="offset1 span11">Do you have any special needs?</div></div>
    <div class="row"><div class="offset1 span11"><input type="text" name="q3" class='input-xlarge' id="q3" data-bind="textInput: booking().q3"/></div></div><br />
    
    <div class="row"><div class="offset1 span3">Referrer:</div><div class="span8">{{ Form::select('referrer', $referrers, '', array('class'=>'referrer input-xlarge', 'id'=>"referrers", 'data-bind'=>"'textInput': booking().Referrer")) }}</div></div>
	<div class="row"><div class="offset1 span12">How well do you speak English? (Required)</div></div>
	<div class="row">
		<div class="span3 offset2"><input type="radio" name="lang_eng_level" value="Very well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Very well</div>
		<div class="span2"><input type="radio" name="lang_eng_level" value="Well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Well</div>
		<div class="span3"><input type="radio" name="lang_eng_level" value="Not Well" data-bind="checked: booking().lang_eng_level, click:updateStudentOne" /> Not Well</div>
	</div>
    @endif
</div>	

<h4>Students Details</h4>   
@if ($group !='agent')
<div class="well well-small">
	<h5><input type="checkbox" name="repeatStudentsDetails" data-bind="checked: booking().RepeatStudentsDetails, click: $root.updateAllStudentDetailsForAllCourses"/> Use <i>'Student 1'</i> details for all students in all courses</h5>
</div>
@endif
<div data-bind="template: { name: 'students-template', foreach: booking().Instances }" id="listOfStudents"></div>
<script id="students-template" type="text/html">
	<!-- ko foreach: { data: $data.Students, as: 'student' } -->
	<div id="cbStudentDetails" class="localActivityIndicator" data-bind="style: {backgroundColor: $parent.instanceColor()}">		
		<div class="row" style="background-color:#f4f4f4; border:1px solid #F0f0f0; padding:10px; margin: 10px">
			<div class="span11">
				<span data-bind="html: '<b>Student ' + order() + '</b>'"></span> <small data-bind="text: $parent.courseName() + ' (' + $parent.courseDate.Display() + ') - ' + $parent.parentLocationName() "></small>
				@if ($group !='agent')
                                <br /><span><input type="checkbox" id="enableStudentEditing" data-bind="checked: student.EnableEditing"/> Enable editing of this student</span>
                                @endif
			</div>
			<div class="span1">
				<button type="button" class="btn btn-danger btn-mini pull-right" data-bind="click: $root.removeStudent.bind($data, $data.courseInstance())"><i class="icon-white icon-remove-sign"></i></button></span>
			</div>
		</div>
		<div class="row"><div class="offset1 span3">First Name:</div><div class="span8"><input type="text" class='input-large' name="firstName" data-bind="textInput: student.FirstName, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span3">Surname:</div><div class="span8"><input type="text" class='input-large' name="surname" data-bind="textInput: student.LastName, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span3">DOB:</div><div class="span8"><input type="text" class='input-small dob_field' name="dob" data-bind="textInput: student.Dob, event:{'change':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span3">Mobile:</div><div class="span8"><input type="text" class='input-large' name="mobile" data-bind="textInput: student.Mobile, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span3">Email:</div><div class="span8"><input type="text" class='input-large' name="email" data-bind="textInput: student.Email, event:{'keyup':$root.repeatStudentDetailsForAllCourses}"/></div></div>
		<div class="row"><div class="offset1 span3">English level:</div>
			<div class="span2"><input type="radio" value="Very well" data-bind="attr: { name: 'LangLevel' + $data.courseInstance() + $data.order()}, checked: student.LangLevel, click:$root.repeatStudentDetailsForAllCourses" /><label> Very well </label></div>
			<div class="span2"><input type="radio" value="Well" data-bind="attr: { name: 'LangLevel' + $data.courseInstance() + $data.order()}, checked: student.LangLevel, click:$root.repeatStudentDetailsForAllCourses" /><label> Well </label></div>
			<div class="span2"><input type="radio" value="Not Well" data-bind="attr: { name: 'LangLevel' + $data.courseInstance() + $data.order()}, checked: student.LangLevel, click: $root.repeatStudentDetailsForAllCourses" /><label> Not Well </label></div>
		</div>
		<div class="row"><div class="offset1 span11">&nbsp;</div></div>
	</div>
	<!-- /ko -->
	<hr class="alert-info" />
</script>

