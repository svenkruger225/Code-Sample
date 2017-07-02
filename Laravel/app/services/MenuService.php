<?php namespace App\Services;

use Log, Config, Input, Lang, Redirect, Sentry, Validator, View, Response, Exception, URL;

class MenuService {

	public function __construct()
	{
	}
	
	public function GetMainMenu()
	{
		$menus = array();
		if (Sentry::check())
		{
			if (Sentry::getUser()->hasAnyAccess(array('admin')))
			{
				$menus += Config::get('menu.superuser', array());
			}
			else
			{
				if (Sentry::getUser()->hasAnyAccess(array('trainer')))
				{
					$menus += Config::get('menu.trainer', array());
				}
				if (Sentry::getUser()->hasAnyAccess(array('agent')))
				{
					$menus += Config::get('menu.agent', array());
				}
			}
		}
		
		return $menus;
		
	}
	

	public function DrawMenu($menus, $level = 0) 
	{
		$html = "<!-- MAIN -->";
		
		foreach ($menus as $key => $menu) {
			if (count($menu['children']) > 0 && $level == 0)
			{
				$html .=  "<li class='dropdown " . (\Utils::IsGroupActive($key) ? 'active' : '') . "'>";
				$html .=  "<a data-hover='dropdown' class='dropdown-toggle' href='" . URL::to($key) . "'>";
				$html .=  "<i class='icon-fixed-width " . $menu['icon'] . "'></i> ". $menu['name'] . " <span class='caret'></span></a>";
			}
			else
			{
				$html .=  "<li class='" . (count($menu['children']) > 0 && $level == 1 ? 'dropdown-submenu ' : '')  . (\Utils::IsGroupActive($key) ? 'active' : '') . "'>";
				$html .=  "<a href='" . URL::to($key) . "'>";
				$html .=  "<i class='" . $menu['icon'] . "'></i> ". $menu['name'] . "</a>";
			}
			
			if (count($menu['children']) > 0) {
				$html .= "<!-- CHILDREN -->";
				$html .= "<ul class='dropdown-menu'>";
				$html .= $this->DrawMenu($menu['children'], 1); 
				$html .= "</ul>";
			}
			$html .= "</li>";
		}
		//Log::info($html);
		return $html;
		
		
	}	


//        <div class="dropdown">
//            <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-primary" data-target="#" href="/page.html">
//                Dropdown <span class="caret"></span>
//            </a>
//    		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
//              <li><a href="#">Some action</a></li>
//              <li><a href="#">Some other action</a></li>
//              <li class="divider"></li>
//              <li class="dropdown-submenu">
//                <a tabindex="-1" href="#">Hover me for more options</a>
//                <ul class="dropdown-menu">
//                  <li><a tabindex="-1" href="#">Second level</a></li>
//                  <li class="dropdown-submenu">
//                    <a href="#">Even More..</a>
//                    <ul class="dropdown-menu">
//                        <li><a href="#">3rd level</a></li>
//                    	<li><a href="#">3rd level</a></li>
//                    </ul>
//                  </li>
//                  <li><a href="#">Second level</a></li>
//                  <li><a href="#">Second level</a></li>
//                </ul>
//              </li>
//            </ul>
//        </div>





//					<ul class="nav">
//					@if (count($menus) > 0)
//						@foreach ($menus as $key => $menu)
//						<li class="dropdown {{ (\Utils::IsGroupActive("$key") ? ' active' : '') }}">
//							<a data-hover="dropdown" class="dropdown-toggle" href="{{ URL::to("$key") }}"><i class="icon-fixed-width {{$menu['icon']}}"></i> {{$menu['name']}} <span class="caret"></span></a>
//							
//							@if (count($menu['children']) > 0)
//								<ul class="dropdown-menu">
//								@foreach ($menu['children'] as $child_key => $child)
//									<li{{ (Request::is("$child_key*") ? ' class="active"' : '') }}>
//										<a href="{{ URL::to("$child_key") }}"><i class="{{$child['icon']}}"></i> {{$child['name']}}</a>
//									</li>
//								@endforeach
//								</ul>
//							@endif
//						</li>
//						@endforeach
//					@endif
//				</ul>
//

}