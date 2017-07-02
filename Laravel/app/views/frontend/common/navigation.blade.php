<ul class="nav">
@foreach ($pages as $page)
	@if(count($page->children) > 0)
		<li class="dropdown {{ (Request::is("content/$page->route") ? ' active' : '') }}">
			<a href='{{ URL::to("$page->route") }}' data-hover="dropdown" class="dropdown-toggle" title="{{$page->name}}">{{{strtoupper($page->name)}}}<b class="caret"></b></a>
		
			<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
			@foreach ($page->children as $pg)
				<li{{ (Request::is("content/$page->route/$pg->name*") ? ' class="active"' : '') }}>
					<a href='{{ URL::to("$page->route/$pg->name") }}' title="{{$page->name}} {{$pg->name}}">{{{strtoupper($pg->name)}}}</a>
				</li>
			@endforeach
			</ul>
		</li>
	@else
		<li{{ (Request::is("content/$page->route*") ? ' class="active"' : '') }}>
			<a href='{{ URL::to("$page->route") }}' title="{{$page->name}}">{{{strtoupper($page->name)}}}</a>
		</li>
	@endif
@endforeach

<!--	<div class="social">
			<a target="_blank" href="https://www.facebook.com/CoffeeRSAschool"><img style="width:32px;height:32px" src="/assets/img/icons/tw-share-facebook@2x.png" alt=""></a>

		<a href="http://www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FThe-Digital-Dresser%2F258636170817205&width=The+pixel+width+of+the+plugin&height=62&colorscheme=light&show_faces=false&header=true&stream=false&show_border=true"><img src="/images/face.jpg" / ></a> 
	</div> 
-->      
</ul>
@if(isset($data))
<div id="loginContent">
    <div class="nav-collapse" data-bind="visible: user_login().name().length > 0">  
        <ul id="pNavText" class="nav pull-right">
            <li class="dropdown{{ (Request::is('account*') ? ' active' : '') }}">
                <a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="{{ route('account') }}">
                        Logged in as <span data-bind="html: user_login().name"></span>
                        <b class="caret"></b>
                </a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                        <li{{ (Request::is('profile') ? ' class="active"' : '') }}><a href="{{ route('profile') }}"><i class="icon-user"></i> Your profile</a></li>
                        <li class="divider"></li>
                        <li><a href="" data-bind="click: processLogout"><i class="icon-off"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div id="menuLoginDiv" class="nav navbar-nav navbar-right" data-bind="visible: user_login().name().length == 0">
            <div class="dropdown" id="menuLogin">
                    <button class="btn btn-success dropdown-toggle" data-toggle="dropdown" id="navLogin">Agent Login</button>
                    <div class="form-horizontal dropdown-menu" style="width: 300px; padding:17px;">
                        <form class="form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" data-bind="initValue: user_login()._token" />
                                <input type="hidden" name="online" value="true" data-bind="initValue: user_login().online" />
                                <h3>Agent Login</h3> 
                        <input class="form-control" type="text" placeholder="Username"  title="Enter your username" required="" autocomplete="off" data-bind="value: user_login().login">
                        <input class="form-control" type="password" placeholder="Password" title="Enter your password" required="" autocomplete="off" data-bind="value: user_login().password">
                        <br>
                        <button type="button" id="btnLogin" class="btn" data-bind="click: agentLogin">Login</button>
                        </form>
                        <a data-toggle="modal" role="button" href="/auth/forgot-password" id="forgot-link">Forgot password?</a>
                    </div>
            </div>
            <script>
                var login_data = '{{ json_encode( array('id'=>$data->student ? $data->student->id : '', 'name'=>$data->student ? $data->student->name : '', 'login'=>'', 'password'=>'', 'last_used'=>\Session::getMetadataBag()->getLastUsed(), 'lifetime'=>\Config::get('session.lifetime') * 60))}}';
            </script>
    </div>
</div>
@endif