<form action="/contact/form" class="form-horizontal" method="post">
<input name="_token" type="hidden" value="{{ csrf_token() }}" />
<div style="color:#000000">
<div class="control-group {{ $errors->has('name') ? 'error' : '' }}">
	<label for="name">Name: </label>
	<input class="input-xlarge" id="name" name="name" placeholder="Name" required="" type="text" value="{{Input::old('name')}}"/>
	{{ $errors->first('name', '<span class="help-inline">:message</span>') }}
</div>

<div class="control-group {{ $errors->has('phone') ? 'error' : '' }}">
	<label for="phone">Phone: </label>
	<input class="input-xlarge" id="phone" name="phone" placeholder="Phone/Mobile" type="text" value="{{Input::old('phone')}}" />
	{{ $errors->first('phone', '<br><span class="help-inline">:message</span>') }}
</div>

<div class="control-group {{ $errors->has('location') ? 'error' : '' }}">
	<label for="location">Location: </label>
	{{ Form::select('location', $locations, Input::old('location'), array('class'=>'input-xlarge')) }}
	{{ $errors->first('location', '<br><span class="help-inline">:message</span>') }}
	</div>

<div class="control-group {{ $errors->has('subject') ? 'error' : '' }}">
	<label for="subject">Subject: </label>
	{{ Form::select('subject', $subjects, Input::old('subject'), array('class'=>'input-xlarge')) }}
	{{ $errors->first('subject', '<br><span class="help-inline">:message</span>') }}
	</div>

<div class="control-group {{ $errors->has('email') ? 'error' : '' }}">
	<label for="email">Email: </label>
	<input class="input-xlarge" email="" id="email" name="email" placeholder="Email" required="" type="text" value="{{Input::old('email')}}" />
	{{ $errors->first('email', '<br><span class="help-inline">:message</span>') }}
</div>

<div class="control-group {{ $errors->has('message') ? 'error' : '' }}">
	<label for="message">Message: </label>
	<textarea class="input-xlarge" id="message" name="message" placeholder="Message" required="" rows="6">{{Input::old('message')}}</textarea>
	{{ $errors->first('message', '<br><span class="help-inline">:message</span>') }}
</div>

<div class="control-group">
	Please enter the value in the image below into the text box.
</div>

<div class="control-group {{ $errors->has('captcha') ? 'error' : '' }}">
	{{HTML::image(Captcha::img(), 'Captcha image')}} :: <input class="input-medium" id="captcha" name="captcha" placeholder="type in the text in image" required="" type="text" />
	{{ $errors->first('captcha', ',<br><span class="help-inline">:message</span>') }}
</div>

<button class="btn btn-warning pull-right" type="submit">Submit</button>
</div>
</form>
