<div class="span3 transparent_span">
<form action="/enrolment/form" class="form-horizontal" method="post">
<input name="_token" type="hidden" value="{{ csrf_token() }}" />
<div style="color:#000000">

<div class="control-group {{ $errors->has('phone') ? 'error' : '' }}">
	<label for="phone">Order Id: </label>
	<input class="input-medium" id="order_id" name="order_id" placeholder="Order Id" type="text" value="{{Input::old('order_id')}}" />
	<button class="btn btn-warning pull-right" type="submit">Submit</button>
	{{ $errors->first('order_id', '<br><span class="help-inline">:message</span>') }}
</div>


</div>
</form>
</div>
