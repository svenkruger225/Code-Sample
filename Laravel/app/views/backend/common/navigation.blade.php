	<!-- Navbar -->
	<div style="display:none;">
	<?php $menus = \MenuService::GetMainMenu() ?>
	</div>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
          <a class="brand" href="/" target="_blank">Coffee School</a>
          <div class="container">
			@if (Sentry::check())
				<ul class="nav">
					@if (count($menus) > 0)
						{{ \MenuService::DrawMenu($menus) }}
					@endif
				</ul>
			@endif
			<div class="nav-collapse">  
				<ul class="nav pull-right">
					@if (Sentry::check())
					<li class="dropdown{{ (Request::is('account*') ? ' active' : '') }}">
						<a class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="{{ route('account') }}">
							Logged in as {{ Sentry::getUser()->username }}
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							<li{{ (Request::is('account/profile') ? ' class="active"' : '') }}><a href="{{ route('profile') }}"><i class="icon-user"></i> Your profile</a></li>
							<li class="divider"></li>
							<li><a href="{{ route('logout') }}"><i class="icon-off"></i> Logout</a></li>
						</ul>
					</li>
					@else
					<li {{ (Request::is('auth/signin') ? 'class="active"' : '') }}><a href="{{ route('signin') }}">Sign in</a></li>
					@endif
				</ul>
          </div><!--/.nav-collapse -->
          </div>
      </div>
    </div>


