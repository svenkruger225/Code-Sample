<div id="cbLocations" class="well" >
    <div>
		{{ Form::select('locations', $locations, '', array('data-bind'=>"'value': purchase().LocationId")) }}	
    </div>
</div>

