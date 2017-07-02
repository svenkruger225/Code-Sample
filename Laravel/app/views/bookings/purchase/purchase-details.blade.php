    
<div id="cbGroupDetails" class="well">
    <div class="row"><div class="offset1 span3">Agent:</div><div class="span8">{{ Form::select('agents', $agents, '', array('class'=>'company input-xlarge', 'id'=>"agents", 'data-bind'=>"'event': { 'change': updateAgentTitle }")) }}</div></div>
    <div class="row"><div class="offset1 span3">Company:</div><div class="span8">{{ Form::select('companies', $companies, '', array('class'=>'company input-xlarge', 'id'=>"companies", 'data-bind'=>"'event': { 'change': updateCompanyValue }")) }}</div></div>
    <div class="row"><div class="offset1 span3">First Name:</div><div class="span8"><input type="text" name="first_name" id="first_name" placeholder="First Name" data-bind="value: purchase().first_name"/></div></div>
	<div class="row"><div class="offset1 span3">Last Name:</div><div class="span8"><input type="text" name="last_name" id="last_name" placeholder="Last Name" data-bind="value: purchase().last_name"/></div></div> 
    <div class="row"><div class="offset1 span3">Phone:</div><div class="span8"><input type="text" name="phone" id="phone" data-bind="value: purchase().phone"/></div></div> 
    <div class="row"><div class="offset1 span3">Mobile:</div><div class="span8"><input type="text" name="mobile" id="mobile" data-bind="value: purchase().mobile"/></div></div> 
    <div class="row"><div class="offset1 span3">Fax:</div><div class="span8"><input type="text" name="fax" id="fax" data-bind="value: purchase().fax"/></div></div> 
    <div class="row"><div class="offset1 span3">Email:</div><div class="span8"><input type="text" name="email" id="email" data-bind="value: purchase().email"/></div></div> 

	<div class="row"><div class="offset1 span3">Building Name: </div><div class="span8"><input type="text" id="address_building_name" name="address_building_name" class="input-large" data-bind="value: purchase().address_building_name" /></div></div>
	<div class="row"><div class="offset1 span3">Unit Number: </div><div class="span8"><input type="text" id="address_unit_details" name="address_unit_details" class="input-large" data-bind="value: purchase().address_unit_details" /></div></div>
	<div class="row"><div class="offset1 span3">Street Number: </div><div class="span8"><input type="text" id="address_street_number" name="address_street_number" class="input-large" data-bind="value: purchase().address_street_number" /></div></div>
	<div class="row"><div class="offset1 span3">Street Name: </div><div class="span8"><input type="text" id="address_street_name" name="address_street_name" class="input-large" data-bind="value: purchase().address_street_name" /></div></div>
    
    <div class="row"><div class="offset1 span3">City:</div><div class="span8"><input type="text" name="city" id="city" data-bind="value: purchase().city"/></div></div> 
    <div class="row"><div class="offset1 span3">State:</div><div class="span8"><input type="text" name="state" id="state" data-bind="value: purchase().state"/></div></div> 
    <div class="row"><div class="offset1 span3">Post Code:</div><div class="span8"><input type="text" name="post_code" id="post_code" data-bind="value: purchase().post_code"/></div></div> 
    <div class="row"><div class="offset1 span3">Description:</div><div class="span8"><input type="text" name="description" id="description" data-bind="value: purchase().description"/></div></div> 
</div>	