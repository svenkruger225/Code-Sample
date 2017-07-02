    
<div id="cbGroupDetails" class="well well-small">
    <div class="row"><div class="offset1 span3">Group Name:</div><div class="span8"><input type="text" name="group_name" id="group_name" data-bind="value: booking().GroupName"/></div></div> 
    <div class="row"><div class="offset1 span3">Agent:</div><div class="span8">{{ Form::select('agents', $agents, '', array('class'=>'company input-xlarge', 'id'=>"agents", 'data-bind'=>"'event': { 'change': updateAgentTitle }")) }}</div></div>
    <div class="row"><div class="offset1 span3">Company:</div><div class="span8">{{ Form::select('companies', $companies, '', array('class'=>'company input-xlarge', 'id'=>"companies", 'data-bind'=>"'event': { 'change': updateCompanyValue }")) }}</div></div>
    <div class="row"><div class="offset1 span3">Contact Name:</div><div class="span8"><input type="text" name="first_name" id="first_name" placeholder="First Name" class="span5" data-bind="value: booking().FirstName"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="last_name" id="last_name" placeholder="Last Name" class="span5" data-bind="value: booking().LastName"/></div></div> 
    <div class="row"><div class="offset1 span3">Phone:</div><div class="span8"><input type="text" name="phone" id="phone" data-bind="value: booking().Phone"/></div></div> 
    <div class="row"><div class="offset1 span3">Mobile:</div><div class="span8"><input type="text" name="mobile" id="mobile" data-bind="value: booking().Mobile, 'event': {'change': updateGroupMobile }"/></div></div> 
    <div class="row"><div class="offset1 span3">Fax:</div><div class="span8"><input type="text" name="fax" id="fax" data-bind="value: booking().Fax"/></div></div> 
    <div class="row"><div class="offset1 span3">Email:</div><div class="span8"><input type="text" name="email" id="email" data-bind="value: booking().Email, 'event': {'change': updateGroupEmail }"/></div></div> 
    <div class="row"><div class="offset1 span3">Notes:</div><div class="span8"><input type="text" name="notes" id="notes" data-bind="value: booking().Notes"/></div></div> 
    <div class="row"><div class="offset1 span3">Description:</div><div class="span8"><input type="text" name="description" id="description" data-bind="value: booking().Description"/></div></div> 
</div>	