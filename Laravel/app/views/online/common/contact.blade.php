@extends('online/layouts/default')

{{-- Page title --}}
@section('title')
Your Profile
@stop

{{-- Content --}}
@section('content')
    <div id="content" class="row">
        <div class="col-sm-12">

			<div class="panel panel-default">
				<div class="panel-heading"><h3 class="panel-title">Contact Us </h3></div>
				<div class="panel-body">
					<div class="container-fluid form-horizontal">
						<div class="online-panel-body">

							<form action="/online/contact" class="form-horizontal" method="post">
								<input name="_token" type="hidden" value="{{ csrf_token() }}" />
								<div class="form-group {{ $errors->has('name') ? 'has-error has-feedback' : '' }}">
									<label class="col-sm-2 control-label" for="name">Name: </label>
									<div class="col-sm-5">
									<input class="form-control" id="name" name="name" placeholder="Name" required="" type="text" value="{{Input::old('name')}}"/>
									{{ $errors->first('name', '<span class="text-danger">:message</span>') }}
									</div>
								</div>

								<div class="form-group {{ $errors->has('phone') ? 'has-error has-feedback' : '' }}">
									<label class="col-sm-2 control-label" for="phone">Phone: </label>
									<div class="col-sm-4">
									<input class="form-control" id="phone" name="phone" placeholder="Phone/Mobile" type="text" value="{{Input::old('phone')}}" />
									{{ $errors->first('phone', '<br><span class="text-danger">:message</span>') }}
									</div>
								</div>

								<div class="form-group {{ $errors->has('location') ? 'has-error has-feedback' : '' }}">
									<label class="col-sm-2 control-label" for="location">Location: </label>
									<div class="col-sm-4">
									{{ Form::select('location', $data->locations, Input::old('location'), array('class'=>'form-control')) }}
									{{ $errors->first('location', '<br><span class="text-danger">:message</span>') }}
									</div>
									</div>

								<div class="form-group {{ $errors->has('subject') ? 'has-error has-feedback' : '' }}">
									<label class="col-sm-2 control-label" for="subject">Subject: </label>
									<div class="col-sm-5">
									{{ Form::select('subject', $data->subjects, Input::old('subject'), array('class'=>'form-control')) }}
									{{ $errors->first('subject', '<br><span class="text-danger">:message</span>') }}
									</div>
									</div>

								<div class="form-group {{ $errors->has('email') ? 'has-error has-feedback' : '' }}">
									<label class="col-sm-2 control-label" for="email">Email: </label>
									<div class="col-sm-5">
									<input class="form-control" email="" id="email" name="email" placeholder="Email" required="" type="text" value="{{Input::old('email')}}" />
									{{ $errors->first('email', '<br><span class="text-danger">:message</span>') }}
									</div>
								</div>

								<div class="form-group {{ $errors->has('message') ? 'has-error has-feedback' : '' }}">
									<label class="col-sm-2 control-label" for="message">Message: </label>
									<div class="col-sm-8">
									<textarea class="form-control" id="message" name="message" placeholder="Message" required="" rows="6">{{Input::old('message')}}</textarea>
									{{ $errors->first('message', '<br><span class="text-danger">:message</span>') }}
									</div>
								</div>

								<div class="form-group">
									<div class="col-sm-10 col-sm-offset-2">
									<b>Please enter the value in the image below into the text box.</b>
									</div>
								</div>

								<div class="form-group {{ $errors->has('captcha') ? 'has-error has-feedback' : '' }}">
									<div class="col-sm-3 col-sm-offset-2">
									{{HTML::image(Captcha::img(), 'Captcha image')}} :: <input class="form-control" id="captcha" name="captcha" placeholder="type in the text in image" required="" type="text" />
									{{ $errors->first('captcha', ',<br><span class="text-danger">:message</span>') }}
									</div>
								</div>
								<hr>

								<!-- Form actions -->
								<div class="form-group">
									<label class="col-sm-2 control-label"> </label>
									<div class="col-sm-8">
										<button class="btn btn-warning" type="submit" class="btn">Submit</button>
									</div>
								</div>

							</form>



						</div>
					</div>	
				</div>
			</div>
        </div>
    </div>

@stop