<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
	{
		\Log::info(Request::url());
	});


App::after(function($request, $response)
	{
	});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

//Route::filter('auth', function()
//{
//	// Check if the user is logged in
//	if ( ! Sentry::check())
//	{
//		// Store the current uri in the session
//		Session::put('loginRedirect', Request::url());
//
//		if (Request::ajax())
//		{
//			$data = array('type' => 'SignIn', 'Message' => 'must Sign In');
//				return Response::json($data, 403);
//		}
//		
//		
//		// Redirect to the login page
//		return Redirect::route('signin');
//	}
//});
//
//Route::filter('auth.basic', function()
//{
//	return Auth::basic();
//});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| Admin authentication filter.
|--------------------------------------------------------------------------
|
| This filter does the same as the 'auth' filter but it checks if the user
| has 'admin' privileges.
|
*/
Route::filter('force_ssl', function()
	{
		if(App::environment('production') && !Request::secure())
		{
			return Redirect::secure(Request::getRequestUri());
		}
	});

Route::filter('admin-auth', function()
{
	try
	{
		$is_logged_in = Sentry::check();
		\Log::info( 'Sentry::check(): ' . $is_logged_in);
		$is_payway = (stripos(Request::header('referer'), 'www.payway.com.au') !== false) ;

		// Check if the user is logged in
		if ( !$is_logged_in && !$is_payway)
		{
			\Log::info( 'Not loggedin not payway');
			// Store the current uri in the session
			Session::put('loginRedirect', Request::url());

			if (Request::ajax())
			{
				\Log::info( 'ajax call');
				$data = array('type' => 'SignIn', 'Message' => 'must Sign In');
				return Response::json($data, 403);
			}

			// Redirect to the login page
			return Redirect::route('signin');
		}
		$user = Sentry::getUser();
		//if (!$user->isSuperUser())
		if ($user && !$user->hasAnyAccess(array('admin')))
		{
			\Log::info( 'user and not hasAnyAccess admin');

			$currentRoute = \Route::currentRouteName();
			
			$groups = $user->getGroups();
			$allowed = array();
			$home = \Config::get('auth.' . $groups[0]->name . '.home', null);				
			
			foreach($groups as $group)
			{
				$allowed = array_unique (array_merge ($allowed, \Config::get('auth.' . $group->name . '.allowed', array())));
			}
				\Log::info( 'Home : ' .$home . 'count groups : ' .count($groups) . 'count allowed : ' .count($allowed));
				
			if (empty($home) || count($groups) == 0 || count($allowed) == 0)
			{
					//return Response::make(View::make('error/403'), 403);
					return App::abort(403, 'Not Authorised');
			}
		
			if (!in_array($currentRoute, $allowed))
			{
				if (Request::ajax())
				{
					return Response::json(array('success' => false,	'Message' => "Not Authorised"), 500);
				}
				return Redirect::route($home);
			}				
		}
	}
	catch (Exception $ex)
	{
			/*\Log::error($ex);
			throw $ex;*/
			//Redirect::home();
            $user = Sentry::getUser();
            if (Sentry::getUser()->hasAnyAccess(array('admin')))
            {
                return Redirect::home();
            }
            else
            {
                return Redirect::route('online');
            }
	}

});

Route::filter('student-auth', function()
	{
		try
		{
			$is_logged_in = Sentry::check();
			$user = Sentry::getUser();
			\Log::info( 'Online Sentry::check(): ' . $is_logged_in . ' | id: ' . ($user ? $user->id : 'N/A'));
			
			// Check if the user is logged in
			if ( !$is_logged_in )
			{
				\Log::info( 'Not loggedin online');
				// Store the current uri in the session
				Session::put('loginRedirect', Request::url());

				if (Request::ajax())
				{
					\Log::info( 'ajax call');
					$data = array('type' => 'SignIn', 'Message' => 'must Sign In');
					return Response::json($data, 403);
				}

				// Redirect to the login page
				return Redirect::route('signin');
			}
			$has_roster = $user && $user->customer && $user->customer->onlinerosters->count() > 0 ? true : false;

			if ($user && !$user->hasAnyAccess(array('student')))
			{
				return App::abort(403);
			}

			if ( !$has_roster )
			{
				\Log::info( 'User does not have roster online');
				if (Request::ajax())
				{
					\Log::info( 'ajax call');
					$data = array('type' => 'SignIn', 'Message' => 'User signed in but has no roster');
					return Response::json($data, 403);
				}

				// Redirect to the login page
				return Redirect::route('signin')->with('error','User is not enrolled to any online course');
			}

		}
		catch (Exception $ex)
		{
			\Log::error($ex);
			Redirect::home();
		}

	});

Route::filter('super-auth', function()
{
	try
	{
		$user = Sentry::getUser();
		if ($user && !$user->hasAnyAccess(array('superuser')) )
		{
			return View::make('error/403');
		}
	}
		catch (Exception $ex)
	{
		Redirect::home();
	}
	
});
/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


// log all database queries depending on database.log flag

if (Config::get('database.log', false))
{           
	Event::listen('illuminate.query', function($query, $bindings, $time, $name)
		{
			$data = compact('bindings', 'time', 'name');

			// Format binding data for sql insertion
			foreach ($bindings as $i => $binding)
			{   
				if ($binding instanceof \DateTime)
				{   
					$bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
				}
				else if (is_string($binding))
				{   
					$bindings[$i] = "'$binding'";
				}   
			}       

			// Insert bindings into query
			$query = str_replace(array('%', '?'), array('%%', '%s'), $query);
			$query = vsprintf($query, $bindings); 

			\Log::info($query, $data);
		});
}