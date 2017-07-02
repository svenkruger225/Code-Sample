<div id="cbLocations" class="well well-small" >
    <div>
    @foreach ( $locations as $location )
        <label>
            <input type="radio" id="locationRadio{{$location->id}}" name="locationRadio" class="locationRadio" value="{{$location->id}}" data-bind="'checked': booking().parentLocation, 'click': updateCoursesInstances" />
            <span>{{$location->name}}</span>
        </label> 
	@endforeach
    </div>
</div>
