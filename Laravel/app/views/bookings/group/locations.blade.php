<div id="cbLocations" class="well well-small" >
    <div>
		{{ Form::select('locations', $locations, '', array('id'=>'location', 'data-bind'=>"'value': booking().LocationId, 'event': {'change': updateLocation }")) }}	
    </div>
</div>

