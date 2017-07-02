<header>
    <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="row">
                <a class="brand header-social" href="/"><img src="/images/logo.jpg" /></a>
                <div id="header-social-links" class="pull-right">
                    <a class="header-social" href="http://www.facebook.com/CoffeeRSAschool" title="facebook">
                        <img src="/images/face_46.png" />
                    </a>
                    <a class="header-social" href="http://instagram.com/coffeersaschool" title="instagram">
                        <img src="/images/instagram_46.png" />
                    </a>
                    <a class="header-social" href="#" title="Tell a Friend">
                        <img src="/images/tell_friend_46.png" />
                    </a>
                </div>
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/online">Online Courses</a>
            </div>
            <div class="collapse navbar-collapse">

                <ul class="nav navbar-nav">
       		        @foreach ( $data->routes as $route )
                    <li @if ( $route->name == '' ) 'class="active"' @endif>
                        <a href="/online/{{$route->url}}" title="{{$route->name}}">{{$route->name}}</a>
                    </li>
                    @endforeach
                </ul>
				
				<div id="loginContent">
					<div class="nav-collapse" data-bind="visible: user_login().name().length > 0">  
						<ul id="pNavText" class="nav pull-right">
							<li class="dropdown{{ (Request::is('account*') ? ' active' : '') }}">
								<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="{{ route('account') }}">
									Logged in as <span data-bind="html: user_login().name"></span>
									<b class="caret"></b>
								</a>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									<li{{ (Request::is('online/profile') ? ' class="active"' : '') }}><a href="{{ route('online.profile') }}"><i class="icon-user"></i> Your profile</a></li>
									<li class="divider"></li>
									<li><a href="" data-bind="click: processLogout"><i class="icon-off"></i> Logout</a></li>
								</ul>
							</li>
						</ul>
					</div><!--/.nav-collapse -->
				
					<div id="menuLoginDiv" class="nav navbar-nav navbar-right" data-bind="visible: user_login().name().length == 0">
						<div class="dropdown" id="menuLogin">
							<button class="btn btn-success dropdown-toggle" data-toggle="dropdown" id="navLogin">Login</button>
							<div class="form-horizontal dropdown-menu" style="width: 300px; padding:17px;">
                                                        <form class="form">
								<!-- CSRF Token -->
								<input type="hidden" name="_token" value="{{ csrf_token() }}" data-bind="initValue: user_login()._token" />
								<input type="hidden" name="online" value="true" data-bind="initValue: user_login().online" />
								<label>Login</label> 
    							<input class="form-control" type="text" placeholder="Email"  title="Enter your email" required="" autocomplete="off" data-bind="value: user_login().login">
    							<input class="form-control" type="password" placeholder="Password" title="Enter your password" required="" autocomplete="off" data-bind="value: user_login().password"><br>
    							<button type="button" id="btnLogin" class="btn" data-bind="click: processLogin">Login</button>
    							</form>
								<a data-toggle="modal" role="button" href="/auth/forgot-password">Forgot password?</a>
							</div>
						</div>
						<script>
							var login_data = '{{ json_encode( array('id'=>$data->student ? $data->student->user->id : '', 'name'=>$data->student ? $data->student->user->name : '', 'login'=>'', 'password'=>'', 'last_used'=>\Session::getMetadataBag()->getLastUsed(), 'lifetime'=>\Config::get('session.lifetime') * 60))}}';
						</script>
					</div>
		
				</div>
		
				
            </div>
        </div>
    </div>
		



</header>





